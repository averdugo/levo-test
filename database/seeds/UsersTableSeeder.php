<?php

use Illuminate\Database\Seeder;
use App\{User, Account};

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        $faker = \Faker\Factory::create();
        $password = Hash::make('levotest');

        User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => $password,
            'type' => 'admin'
        ]);

        $user = User::create([
            'name' => 'Aldo Verdugo',
            'email' => $faker->email,
            'password' => $password,
            'type' => 'client'
        ]);

        Account::create([
            'user_id'=>$user->id,
            'card_number' => '6011328012741707',        
            'balance'=> $faker->numberBetween($min = 1000, $max = 20000),
            'type'=> $faker->creditCardType          
        ]);
    }
}
