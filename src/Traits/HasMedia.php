<?php

namespace Finller\LaravelMedia\Traits;

use Finller\LaravelMedia\Jobs\ConversionJob;
use Finller\LaravelMedia\Media;
use Finller\LaravelMedia\MediaCollection;
use Finller\LaravelMedia\MediaConversion;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

/**
 * @property ?string $uuid
 */
trait HasMedia
{
    public function media(): MorphMany
    {
        return $this->morphMany(config('media.model'), 'model');
    }

    /**
     * @return EloquentCollection<int, Media>
     */
    public function getMedia(string $collection_name = null): EloquentCollection
    {
        return $this->media->where('collection_name', $collection_name);
    }

    /**
     * @return Arrayable<MediaCollection>|iterable<MediaCollection>|null
     */
    protected function registerMediaCollections(): Arrayable|iterable|null
    {
        return collect();
    }

    /**
     * @return Arrayable<MediaConversion>|iterable<MediaConversion>|null
     */
    protected function registerMediaConversions(Media $media): Arrayable|iterable|null
    {
        return collect();
    }

    /**
     * @return Collection<string, MediaCollection>
     */
    public function getMediaCollections(): Collection
    {
        return collect($this->registerMediaCollections())
            ->push(new MediaCollection(
                name: config('media.default_collection_name'),
                single: false,
                public: false
            ))
            ->keyBy('name');
    }

    /**
     * @return Collection<string, MediaConversion>
     */
    public function getMediaConversions(Media $media): Collection
    {
        return collect($this->registerMediaConversions($media))->keyBy('name');
    }

    function hasMediaCollection(string $collection_name): bool
    {
        return $this->getMediaCollections()->has($collection_name);
    }

    function clearMediaCollection(string $collection_name): static
    {
        $this->getMedia($collection_name)->each(function (Media $media) {
            $media->delete();
        });

        return $this;
    }

    public function saveMedia(string|UploadedFile $file, string $collection_name = null, string $name = null, string $disk = null): Media
    {
        $collection_name ??= config('media.default_collection_name');

        $collection = $this->getMediaCollections()->get($collection_name);

        if (!$collection) {
            $class = static::class;
            throw new Exception("The media collection {$collection_name} is not registered for {$class}");
        }

        if ($collection->single) {
            $this->clearMediaCollection($collection_name);
        }

        $media = new Media();

        $media->model()->associate($this);

        $media->storeFile(
            file: $file,
            collection_name: $collection_name,
            name: $name,
            disk: $disk
        );

        $this->dispatchConversions($media, $collection_name);

        return $media;
    }

    public function dispatchConversions(Media $media): static
    {
        $conversions = $this->getMediaConversions($media);

        if ($conversions->isEmpty()) {
            return $this;
        }

        foreach ($conversions as $name => $conversion) {
            if ($conversion->job instanceof ConversionJob) {
                dispatch($conversion->job);
            } else {
                $job = $conversion->job;
                dispatch(new $job($media, $name));
            }
        }

        return $this;
    }
}
