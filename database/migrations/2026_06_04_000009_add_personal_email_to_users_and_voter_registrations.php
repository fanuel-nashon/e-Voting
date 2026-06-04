<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('personal_email')->nullable()->after('email');
        });

        Schema::table('voter_registrations', function (Blueprint $table) {
            $table->string('personal_email')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('personal_email');
        });
        Schema::table('voter_registrations', function (Blueprint $table) {
            $table->dropColumn('personal_email');
        });
    }
};
