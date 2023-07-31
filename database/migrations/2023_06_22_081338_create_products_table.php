<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('name', 50);
            $table->string('upc', 50)->nullable();
            $table->string('asin', 50)->nullable();
            $table->float('retail_price');
            $table->float('reseller_price');
            $table->text('product_image');
            $table->string('weight_value', 10);
            $table->string('weight_unit', 20)->default('lbs');
            $table->string('shipper', 20)->default('USPS');
            $table->float('shipping_fee');
            $table->string('tracking_number', 50);
            $table->text('notes')->nullable();


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
        Schema::dropIfExists('products');
    }
}
