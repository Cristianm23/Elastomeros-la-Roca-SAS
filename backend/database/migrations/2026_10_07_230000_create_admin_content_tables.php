<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table): void {
            $table->id(); $table->string('question'); $table->text('answer');
            $table->boolean('is_active')->default(true); $table->unsignedInteger('sort_order')->default(0); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('testimonials', function (Blueprint $table): void {
            $table->id(); $table->string('author'); $table->string('company')->nullable(); $table->text('quote');
            $table->boolean('is_active')->default(true); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id(); $table->string('title'); $table->string('slug')->unique(); $table->text('excerpt')->nullable(); $table->longText('body');
            $table->string('status')->default('draft')->index(); $table->timestamp('published_at')->nullable(); $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps(); $table->softDeletes();
        });
        Schema::table('quotes', function (Blueprint $table): void {
            $table->foreignId('assigned_to')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable()->after('observations');
        });
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); $table->string('auditable_type')->nullable(); $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable(); $table->json('new_values')->nullable(); $table->ipAddress('ip_address')->nullable(); $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']); $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::table('quotes', fn (Blueprint $table) => $table->dropForeign(['assigned_to']));
        Schema::table('quotes', fn (Blueprint $table) => $table->dropColumn(['assigned_to', 'admin_notes']));
        Schema::dropIfExists('blog_posts'); Schema::dropIfExists('testimonials'); Schema::dropIfExists('faqs');
    }
};
