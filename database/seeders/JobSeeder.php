<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use Illuminate\Support\Carbon;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Job::insert([
            [
                'company_name' => 'TechNova Sdn Bhd',
                'site_name' => 'Mid Valley',
                'product' => 'Outdoor Billboard',
                'status' => 'completed',
                'design' => true,
                'client_approval' => true,
                'printing' => true,
                'installation' => true,
                'remarks' => 'Delivered ahead of time',
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->subDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_name' => 'CreativeWorks',
                'site_name' => 'KLCC',
                'product' => 'Banner',
                'status' => 'ongoing',
                'design' => true,
                'client_approval' => true,
                'printing' => false,
                'installation' => false,
                'remarks' => 'Waiting for printing',
                'start_date' => Carbon::now()->subDays(4),
                'end_date' => Carbon::now()->addDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_name' => 'EcoPrint Malaysia',
                'site_name' => 'Bangsar South',
                'product' => 'Flyers',
                'status' => 'pending',
                'design' => false,
                'client_approval' => false,
                'printing' => false,
                'installation' => false,
                'remarks' => 'Waiting for client feedback',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_name' => 'Red Dot Agency',
                'site_name' => 'Pavilion KL',
                'product' => 'LED Display',
                'status' => 'completed',
                'design' => true,
                'client_approval' => true,
                'printing' => true,
                'installation' => true,
                'remarks' => 'Perfect execution',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->subDays(8),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_name' => 'UrbanGraphix',
                'site_name' => 'Sunway Pyramid',
                'product' => 'Vehicle Wrap',
                'status' => 'ongoing',
                'design' => true,
                'client_approval' => false,
                'printing' => false,
                'installation' => false,
                'remarks' => 'Client needs revision on design',
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
