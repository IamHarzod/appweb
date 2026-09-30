<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->call([
                AdminUserSeeder::class,
                CategorySeeder::class,
                ProductSeeder::class,
            ]);
        });
    }
}
