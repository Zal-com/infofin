<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class NewsletterScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Db::table('newsletter_schedule')->count() == 0){
            DB::table('newsletter_schedule')->insert([
                [
                    'day_of_week' => '1',
                    'send_time' => '08:00:00',
                    'is_active' => 1,
                    'message' => 'Lettre d information hebdomadaire - Édition du lundi',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'day_of_week' => '2',
                    'send_time' => '14:00:00',
                    'is_active' => 1,
                    'message' => 'Mise à jour en milieu de semaine',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'day_of_week' => '3',
                    'send_time' => '18:00:00',
                    'is_active' => 0,
                    'message' => 'Synthèse de fin de semaine',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'day_of_week' => '4',
                    'send_time' => '09:00:00',
                    'is_active' => 1,
                    'message' => 'Conseils hebdomadaires',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'day_of_week' => '5',
                    'send_time' => '15:30:00',
                    'is_active' => 1,
                    'message' => 'Événements à venir ce week-end',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'day_of_week' => '6',
                    'send_time' => '10:00:00',
                    'is_active' => 1,
                    'message' => 'Les meilleures offres de la semaine',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
    
    
            ]);

        }else{
            echo "La table `newsletter_schedule` contient déjà des données, aucun seeding n'a été effectué.\n";
        }
       
    }
}
