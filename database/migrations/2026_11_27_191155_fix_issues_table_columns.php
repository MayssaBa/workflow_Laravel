<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->foreignId('task_id')
                ->after('id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->foreignId('resolved_by')
                ->nullable()
                ->after('task_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status')
                ->default('pending')
                ->after('resolved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropForeign(['resolved_by']);

            $table->dropColumn([
                'task_id',
                'resolved_by',
                'status',
            ]);
        });
    }
};
