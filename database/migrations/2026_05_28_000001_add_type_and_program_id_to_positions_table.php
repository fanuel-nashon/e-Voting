<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            // president | faculty_rep | senator | class_rep
            $table->string('type')->default('president')->after('name');
            $table->foreignId('program_id')->nullable()->after('faculty_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropColumn(['type', 'program_id']);
        });
    }
};
