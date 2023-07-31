<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderEntryProductPivotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_entry_product_pivots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_entries_id')->constrained('order_entries')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('quantity')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_entry_product_pivots');
    }
}
