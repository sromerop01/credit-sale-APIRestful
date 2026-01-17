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
        Schema::create('loan_roads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('detail');
            $table->date('start_date');
            $table->decimal('sales_commission');
            $table->decimal('legth');
            $table->decimal('latitude');
            $table->boolean('inactive');

            $table->unsignedBigInteger('user_id')
                    ->nullable();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');

            $table->unsignedBigInteger('supervisor_id')
                    ->nullable();
            $table->foreign('supervisor_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_roads', function(Blueprint $table) {
           $table->dropForeign(['user_id']);
           $table->dropForeign(['supervisor_id']);
        });

        Schema::dropIfExists('loan_roads');
    }
};
