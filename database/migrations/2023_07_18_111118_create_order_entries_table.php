<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_entries', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id');
            $table->string('reference_id');

            $table->string('email')->nullable();

            $table->foreignId('user_id')->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('num_of_boxes_shipped')->default(1);
            $table->string('shipper')->default('usps');
            $table->float('shipping_fee');
            $table->float('tax_fee');
            $table->float('intermediary_fess');
            $table->string('tracking_type')->default('tracking_number');
            $table->string('tracking_reference');

            $table->foreignId('group_teams_id')->constrained('group_team_members')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // INFO: total price of the product ordered + (shipping fee - tax fee
            // - intermediary_fess)
            $table->float('total_sales')->nullable();
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
        Schema::dropIfExists('order_entries');
    }
}
