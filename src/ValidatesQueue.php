<?php

namespace Ramstad\QueueValidator;

use Carbon\Carbon;
use DB;

trait ValidatesQueue
{
    public function queue($queue, $command)
    {
        if (isset($this->related_class) && isset($this->related_id) && isset($this->related_data)) {
            DB::table('queue_log')->insert([
                'job_class' => self::class,
                'rel_class' => $this->related_class,
                'rel_id' => $this->related_id,
                'data' => json_encode($this->related_data),
                'created_at' => Carbon::now()->toDateTimeString()
            ]);
        }

        if (isset($command->queue, $command->delay)) {
            return $queue->laterOn($command->queue, $command->delay, $command);
        }

        if (isset($command->queue)) {
            return $queue->pushOn($command->queue, $command);
        }

        if (isset($command->delay)) {
            return $queue->later($command->delay, $command);
        }

        return $queue->push($command);
    }

    public function validateJob()
    {
        if (isset($this->related_class) && isset($this->related_id) && isset($this->related_data)) {
            DB::table('queue_log')
                ->where('job_class', '=', self::class)
                ->where('rel_class', '=', $this->related_class)
                ->where('rel_id', '=', $this->related_id)
                ->delete();
        }
    }
}