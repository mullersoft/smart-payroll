<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('transaction_type');
            $table->string('tx_ref')->nullable()->index()->after('provider');
            $table->string('checkout_url')->nullable()->after('tx_ref');
            $table->json('provider_meta')->nullable()->after('checkout_url');
            $table->timestamp('verified_at')->nullable()->after('status');
            $table->text('failure_reason')->nullable()->change(); // if not already text
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['provider', 'tx_ref', 'checkout_url', 'provider_meta', 'verified_at']);
        });
    }
};
