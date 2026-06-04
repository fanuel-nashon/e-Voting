<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('voter_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('faculty_id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->string('voter_id')->nullable();
        });
    }
};
