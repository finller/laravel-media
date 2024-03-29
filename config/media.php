<?php

// config for Finller/Media

use Finller\Media\Jobs\DeleteModelMediaJob;
use Finller\Media\Models\Media;

return [
    /**
     * The media model
     */
    'model' => Media::class,

    /**
     * The default disk used to store files
     */
    'disk' => env('MEDIA_DISK', env('FILESYSTEM_DISK', 'local')),

    /**
     * Control if media should be deleted with the model
     * when using the HasMedia Trait
     */
    'delete_media_with_model' => true,

    /**
     * Control if media should be deleted with the model
     * when soft deleted
     */
    'delete_media_with_trashed_model' => false,

    /**
     * Deleting a lot of media related to a model can take some time
     * or even fail (cloud api error, permissions, ...)
     * For performance and monitoring, when a model with HasMedia trait is deleted,
     * each media is individually deleted inside a job.
     */
    'delete_media_with_model_job' => DeleteModelMediaJob::class,

    /**
     * The default collection name
     */
    'default_collection_name' => 'default',

    /**
     * Prefix the generate path of files
     * set to null if you don't want any prefix
     * To fully customize the generated default path, extends the Media model ans override generateBasePath method
     */
    'generated_path_prefix' => null,

    /**
     * Customize the queue connection used when dispatching conversion jobs
     */
    'queue_connection' => env('QUEUE_CONNECTION', 'sync'),

    /**
     * Customize the queue used when dispatching conversion jobs
     * null will fallback to the default laravel queue
     */
    'queue' => null,

    /**
     * Customize WithoutOverlapping middleware settings
     */
    'queue_overlapping' => [
        /**
         * Release value must be longer than the longest conversion job that might run
         * Default is: 1 minute, increase it if you jobs are longer
         */
        'release_after' => 60,
        /**
         * Expire value allow to forget a lock in case of the job failed in a unexpected way
         *
         * @see https://laravel.com/docs/10.x/queues#preventing-job-overlaps
         */
        'expire_after' => 60 * 60,
    ],

];
