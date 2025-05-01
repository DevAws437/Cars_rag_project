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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('model');          // طراز السيارة
            $table->integer('year'); // أو استخدم string إذا كنت تريد نصًا
            $table->string('color');         // اللون
            $table->text('features')->nullable();  // مميزات السيارة (مصفوفة أو نص طويل)
            $table->integer('seats');        // عدد المقاعد
            $table->string('status');        // حالة السيارة (مؤجر، متاح، تحت الصيانة...)
            $table->string('location');      // موقع السيارة
            $table->decimal('daily_price', 8, 2); // السعر اليومي
            $table->string('image')->nullable();  // رابط الصورة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
};
