<?php

namespace Finller\LaravelMedia\Casts;

use Finller\LaravelMedia\Enums\GeneratedConversionState;
use Finller\LaravelMedia\Enums\MediaType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @property null|Collection<string, GeneratedConversion> $conversions
 */
class GeneratedConversion implements Arrayable
{
    public function __construct(
        public GeneratedConversionState $state = GeneratedConversionState::Pending,
        public string $file_name,
        public ?string $path = null,
        public MediaType $type = MediaType::Other,
        public ?string $mime_type = null,
        public ?string $extension = null,
        public ?int $size = null,
        public ?int $height = null,
        public ?int $width = null,
        public ?string $aspect_ratio = null,
        public ?string $average_color = null,
        public Collection $conversions = new Collection()
    ) {
    }

    public static function make(array $attributes): self
    {
        return new self(
            state: GeneratedConversionState::from(Arr::get($attributes, 'state')),
            file_name: Arr::get($attributes, 'file_name'),
            path: Arr::get($attributes, 'path'),
            type: ($type = Arr::get($attributes, 'type')) ? MediaType::from($type) : MediaType::Other,
            mime_type: Arr::get($attributes, 'mime_type'),
            extension: Arr::get($attributes, 'extension'),
            size: Arr::get($attributes, 'size'),
            height: Arr::get($attributes, 'height'),
            width: Arr::get($attributes, 'width'),
            aspect_ratio: Arr::get($attributes, 'aspect_ratio'),
            average_color: Arr::get($attributes, 'average_color'),
            conversions: collect(Arr::get($attributes, 'conversions', []))->map(fn ($item) => self::make($item)),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
