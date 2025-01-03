<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeRequestTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['user', 'moderator', 'admin'])->default('user');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            // Add indexes for frequently accessed columns
            $table->index('email');
            $table->index('role');
        });

        // Change requests table
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('table_name', ['countries', 'states', 'cities']);
            $table->enum('change_type', ['add', 'update', 'delete']);
            $table->json('original_data')->nullable();
            $table->json('new_data');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Add indexes for filtering and sorting
            $table->index('status');
            $table->index(['table_name', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });

        // Change request comments table
        Schema::create('change_request_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('change_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->timestamps();
            $table->softDeletes();
            
            // Add indexes for efficient retrieval
            $table->index(['change_request_id', 'created_at']);
        });

        // Change request attachments table
        Schema::create('change_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('change_request_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->timestamps();
            $table->softDeletes();
            
            // Add index for change request relationship
            $table->index('change_request_id');
        });

        // Audit log table for tracking all changes
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('action');
            $table->string('table_name');
            $table->unsignedBigInteger('record_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Add indexes for efficient querying
            $table->index(['table_name', 'record_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop tables in reverse order to handle foreign key constraints
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('change_request_attachments');
        Schema::dropIfExists('change_request_comments');
        Schema::dropIfExists('change_requests');
        Schema::dropIfExists('users');
    }
}
