<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->unsignedBigInteger('deceased_id')->after('member_id');
            $table->foreign('deceased_id')->references('member_id')->on('death_reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropForeign(['deceased_id']);
            $table->dropColumn('deceased_id');
        });
    }
};
