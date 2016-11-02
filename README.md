## Note
Looks like the following PL implements changes better then this package provides. The latest versions of 5.3 (not sure the exact version this is implemented on) will cover what this package tries to help with.
https://github.com/laravel/framework/pull/16212

## Queue Validator for Laravel 5.3

Having issues with queue's in laravel 5.3? This package is made to help mitigate any damage from lost jobs.

### How it works

This package will add a table that will send any data from jobs that are sent to the queue. This is configurable per job class since proper verification might change from class to class. More on this later.

Once a queue is finished it will remove the record from the database. This might seem like its just using the database driver, but its more of a log. If the record is added but never removed (even after running failed jobs) it can be assumed that something went wrong.

Since this can effectively back up all data related to the queue, it can be used in a similar fashion to the failed jobs. However the reason for each job being configurable is that sometimes there is an error in the job class itself. Meaning that even if the queue tries to run it, it cannot even initialize it. This allows the raw data that was used to generate the job to be used, rather then a serialized version.

### Installation

First Add the service provider to your apps config.

```
Ramstad\QueueValidator\QueueValidatorServiceProvider::class
```

Then for each Job you want to validate you need to add an extra trait.

```
use Ramstad\QueueValidator\ValidatesQueue;
```

```
class SomeJob implements ShouldQueue
{
    use ValidatesQueue;
}
```

This uses the jobs `queue()` function so if you are using that for any other reason this may not work for you.

You will also need some extra fields added to your job in its initialization.
    
```
class SomeJob implements ShouldQueue
{
    use ValidatesQueue;
    
    protected $related_class;
    protected $related_id;
    protected $related_data;
    
    public function __construct(SomeModelClass $model, $fields)
    {
        $this->related_class = SomeModelClass::class;
        $this->related_id = $model->id;
        $this->related_data = [
            'fields' => $fields
        ];
    }
}
```

### Usage

Right now there is only one method to check the queue log table 

```
php artisan queue:check
```

What this will do is output a list of jobs that have been lost, because the jobs could be still processing it check that they are older then 5 minutes by default.

To adjust the time just add a number to the end.

```
php artisan queue:check 10
```

Will check for any queue's older then 10 minutes that have not been processed.

### Todo

* Add command to retry lost queues (different then failed job's, it completely redispatches the job rather then use the serialized job class).
* Create lost job notifications.
* Add extra functions to jobs to allow full verification of the job. Rather then just check that the queue ran it can also check to ensure it did what it needed too (If it creates a file, then check that it exists and such).

### Notes

This is far from a perfect solution, there is likely better ways to help with queue issues and hopefully at some point this package becomes pointless. Removing the package or leaving it should not have any problematic effects on your application though.
