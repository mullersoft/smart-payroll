<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('position_allowance_position_id')
                ->nullable()
                ->constrained('positions')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['position_allowance_position_id']);
            $table->dropColumn('position_allowance_position_id');
        });
    }
};
