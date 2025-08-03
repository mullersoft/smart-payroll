<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // salary, tax, pension
            $table->decimal('amount', 10, 2);

            $table->string('from_account'); // Qelem Meda's account
            $table->string('to_account');   // employee or tax authority

            $table->dateTime('transaction_date');
            $table->string('processed_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
