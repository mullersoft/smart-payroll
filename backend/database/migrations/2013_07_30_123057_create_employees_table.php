<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('employee_id')->unique();
            $table->string('email')->nullable();
            // $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');


            $table->foreignId('position_id')->constrained('positions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('employment_type_id')->constrained('employment_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('base_salary', 10, 2);
            $table->string('gender');
            $table->date('employment_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
