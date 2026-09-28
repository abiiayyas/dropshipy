<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('public_token', 48)->nullable()->unique()->after('order_number');
        });

        DB::table('orders')->orderBy('id')->eachById(function (object $order): void {
            do {
                $token = Str::random(48);
            } while (DB::table('orders')->where('public_token', $token)->exists());

            DB::table('orders')->where('id', $order->id)->update(['public_token' => $token]);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
