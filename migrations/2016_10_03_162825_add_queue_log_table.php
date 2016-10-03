<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQueueLogTable extends Migration
{
    public function up()
    {
        Schema::create('queue_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_class');
            $table->string('rel_class');
            $table->integer('rel_id');
            $table->text('data');
            $table->timestamp('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('queue_log');
    }
}
