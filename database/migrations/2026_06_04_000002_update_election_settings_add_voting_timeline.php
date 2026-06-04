<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('election_settings', function (Blueprint $table) {
            $table->string('title')->default('Student Union Election')->after('id');
            $table->dateTime('voting_opens_at')->nullable()->after('title');
            $table->dateTime('voting_closes_at')->nullable()->after('voting_opens_at');
            $table->dateTime('results_released_at')->nullable()->after('voting_closes_at');
            $table->dateTime('acceptance_deadline_at')->nullable()->after('results_released_at');
        });
    }

    public function down(): void
    {
        Schema::table('election_settings', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'voting_opens_at', 'voting_closes_at',
                'results_released_at', 'acceptance_deadline_at',
            ]);
        });
    }
};
