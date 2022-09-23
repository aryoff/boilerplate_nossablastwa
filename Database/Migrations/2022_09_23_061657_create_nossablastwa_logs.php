<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNossablastwaLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nossablastwa_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            if (env('DB_CONNECTION', false) == 'pgsql') {
                $table->jsonb('status')->default('{}');
                $table->jsonb('data')->default('{}');
            } else {
                $table->json('status')->default('{}');
                $table->json('data')->default('{}');
            }
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique('session_id');
        });
        if (env('DB_CONNECTION', false) == 'pgsql') {
            DB::statement('CREATE INDEX nossablastwa_logs_datagin ON nossablastwa_logs USING gin ((data))');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nossablastwa_logs');
    }
}