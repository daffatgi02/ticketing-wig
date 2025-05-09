<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->boolean('use_in_report')->default(false)->after('filesize');
            $table->integer('report_order')->nullable()->after('use_in_report');
        });
    }

    public function down()
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->dropColumn(['use_in_report', 'report_order']);
        });
    }
};
