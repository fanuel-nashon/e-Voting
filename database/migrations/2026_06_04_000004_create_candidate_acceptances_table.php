<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('votes_received')->default(0);
            $table->boolean('won')->default(false);
            $table->string('token', 64)->unique();   // for the acceptance URL
            $table->timestamp('notification_sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->boolean('accepted')->nullable(); // null=no response, true=accepted, false=declined
            $table->text('response_note')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_acceptances');
    }
};
