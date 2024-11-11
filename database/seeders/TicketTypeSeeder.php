<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ticket_types')->insert([
            [
                'name'        => 'group',
                'description' => 'Групповой билет',
                'event_date'  => '31.01.01 13:30',
                'price'       => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'        => 'preferential',
                'description' => 'Льготный билет',
                'event_date'  => '31.01.01 13:30',
                'price'       => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
