<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_logs', function (Blueprint $table) {
            $table->id();
            $table->string('voter_hash', 64);       // SHA-256 of user_id + APP_KEY
            $table->string('faculty_name')->nullable();
            $table->string('program_name')->nullable();
            $table->string('position_name');        // position type cast
            $table->string('action')->default('vote_cast'); // vote_cast | login | logout
            $table->string('ip_prefix', 16)->nullable(); // first two octets only
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_logs');
    }
};
