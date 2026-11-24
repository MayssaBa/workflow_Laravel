<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('task_histories', function (Blueprint $table) {
            $table->foreignId('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action');
            $table->string('field')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('task_histories', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropForeign(['user_id']);

            $table->dropColumn([
                'task_id',
                'user_id',
                'action',
                'field',
                'old_value',
                'new_value',
            ]);
        });
    }
};
