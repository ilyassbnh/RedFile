<?php

// This should be your only migration for documents table
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); // Assuming you have an ID field
            $table->string('title');
            $table->text('content');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('file')->nullable();
            $table->timestamps(); // Add timestamps for created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}

