<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['academic', 'sports', 'cultural', 'social', 'other'])->default('other');

            $table->date('start_date');
            $table->date('end_date');

            $table->json('daily_times')->nullable();

            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->enum('status', ['draft', 'pending_approval', 'published', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();


            $table->unsignedInteger('registration')->default(0);

            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['organization_id', 'status', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
