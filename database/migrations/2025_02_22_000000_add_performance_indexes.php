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
            // Index for filtering by status
            $table->index('status', 'idx_change_requests_status');

            // Index for filtering by user
            $table->index('user_id', 'idx_change_requests_user_id');

            // Composite index for user + status queries
            $table->index(['user_id', 'status'], 'idx_change_requests_user_status');

            // Index for date-based queries
            $table->index('created_at', 'idx_change_requests_created_at');

            // Index for approved requests
            $table->index(['status', 'approved_at'], 'idx_change_requests_approved');

            // Index for rejected requests
            $table->index(['status', 'rejected_at'], 'idx_change_requests_rejected');
        });

        Schema::table('comments', function (Blueprint $table) {
            // Index for finding comments by change request
            $table->index('change_request_id', 'idx_comments_change_request_id');

            // Index for finding comments by user
            $table->index('user_id', 'idx_comments_user_id');

            // Composite index for change request + created date
            $table->index(['change_request_id', 'created_at'], 'idx_comments_request_date');
        });

                Schema::table('users', function (Blueprint $table) {
            // Index for admin users
            $table->index('is_admin', 'idx_users_is_admin');

            // Index for email verification
            $table->index('email_verified_at', 'idx_users_email_verified');
        });

        // Add indexes to geographical tables for better search performance
        // Note: These may fail if tables don't exist or indexes already exist - that's OK
        try {
            Schema::table('countries', function (Blueprint $table) {
                $table->index('name', 'idx_countries_name');
                $table->index('iso2', 'idx_countries_iso2');
                $table->index('region_id', 'idx_countries_region_id');
                $table->index('subregion_id', 'idx_countries_subregion_id');
            });
        } catch (\Exception $e) {
            // Table might not exist or indexes might already exist
        }

        try {
            Schema::table('states', function (Blueprint $table) {
                $table->index('name', 'idx_states_name');
                $table->index('country_id', 'idx_states_country_id');
                $table->index('country_code', 'idx_states_country_code');
            });
        } catch (\Exception $e) {
            // Table might not exist or indexes might already exist
        }

        try {
            Schema::table('cities', function (Blueprint $table) {
                $table->index('name', 'idx_cities_name');
                $table->index('state_id', 'idx_cities_state_id');
                $table->index('country_id', 'idx_cities_country_id');
                // Composite index for state + country filtering
                $table->index(['state_id', 'country_id'], 'idx_cities_state_country');
            });
        } catch (\Exception $e) {
            // Table might not exist or indexes might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropIndex('idx_change_requests_status');
            $table->dropIndex('idx_change_requests_user_id');
            $table->dropIndex('idx_change_requests_user_status');
            $table->dropIndex('idx_change_requests_created_at');
            $table->dropIndex('idx_change_requests_approved');
            $table->dropIndex('idx_change_requests_rejected');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('idx_comments_change_request_id');
            $table->dropIndex('idx_comments_user_id');
            $table->dropIndex('idx_comments_request_date');
        });

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('idx_users_is_admin');
                $table->dropIndex('idx_users_email_verified');
            });
        } catch (\Exception $e) {
            // Indexes might not exist
        }

        try {
            Schema::table('countries', function (Blueprint $table) {
                $table->dropIndex('idx_countries_name');
                $table->dropIndex('idx_countries_iso2');
                $table->dropIndex('idx_countries_region_id');
                $table->dropIndex('idx_countries_subregion_id');
            });
        } catch (\Exception $e) {
            // Indexes might not exist
        }

        try {
            Schema::table('states', function (Blueprint $table) {
                $table->dropIndex('idx_states_name');
                $table->dropIndex('idx_states_country_id');
                $table->dropIndex('idx_states_country_code');
            });
        } catch (\Exception $e) {
            // Indexes might not exist
        }

        try {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropIndex('idx_cities_name');
                $table->dropIndex('idx_cities_state_id');
                $table->dropIndex('idx_cities_country_id');
                $table->dropIndex('idx_cities_state_country');
            });
        } catch (\Exception $e) {
            // Indexes might not exist
        }
    }


};
