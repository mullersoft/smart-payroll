<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->onDelete('cascade');

            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('hours', 8, 2)->default(0); // number of overtime hours
            $table->string('rate_type'); // e.g. weekday_evening, night, rest_day, holiday
            $table->decimal('multiplier', 4, 2); // 1.25, 1.5, 2.0, 2.5
            $table->decimal('amount', 12, 2)->default(0); // calculated overtime pay
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('overtimes');
    }
};
