<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energies', function (Blueprint $table) {
            $table->id();
            $table->morphs('energisable');
            $table->float('amount', 8)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energies');
    }
};
