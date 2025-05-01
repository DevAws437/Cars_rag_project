<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id'); // علاقة مع السيارات
            $table->decimal('discount_value', 8, 2); // قيمة الخصم
            $table->enum('discount_type', ['daily', 'monthly'])->default('daily'); // نوع الخصم
            $table->date('expiration_date')->nullable(); // تاريخ انتهاء الخصم
            $table->timestamps();

            // مفتاح أجنبي لعلاقة السيارة
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount');
    }
};
