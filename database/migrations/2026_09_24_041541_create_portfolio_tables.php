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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_ru');
            $table->string('name_en');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title_ru');
            $table->string('title_en')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_en')->nullable();
            $table->string('location_ru')->nullable();
            $table->string('location_en')->nullable();
            $table->string('shooting_date')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained('series')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('alt_ru')->nullable();
            $table->string('alt_en')->nullable();
            $table->string('caption_ru')->nullable();
            $table->string('caption_en')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_ru');
            $table->string('title_en')->nullable();
            $table->string('subtitle_ru')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->string('duration_ru')->nullable();
            $table->string('duration_en')->nullable();
            $table->string('photo_count_ru')->nullable();
            $table->string('photo_count_en')->nullable();
            $table->text('includes_ru')->nullable();
            $table->text('includes_en')->nullable();
            $table->string('delivery_time_ru')->nullable();
            $table->string('delivery_time_en')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->boolean('is_price_from')->default(false);
            $table->text('extra_conditions_ru')->nullable();
            $table->text('extra_conditions_en')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question_ru');
            $table->string('question_en')->nullable();
            $table->text('answer_ru')->nullable();
            $table->text('answer_en')->nullable();
            $table->boolean('is_draft')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('series');
        Schema::dropIfExists('categories');
    }
};
