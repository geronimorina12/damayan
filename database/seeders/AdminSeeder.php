<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Hash;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FacadesDB::table('users')->insert([
            'name' => 'Rina Geronimo',
            'email' => 'geronimorina76@gmail.com',
            'email_verified_at' => now(), 
            'password' => Hash::make('iamrina123'),
            'role' => 'admin',
        ]);
    }
    //collector (already in database)
    // jiji_protectdamayan@gmail.org 
    // iamjiji123
}
