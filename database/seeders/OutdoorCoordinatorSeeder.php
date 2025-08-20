<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OutdoorCoordinatorTracking;
use App\Models\MasterFile;

class OutdoorCoordinatorSeeder extends Seeder
{
    public function run()
    {
        $outdoorFiles = MasterFile::where('product_category', 'Outdoor')->get();
        $count = 0;

        foreach ($outdoorFiles as $file) {
            OutdoorCoordinatorTracking::create([
                'master_file_id' => $file->id,
                'client' => $file->client,
                'product' => $file->product,
                'status' => $file->status ?? 'pending',
                // field lain kosong dulu (manual diisi nanti)
            ]);
            $count++;
        }

        $this->command->info("✓ Inserted: $count Outdoor Coordinator records.");
    }
}
