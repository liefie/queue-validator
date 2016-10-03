<?php

namespace Ramstad\QueueValidator;

use Event;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\ServiceProvider;

class QueueValidatorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../migrations');

        //queue completed, we no longer need the record
        Event::listen(JobProcessed::class, function (JobProcessed $event) {
            $payload = $event->job->payload();
            $job = unserialize($payload['data']['command']);
            if (method_exists($job, 'validateJob')) {
                $job->validateJob();
            }
        });

        //if the failing event is run then there is no need to keep the log, as it can be rerun via the failed_jobs command.
        Event::listen(JobFailed::class, function (JobFailed $event) {
            $payload = $event->job->payload();
            $job = unserialize($payload['data']['command']);
            if (method_exists($job, 'validateJob')) {
                $job->validateJob();
            }
        });
    }

    public function register()
    {
        $this->commands([
            CheckQueue::class
        ]);
    }
}