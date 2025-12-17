<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('mime_type');
            $table->string('extension', 10);
            $table->unsignedBigInteger('size'); // bytes
            $table->unsignedBigInteger('optimized_size')->nullable(); // bytes after optimization
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('disk')->default('public');
            $table->string('collection')->default('general'); // general, logo, product, document, etc.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->json('metadata')->nullable(); // additional metadata
            $table->timestamps();

            $table->index(['collection', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('mime_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
