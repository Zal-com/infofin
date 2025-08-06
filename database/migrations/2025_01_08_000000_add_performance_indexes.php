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
        // Projects table indexes
        Schema::table('projects', function (Blueprint $table) {
            $table->index(['organisation_id', 'status'], 'idx_projects_org_status');
            $table->index('updated_at', 'idx_projects_updated_at');
            $table->index(['status', 'updated_at'], 'idx_projects_status_updated');
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_login')) {
                $table->index('last_login', 'idx_users_last_login');
            }
            $table->index('is_email_subscriber', 'idx_users_email_subscriber');
        });

        // Pivot tables composite indexes for efficient lookups
        Schema::table('projects_scientific_domains', function (Blueprint $table) {
            $table->index(['project_id', 'scientific_domain_id'], 'idx_proj_sci_domain');
            $table->index(['scientific_domain_id', 'project_id'], 'idx_sci_domain_proj');
        });

        Schema::table('users_favorite_projects', function (Blueprint $table) {
            $table->index(['user_id', 'project_id'], 'idx_user_fav_proj');
            $table->index(['project_id', 'user_id'], 'idx_fav_proj_user');
        });

        if (Schema::hasTable('projects_info_types')) {
            Schema::table('projects_info_types', function (Blueprint $table) {
                $table->index(['project_id', 'info_type_id'], 'idx_proj_info_type');
                $table->index(['info_type_id', 'project_id'], 'idx_info_type_proj');
            });
        }

        if (Schema::hasTable('projects_activities')) {
            Schema::table('projects_activities', function (Blueprint $table) {
                $table->index(['project_id', 'activity_id'], 'idx_proj_activity');
                $table->index(['activity_id', 'project_id'], 'idx_activity_proj');
            });
        }

        if (Schema::hasTable('projects_expenses')) {
            Schema::table('projects_expenses', function (Blueprint $table) {
                $table->index(['project_id', 'expense_id'], 'idx_proj_expense');
                $table->index(['expense_id', 'project_id'], 'idx_expense_proj');
            });
        }

        if (Schema::hasTable('users_scientific_domains')) {
            Schema::table('users_scientific_domains', function (Blueprint $table) {
                $table->index(['user_id', 'scientific_domain_id'], 'idx_user_sci_domain');
                $table->index(['scientific_domain_id', 'user_id'], 'idx_sci_domain_user');
            });
        }

        if (Schema::hasTable('users_info_types')) {
            Schema::table('users_info_types', function (Blueprint $table) {
                $table->index(['user_id', 'info_type_id'], 'idx_user_info_type');
                $table->index(['info_type_id', 'user_id'], 'idx_info_type_user');
            });
        }

        // Info sessions indexes
        if (Schema::hasTable('info_sessions')) {
            Schema::table('info_sessions', function (Blueprint $table) {
                $table->index(['organisation_id', 'session_datetime'], 'idx_info_sessions_org_date');
                $table->index('session_datetime', 'idx_info_sessions_datetime');
            });
        }

        // Collections indexes
        if (Schema::hasTable('projects_collections')) {
            Schema::table('projects_collections', function (Blueprint $table) {
                $table->index(['collection_id', 'project_id'], 'idx_collection_proj');
                $table->index(['project_id', 'collection_id'], 'idx_proj_collection');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_org_status');
            $table->dropIndex('idx_projects_updated_at');
            $table->dropIndex('idx_projects_status_updated');
        });

        // Users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_login')) {
                $table->dropIndex('idx_users_last_login');
            }
            $table->dropIndex('idx_users_email_subscriber');
        });

        // Pivot tables
        Schema::table('projects_scientific_domains', function (Blueprint $table) {
            $table->dropIndex('idx_proj_sci_domain');
            $table->dropIndex('idx_sci_domain_proj');
        });

        Schema::table('users_favorite_projects', function (Blueprint $table) {
            $table->dropIndex('idx_user_fav_proj');
            $table->dropIndex('idx_fav_proj_user');
        });

        if (Schema::hasTable('projects_info_types')) {
            Schema::table('projects_info_types', function (Blueprint $table) {
                $table->dropIndex('idx_proj_info_type');
                $table->dropIndex('idx_info_type_proj');
            });
        }

        if (Schema::hasTable('projects_activities')) {
            Schema::table('projects_activities', function (Blueprint $table) {
                $table->dropIndex('idx_proj_activity');
                $table->dropIndex('idx_activity_proj');
            });
        }

        if (Schema::hasTable('projects_expenses')) {
            Schema::table('projects_expenses', function (Blueprint $table) {
                $table->dropIndex('idx_proj_expense');
                $table->dropIndex('idx_expense_proj');
            });
        }

        if (Schema::hasTable('users_scientific_domains')) {
            Schema::table('users_scientific_domains', function (Blueprint $table) {
                $table->dropIndex('idx_user_sci_domain');
                $table->dropIndex('idx_sci_domain_user');
            });
        }

        if (Schema::hasTable('users_info_types')) {
            Schema::table('users_info_types', function (Blueprint $table) {
                $table->dropIndex('idx_user_info_type');
                $table->dropIndex('idx_info_type_user');
            });
        }

        if (Schema::hasTable('info_sessions')) {
            Schema::table('info_sessions', function (Blueprint $table) {
                $table->dropIndex('idx_info_sessions_org_date');
                $table->dropIndex('idx_info_sessions_datetime');
            });
        }

        if (Schema::hasTable('projects_collections')) {
            Schema::table('projects_collections', function (Blueprint $table) {
                $table->dropIndex('idx_collection_proj');
                $table->dropIndex('idx_proj_collection');
            });
        }
    }
};