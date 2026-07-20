<?php

namespace Database\Seeders;

use App\Models\Labo;
use Illuminate\Database\Seeder;

class LabLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Paris'               => ['lat' => 48.8566, 'lng' => 2.3522],
            'Lyon'                => ['lat' => 45.7640, 'lng' => 4.8357],
            'Marseille'           => ['lat' => 43.2965, 'lng' => 5.3698],
            'Nice'                => ['lat' => 43.7102, 'lng' => 7.2620],
            'Toulouse'            => ['lat' => 43.6047, 'lng' => 1.4442],
            'Strasbourg'          => ['lat' => 48.5734, 'lng' => 7.7521],
            'Bordeaux'            => ['lat' => 44.8378, 'lng' => -0.5792],
            'Lille'               => ['lat' => 50.6292, 'lng' => 3.0573],
            'Nantes'              => ['lat' => 47.2184, 'lng' => -1.5536],
            'Rouen'               => ['lat' => 49.4432, 'lng' => 1.0999],
            'Montpellier'         => ['lat' => 43.6108, 'lng' => 3.8767],
            'Orléans'             => ['lat' => 47.9029, 'lng' => 1.9093],
            'Grenoble'            => ['lat' => 45.1885, 'lng' => 5.7245],
            'Biarritz'            => ['lat' => 43.4832, 'lng' => -1.5586],
            'Nancy'               => ['lat' => 48.6921, 'lng' => 6.1844],
            'Créteil'             => ['lat' => 48.7893, 'lng' => 2.4556],
            'Rennes'              => ['lat' => 48.1173, 'lng' => -1.6778],
            'Brest'               => ['lat' => 48.3904, 'lng' => -4.4861],
            'Tours'               => ['lat' => 47.3941, 'lng' => 0.6848],
            'Clermont-Ferrand'    => ['lat' => 45.7772, 'lng' => 3.0870],
        ];

        $count = 0;
        foreach ($locations as $city => $coords) {
            Labo::where('city', $city)
                ->whereNull('latitude')
                ->update([
                    'latitude'  => $coords['lat'] + (mt_rand(-50, 50) / 10000),
                    'longitude' => $coords['lng'] + (mt_rand(-50, 50) / 10000),
                ]);
            $count++;
        }

        $this->command->info("Location assigned to {$count} city(ies).");
    }
}
