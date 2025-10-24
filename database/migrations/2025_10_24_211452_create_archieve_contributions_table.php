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
        Schema::create('archieve_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->index();
            $table->unsignedBigInteger('deceased_id')->nullable()->index();
            $table->string('collector')->nullable();
            $table->decimal('amount', 10, 2);
            $table->dateTime('payment_date');
            $table->enum('purok', ['purok1', 'purok2', 'purok3', 'purok4'])->nullable();
            $table->enum('status', ['paid', 'not_paid'])->default('not_paid');
            $table->unsignedBigInteger('updated_by')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archieve_contributions');
    }
};
