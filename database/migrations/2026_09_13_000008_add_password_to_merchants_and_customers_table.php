<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
        });

        // Set default password '123456' for all existing merchants and customers
        $defaultPasswordHash = Hash::make('123456');
        DB::table('merchants')->whereNull('password')->update(['password' => $defaultPasswordHash]);
        DB::table('customers')->whereNull('password')->update(['password' => $defaultPasswordHash]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn('password');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};
