<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable  extends Migration
{
    public function up()
{
 Schema::create('bank_accounts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->nullable()->unique()->constrained()->onDelete('cascade');
    $table->string('account_number')->unique();
    $table->string('owner_name')->nullable(); // optional
    $table->decimal('balance', 12, 2)->default(0);
    $table->timestamps();
});

}


    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
}