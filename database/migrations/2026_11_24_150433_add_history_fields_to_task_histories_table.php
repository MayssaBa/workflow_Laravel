<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('task_histories', 'task_id')) {
            Schema::table('task_histories', function (Blueprint $table) {
                $table->foreignId('task_id')
                    ->constrained('tasks')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('task_histories', 'user_id')) {
            Schema::table('task_histories', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            });
        } else {
            Schema::table('task_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }

        Schema::table('task_histories', function (Blueprint $table) {
            if (! Schema::hasColumn('task_histories', 'action')) {
                $table->string('action');
            }

            if (! Schema::hasColumn('task_histories', 'field')) {
                $table->string('field')->nullable();
            }

            if (! Schema::hasColumn('task_histories', 'old_value')) {
                $table->text('old_value')->nullable();
            }

            if (! Schema::hasColumn('task_histories', 'new_value')) {
                $table->text('new_value')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('task_histories', function (Blueprint $table) {
            $columns = [];

            foreach (['action', 'field', 'old_value', 'new_value'] as $column) {
                if (Schema::hasColumn('task_histories', $column)) {
                    $columns[] = $column;
                }
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
