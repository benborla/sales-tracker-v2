<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_information', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // available values: customer | staff
            $table->string('type', 10)->default('customer');
            $table->string('email', 100);
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('telephone_number', 50)->nullable();
            $table->string('mobile_number', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            // Address
            $table->string('billing_address')->nullable();
            $table->string('billing_address_city')->nullable();
            $table->string('billing_address_state')->nullable();
            $table->string('billing_address_zipcode')->nullable();
            $table->string('billing_address_country')->nullable();

            $table->string('shipping_address')->nullable();
            $table->string('shipping_address_city')->nullable();
            $table->string('shipping_address_state')->nullable();
            $table->string('shipping_address_zipcode')->nullable();
            $table->string('shipping_address_country')->nullable();

            // Types: JCB, Visa, Mastercard
            $table->string('credit_card_type')->nullable();
            $table->string('credit_card_number')->nullable();
            $table->string('credit_card_expiration_date')->nullable();
            $table->string('credit_card_cvv')->nullable();

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
        Schema::dropIfExists('user_information');
    }
}
