<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->date('incident_date')->nullable()->after('external_support_requested_at');
            $table->time('incident_time')->nullable()->after('incident_date');
            $table->text('issue_detail')->nullable()->after('incident_time');
            $table->text('actions_taken')->nullable()->after('issue_detail');
            $table->string('report_recipient')->nullable()->after('actions_taken');
            $table->string('report_recipient_position')->nullable()->after('report_recipient');
            $table->text('additional_notes')->nullable()->after('report_recipient_position');
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'incident_date',
                'incident_time',
                'issue_detail',
                'actions_taken',
                'report_recipient',
                'report_recipient_position',
                'additional_notes'
            ]);
        });
    }
};
