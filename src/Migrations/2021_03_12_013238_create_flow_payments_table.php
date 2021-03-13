<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlowPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('flow.table_name'), function (Blueprint $table) {
            $table->id();
            $table->morphs('modelable');
            $table->bigInteger('flow_order');
            $table->uuid('commerce_order')->unique();
            $table->date('request_date')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('subject')->nullable();
            $table->string('currency')->nullable();
            $table->double('amount')->nullable();
            $table->string('payer');
            $table->string('url_confirmation');
            $table->string('url_return');
            $table->string('url_redirect');
            $table->longText('optional')->nullable();
            $table->longText('pending_info')->nullable();
            $table->longText('payment_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('flow.table_name'));
    }
}
