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

            $table->decimal('position_allowance_taxable', 10, 2);
            $table->decimal('position_allowance_non_tax', 10, 2);

            $table->decimal('transport_allowance', 10, 2);
            $table->decimal('other_commission', 10, 2)->nullable();

            $table->decimal('gross_pay', 10, 2);
            $table->decimal('taxable_income', 10, 2);

            $table->decimal('income_tax', 10, 2);
            $table->decimal('employee_pension', 10, 2);
            $table->decimal('employer_pension', 10, 2);
            $table->decimal('pension_contribution', 10, 2);
            $table->decimal('total_deduction', 10, 2);
            $table->decimal('net_payment', 10, 2);

            $table->enum('status', ['prepared', 'approved', 'paid', 'rejected'])->default('prepared');
            $table->boolean('is_processed')->default(false);

            $table->foreignId('prepared_by')->nullable()->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('rejection_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
}
