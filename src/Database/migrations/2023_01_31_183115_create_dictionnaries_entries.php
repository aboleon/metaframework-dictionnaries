<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dictionnary_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dictionnary_id')->constrained('dictionnaries')->onDelete('cascade');
            $table->unsignedBigInteger('parent')->nullable()->index();
            $table->unsignedInteger('position')->default(0)->index();
            $table->longText('name');
            $table->longText('custom')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dictionnary_entries');
    }
};
