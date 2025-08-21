// database/migrations/2025_08_16_000001_create_allowances_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Housing Allowance, Transport Allowance
            $table->string('description')->nullable(); // explanation text
            $table->enum('type', ['fixed', 'percent']); // fixed = 1000 ETB, percent = % of base salary
            $table->decimal('value', 10, 2); // amount or % value
            $table->boolean('is_taxable')->default(true);

            // Scope allowance
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('allowances');
    }
};
