<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doc_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'bak', 'rkb', etc.
            $table->text('content');
            $table->text('thumbnail')->nullable();
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doc_templates');
    }
};
