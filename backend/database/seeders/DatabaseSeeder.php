<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            UsersSeeder::class,
            InstitutionsSeeder::class,
            ProjectsSeeder::class,
            DocumentsSeeder::class,
            FinancementsSeeder::class,
            RemboursementsSeeder::class,
            NotificationsSeeder::class,
            MessagesSeeder::class,
            AuditLogsSeeder::class,
            DisputesSeeder::class,
            DocumentRuleSeeder::class,
        ]);
    }
}
