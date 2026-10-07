<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table): void {
            $table->string('city', 120)->nullable()->after('phone');
            $table->date('required_date')->nullable()->after('city');
            $table->text('observations')->nullable()->after('required_date');
            $table->string('attachment_path')->nullable()->after('observations');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table): void {
            $table->dropColumn(['city', 'required_date', 'observations', 'attachment_path']);
        });
    }
};
