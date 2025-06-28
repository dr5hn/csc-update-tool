<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            // Track incorporation status
            $table->enum('incorporation_status', [
                'pending',           // Approved but not yet incorporated
                'incorporated',      // Successfully incorporated into main DB
                'verified',          // Verified present in external sync
                'missing',           // Missing from external sync (needs re-incorporation)
                'conflicted'         // Conflicts with external data
            ])->default('pending');

            // Track when incorporated
            $table->timestamp('incorporated_at')->nullable();
            $table->string('incorporated_by')->nullable();

            // Track sync verification
            $table->timestamp('last_sync_verified_at')->nullable();
            $table->json('sync_verification_details')->nullable();

            // Track incorporation details
            $table->json('incorporation_details')->nullable();
            $table->text('incorporation_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn([
                'incorporation_status',
                'incorporated_at',
                'incorporated_by',
                'last_sync_verified_at',
                'sync_verification_details',
                'incorporation_details',
                'incorporation_notes'
            ]);
        });
    }
};
