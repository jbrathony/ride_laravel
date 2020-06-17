<?php

use Illuminate\Database\Seeder;

class PaymentGatewayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_gateway')->delete();

        DB::table('payment_gateway')->insert([
            ['name' => 'mode', 'value' => 'sandbox', 'site' => 'Braintree'],
            ['name' => 'merchant_id', 'value' => 'g3dprd7kyfs7f3jr', 'site' => 'Braintree'],
            ['name' => 'public_key', 'value' => 'prwd98qgnqkdptkp', 'site' => 'Braintree'],
            ['name' => 'private_key', 'value' => 'fe3e98760ba97b6b2e01fe28379cd477', 'site' => 'Braintree'],
        ]);
    }
}