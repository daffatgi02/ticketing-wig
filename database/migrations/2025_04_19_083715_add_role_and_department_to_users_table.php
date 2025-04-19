<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'it_support', 'ga_support', 'admin'])->default('user');
            $table->foreignId('department_id')->nullable()->constrained();
            $table->string('employee_id')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department_id', 'employee_id', 'position', 'phone']);
        });
    }
};
