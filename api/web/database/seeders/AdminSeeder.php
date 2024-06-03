<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new Admin();
        $admin->company_id = 1;
        $admin->email = 'test.user@example.com';
        $admin->password = Hash::make('password');
        $admin->save();
    }
}