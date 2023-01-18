<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deleted_models', function (Blueprint $table) {
            $table->id();

            $table->string('key', 40);
            $table->string('model');
            $table->json('values');

            $table->timestamps();

            $table->unique(['model', 'key']);
        });
    }
};
