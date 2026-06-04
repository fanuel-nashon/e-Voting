<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('election_settings', function (Blueprint $table) {
            $table->date('date')->nullable()->default(null)->change();
            $table->string('time')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('election_settings', function (Blueprint $table) {
            $table->date('date')->nullable(false)->change();
            $table->string('time')->nullable(false)->change();
        });
    }
};
