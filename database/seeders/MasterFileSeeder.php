<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class MasterFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        \App\Models\MasterFileTimeline::truncate();
        MasterFile::truncate();

        // 👇 CALL YOUR FIXED & CSV SEEDING FUNCTIONS
        $this->insertFixedData();
        $this->importFromCSV();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }




    /**
     * Insert fixed data that never changes
     */
    private function insertFixedData(): void
    {
        $fixedData = [
            [
                'month' => 'February',
                'date' => '2024-02-05',
                'company' => 'Media House Sdn Bhd',
                'product' => 'Outdoor',
                'traffic' => '1800',
                'duration' => '30',
                'status' => 'completed',
                'client' => 'Banking Corporation',
                'date_finish' => '2024-02-10',
                'job_number' => 'JOB004',
                'artwork' => 'Client',
                'invoice_date' => '2024-02-15',
                'invoice_number' => 'INV002',
            ],
            [
                'month' => 'February',
                'date' => '2024-02-12',
                'company' => 'FB IG Ad	',
                'product' => 'FB IG Ad',
                'traffic' => '2500',
                'duration' => '90',
                'status' => 'ongoing',
                'client' => 'Automotive Company',
                'date_finish' => null,
                'job_number' => 'JOB005',
                'artwork' => 'BGOC',
                'invoice_date' => null,
                'invoice_number' => null,
            ],
            [
                'month' => 'March',
                'date' => '2024-03-01',
                'company' => ' titit',
                'product' => 'KLTG',
                'traffic' => '1200',
                'duration' => '15',
                'status' => 'completed',
                'client' => 'Restaurant Chain',
                'date_finish' => '2024-03-05',
                'job_number' => 'JOB006',
                'artwork' => 'Client',
                'invoice_date' => '2024-03-10',
                'invoice_number' => 'INV003',
            ],
            [
                'month' => 'March',
                'date' => '2024-03-08',
                'company' => 'Event Marketing Group',
                'product' => 'Outdoor',
                'traffic' => '500',
                'duration' => '7',
                'status' => 'completed',
                'client' => 'Concert Promoter',
                'date_finish' => '2024-03-12',
                'job_number' => 'JOB007',
                'artwork' => 'BGOC',
                'invoice_date' => '2024-03-15',
                'invoice_number' => 'INV004',
            ],
            [
                'month' => 'March',
                'date' => '2024-03-15',
                'company' => 'Festival Organizers',
                'product' => 'Outdoor',
                'traffic' => '800',
                'duration' => '10',
                'status' => 'pending',
                'client' => 'Cultural Festival',
                'date_finish' => null,
                'job_number' => 'JOB008',
                'artwork' => 'Client',
                'invoice_date' => null,
                'invoice_number' => null,
            ],
            [
                'month' => 'April',
                'date' => '2024-04-01',
                'company' => 'Social Media Agency',
                'product' => 'Media Social Management',
                'traffic' => '5000',
                'duration' => '30',
                'status' => 'ongoing',
                'client' => 'E-commerce Platform',
                'date_finish' => null,
                'job_number' => 'JOB009',
                'artwork' => 'BGOC',
                'invoice_date' => null,
                'invoice_number' => null,
            ],
            [
                'month' => 'April',
                'date' => '2024-04-10',
                'company' => 'Signage Solutions',
                'product' => 'KLTG',
                'traffic' => '1000',
                'duration' => '20',
                'status' => 'completed',
                'client' => 'Office Building',
                'date_finish' => '2024-04-15',
                'job_number' => 'JOB010',
                'artwork' => 'Client',
                'invoice_date' => '2024-04-20',
                'invoice_number' => 'INV005',
            ],
        ];

        foreach ($fixedData as $data) {
            MasterFile::create($data);
        }

        $this->command->info('✓ Fixed data inserted: ' . count($fixedData) . ' records');
    }

    /**
     * Import data from CSV file if it exists
     */
    private function importFromCSV(): void
    {
        // Path to CSV file in database/seeders directory
        $csvPath = database_path('seeders/masterfile_data.csv');

        if (!File::exists($csvPath)) {
            $this->command->warn('⚠ CSV file not found at: ' . $csvPath);
            $this->command->info('You can create a CSV file with the following headers:');
            $this->command->info('month,date,company,product,traffic,duration,status,client,date_finish,job_number,artwork,invoice_date,invoice_number');
            return;
        }

        try {
            // Read CSV file
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setHeaderOffset(0); // First row is header

            $records = $csv->getRecords();
            $count = 0;

            foreach ($records as $record) {
                // Skip empty rows
                if (empty(trim($record['month'])) && empty(trim($record['company']))) {
                    continue;
                }

                // Create the record
                MasterFile::create([
                    'month' => trim($record['month']) ?: null,
                    'date' => $this->parseDate(trim($record['date'])) ?: null,
                    'company' => trim($record['company']) ?: null,
                    'product' => trim($record['product']) ?: null,
                    'traffic' => trim($record['traffic']) ?: null,
                    'duration' => trim($record['duration']) ?: null,
                    'status' => trim($record['status']) ?: 'pending',
                    'client' => trim($record['client']) ?: null,
                    'date_finish' => $this->parseDate(trim($record['date_finish'] ?? '')) ?: null,
                    'job_number' => trim($record['job_number'] ?? '') ?: null,
                    'artwork' => trim($record['artwork'] ?? '') ?: null,
                    'invoice_date' => $this->parseDate(trim($record['invoice_date'] ?? '')) ?: null,
                    'invoice_number' => trim($record['invoice_number'] ?? '') ?: null,
                ]);

                $count++;
            }

            $this->command->info('✓ CSV data imported: ' . $count . ' records');

        } catch (\Exception $e) {
            $this->command->error('✗ Error importing CSV: ' . $e->getMessage());
        }
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Handle Excel date serial numbers
            if (is_numeric($date)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            }

            // Handle string dates
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            $this->command->warn('⚠ Could not parse date: ' . $date);
            return null;
        }
    }
}
