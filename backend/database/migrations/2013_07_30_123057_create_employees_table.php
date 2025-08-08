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
        $table->enum('position', ['CEO', 'COO', 'CTO', 'CISO', 'Director', 'Dept Lead', 'Normal Employee']);
        $table->enum('employment_type', ['permanent', 'contract']);
        $table->decimal('base_salary', 10, 2);
        $table->string('gender'); 
        $table->date('employment_date');
        $table->timestamps();
        $table->boolean('is_active')->default(true);

    });
}

   
}
