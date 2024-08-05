<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignaturesTable extends Migration
{
    public function up()
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            // $table->string('user_id');
            $table->unsignedBigInteger('user_id');
            $table->string('name')->default('');
            $table->string('last_name')->default('');
            $table->string('email')->default('');
            $table->string('title')->nullable();
            $table->string('company')->default('');
            $table->string('meeting_link')->nullable(); 
            $table->string('address')->default('');
            $table->string('website')->default('');
            $table->string('linkedin_profile')->nullable();
            $table->string('company_linkedin')->nullable();
            $table->string('facebook')->nullable();
            $table->string('feedback')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('phone')->default('');
            // $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->default('');
            $table->rememberToken();
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('signatures');
    }
}
