<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->boolean('needs_external_support')->default(false)->after('rejection_reason');
            $table->text('external_support_reason')->nullable()->after('needs_external_support');
            $table->string('bak_document')->nullable()->after('external_support_reason');
            $table->string('rkb_document')->nullable()->after('bak_document');
            $table->string('resolution_document')->nullable()->after('rkb_document');
            $table->timestamp('external_support_requested_at')->nullable()->after('closed_at');
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'needs_external_support',
                'external_support_reason',
                'bak_document',
                'rkb_document',
                'resolution_document',
                'external_support_requested_at'
            ]);
        });
    }
};
