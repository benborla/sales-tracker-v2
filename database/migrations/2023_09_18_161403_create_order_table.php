<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id');
            $table->string('reference_id')->nullable();

            $table->string('email')->nullable();

            $table->foreignId('user_id')->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('handled_by_agent_user_id')->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('num_of_boxes_shipped')->default(1);
            $table->string('shipper')->default('usps');
            $table->decimal('shipping_fee')->default(0);
            $table->decimal('tax_fee');
            $table->decimal('intermediary_fees');
            $table->string('tracking_type')->default('tracking_number');
            $table->string('tracking_reference');

            // INFO: total price of the product ordered + (shipping fee - tax fee
            // - intermediary_fess)
            $table->decimal('total_sales')->nullable(0);
            $table->text('notes')->nullable();
            /**
             * @INFO: Available statuses
             * New, Processed, In-transit, fulfilled or delivered, Failed
             */
            $table->text('order_status')->nullable();
            /**
             * @INFO: Available statuses
             * Awaiting Payment, Payment Failed, Payment Received
             */
            $table->text('payment_status')->nullable();

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
        Schema::drop('order_entries');
    }
}
