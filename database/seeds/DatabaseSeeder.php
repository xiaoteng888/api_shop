<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(\Database\Seeds\AdminTablesSeeder::class);
         $this->call(UsersSeeder::class);
         $this->call(UserAddressesSeeder::class);
         $this->call(CategoriesSeeder::class);
         $this->call(ProductsSeeder::class);
         $this->call(CouponCodesSeeder::class);
         $this->call(OrdersSeeder::class);
    }
}
