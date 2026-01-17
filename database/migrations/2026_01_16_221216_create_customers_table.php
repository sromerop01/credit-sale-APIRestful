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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('identification');
            $table->string('name');
            $table->string('address');
            $table->string('phone');
            $table->string('detail');
            $table->boolean('delinquent');
            $table->unsignedInteger('quota');
            $table->decimal('interest');
            $table->smallInteger('order');

            $table->unsignedBigInteger('loan_road_id')
                    ->nullable();
            $table->foreign('loan_road_id')
                    ->references('id')
                    ->on('loan_roads')
                    ->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function(Blueprint $table) {
           $table->dropForeign(['oan_road_id']);
        });

        Schema::dropIfExists('customers');
    }
};
