<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsletterScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Db::table('newsletter_schedule')->count() == 0) {
            DB::table('newsletter_schedule')->insert([
                [
                    'day_of_week' => '1',
                    'send_time' => '08:00:00',
                    'is_active' => 1,
                    'message' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

            ]);

        } else {
            echo "La table `newsletter_schedule` contient déjà des données, aucun seeding n'a été effectué.\n";
        }

    }
}
