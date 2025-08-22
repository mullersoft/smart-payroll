<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employee_allowance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('allowance_id')->constrained()->onDelete('cascade');
            $table->decimal('value', 12, 2)->default(0);   // amount for this employee
            $table->boolean('is_taxable')->default(true);  // taxable flag
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_allowance');
    }
};
