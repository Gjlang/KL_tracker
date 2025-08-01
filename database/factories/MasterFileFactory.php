<?php

namespace Database\Factories;

use App\Models\MasterFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterFileFactory extends Factory
{
    protected $model = MasterFile::class;

    public function definition()
    {
        return [
            'month' => $this->faker->monthName,
            'date' => $this->faker->date(),
            'company' => $this->faker->company,
            'product' => $this->faker->randomElement(['HM', 'TB', 'TTM', 'BB']),
            'traffic' => $this->faker->numberBetween(100, 1000),
            'duration' => $this->faker->numberBetween(1, 30) . ' days',
            'status' => $this->faker->randomElement(['pending', 'ongoing', 'completed']),
            'client' => $this->faker->company,
            'date_finish' => $this->faker->optional()->date(),
            'job_number' => $this->faker->bothify('JOB-###'),
            'artwork' => $this->faker->optional()->randomElement(['BGOC', 'Client']),
            'invoice_date' => $this->faker->optional()->date(),
            'invoice_number' => $this->faker->optional()->bothify('INV-####'),
        ];
    }
}
