<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('pay_month');

            $table->decimal('working_days', 5, 2);
            $table->decimal('base_salary', 10, 2);           // snapshot of employee salary
            $table->decimal('earned_salary', 10, 2);

            $table->decimal('position_allowance', 10, 2);
            $table->decimal('transport_allowance', 10, 2);
            $table->decimal('other_commission', 10, 2)->nullable();

            $table->decimal('gross_pay', 10, 2);
            $table->decimal('taxable_income', 10, 2);

            $table->decimal('income_tax', 10, 2);
            $table->decimal('employee_pension', 10, 2);
            $table->decimal('employer_pension', 10, 2)->nullable(); // optional for reporting

            $table->decimal('total_deduction', 10, 2);
            $table->decimal('net_payment', 10, 2);

            $table->enum('status', ['prepared', 'approved', 'paid'])->default('prepared');
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
}
