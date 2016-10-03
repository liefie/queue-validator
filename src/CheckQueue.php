<?php

namespace Ramstad\QueueValidator;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class CheckQueue extends Command
{
    protected $signature = 'queue:check {time=5}';
    protected $description = 'Check the queue for any lost jobs';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $time = $this->argument('time');
        $logs = DB::table('queue_log')
            ->where('created_at', '<', Carbon::now()->subMinutes($time)->toDateTimeString())
            ->get();

        foreach ($logs as $log) {
            $this->info('Job Lost - ' . $log->job_class . ' (' . $log->rel_class . ' ID: ' . $log->rel_id . ')');
        }
    }
}
