<?php

namespace Database\Seeders;

use App\Models\Consumable;
use App\Models\Equipment;
use App\Models\Labo;
use App\Models\WorkingHours;
use Illuminate\Database\Seeder;

class LabSeeder extends Seeder
{
    public function run(): void
    {
        $labsData = [
            ['name' => "Laboratoire d'Analyses Medicales Bio-Renard", 'address' => '15 Avenue de la Republique', 'city' => 'Paris', 'phone' => '+33142005511', 'email' => 'contact@biorenard-paris.fr'],
            ['name' => 'Laboratoire de Biologie Clinique de la Gare', 'address' => '4 Place Bellecour', 'city' => 'Lyon', 'phone' => '+33472008822', 'email' => 'accueil@biogare-lyon.fr'],
            ['name' => 'Centre de Biologie Medicale Prado-Mermoz', 'address' => '250 Avenue du Prado', 'city' => 'Marseille', 'phone' => '+33491003344', 'email' => 'contact@pradomermoz-bio.fr'],
            ['name' => 'Laboratoire Bio-Esterel', 'address' => '12 Promenade des Anglais', 'city' => 'Nice', 'phone' => '+33493006655', 'email' => 'contact@bioesterel-nice.fr'],
            ['name' => 'Laboratoire Medical de la Place', 'address' => '8 Rue Lafayette', 'city' => 'Toulouse', 'phone' => '+33561009900', 'email' => 'secretariat@bioplace-toulouse.fr'],
            ['name' => "Laboratoire d'Analyses de l'Europe", 'address' => "32 Boulevard d'Europe", 'city' => 'Strasbourg', 'phone' => '+33388002233', 'email' => 'contact@bioeurope-stras.fr'],
            ['name' => 'Laboratoire de Biologie Saint-Antoine', 'address' => '45 Rue Saint-Antoine', 'city' => 'Bordeaux', 'phone' => '+33556004455', 'email' => 'accueil@biosaintantoine.fr'],
            ['name' => 'Centre de Diagnostic Medical du Nord', 'address' => '88 Rue Nationale', 'city' => 'Lille', 'phone' => '+33320005566', 'email' => 'contact@cdmnord-lille.fr'],
            ['name' => "Laboratoire d'Analyses du Grand-Canyon", 'address' => '14 Rue Crebillon', 'city' => 'Nantes', 'phone' => '+33240003322', 'email' => 'contact@biocanyon-nantes.fr'],
            ['name' => "Laboratoire de l'Horloge", 'address' => '5 Rue Jeanne d\'Arc', 'city' => 'Rouen', 'phone' => '+33235008899', 'email' => 'contact@biohorloge-rouen.fr'],
            ['name' => 'Laboratoire Bio-Avenir', 'address' => '20 Avenue de Lodeve', 'city' => 'Montpellier', 'phone' => '+33467007788', 'email' => 'contact@bioavenir-montpellier.fr'],
            ['name' => 'Laboratoire de Biologie Centre-Loire', 'address' => '55 Rue Royale', 'city' => 'Orleans', 'phone' => '+33238001122', 'email' => 'contact@biocentreloire.fr'],
            ['name' => "Laboratoire d'Analyses des Alpes", 'address' => '18 Avenue Alsace-Lorraine', 'city' => 'Grenoble', 'phone' => '+33476003344', 'email' => 'contact@bioalpes-grenoble.fr'],
            ['name' => 'Laboratoire de Biologie Medicale Aquitaine', 'address' => '3 Avenue de la Marne', 'city' => 'Biarritz', 'phone' => '+33559002211', 'email' => 'contact@bioaquitaine-biarritz.fr'],
            ['name' => "Centre d'Analyses Medicales de l'Est", 'address' => '10 Place Stanislas', 'city' => 'Nancy', 'phone' => '+33383005544', 'email' => 'contact@camest-nancy.fr'],
            ['name' => 'Laboratoire de Biologie du Val-de-Marne', 'address' => '24 Avenue du General de Gaulle', 'city' => 'Creteil', 'phone' => '+33149008877', 'email' => 'contact@bioval-creteil.fr'],
            ['name' => "Laboratoire d'Analyses Medicales Saint-Michel", 'address' => '14 Boulevard de la Liberte', 'city' => 'Rennes', 'phone' => '+33299004455', 'email' => 'contact@biosaintmichel-rennes.fr'],
            ['name' => "Laboratoire de Biologie Clinique d'Armor", 'address' => '8 Rue de Siam', 'city' => 'Brest', 'phone' => '+33298007788', 'email' => 'contact@bioarmor-brest.fr'],
            ['name' => 'Centre de Pathologie et Biologie', 'address' => '30 Rue Nationale', 'city' => 'Tours', 'phone' => '+33247009988', 'email' => 'contact@biopath-tours.fr'],
            ['name' => "Laboratoire d'Analyses Medicales des Volcans", 'address' => '12 Place de Jaude', 'city' => 'Clermont-Ferrand', 'phone' => '+33473001122', 'email' => 'contact@biovolcans-clermont.fr'],
        ];

        $locations = [
            'Paris' => [48.8566, 2.3522], 'Lyon' => [45.7640, 4.8357], 'Marseille' => [43.2965, 5.3698],
            'Nice' => [43.7102, 7.2620], 'Toulouse' => [43.6047, 1.4442], 'Strasbourg' => [48.5734, 7.7521],
            'Bordeaux' => [44.8378, -0.5792], 'Lille' => [50.6292, 3.0573], 'Nantes' => [47.2184, -1.5536],
            'Rouen' => [49.4432, 1.0999], 'Montpellier' => [43.6108, 3.8767], 'Orleans' => [47.9029, 1.9093],
            'Grenoble' => [45.1885, 5.7245], 'Biarritz' => [43.4832, -1.5586], 'Nancy' => [48.6921, 6.1844],
            'Creteil' => [48.7893, 2.4556], 'Rennes' => [48.1173, -1.6778], 'Brest' => [48.3904, -4.4861],
            'Tours' => [47.3941, 0.6848], 'Clermont-Ferrand' => [45.7772, 3.0870],
        ];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($labsData as $l) {
            $coords = $locations[$l['city']] ?? [48.8566, 2.3522];
            $lab = Labo::updateOrCreate(
                ['email' => $l['email']],
                [
                    'name' => $l['name'], 'address' => $l['address'], 'city' => $l['city'], 'phone' => $l['phone'],
                    'latitude' => $coords[0] + (mt_rand(-50, 50) / 10000),
                    'longitude' => $coords[1] + (mt_rand(-50, 50) / 10000),
                    'is_archive' => false,
                ]
            );

            foreach ($days as $day) {
                WorkingHours::updateOrCreate(
                    ['labo_id' => $lab->id, 'day' => $day, 'date_close' => null],
                    [
                        'start_time' => in_array($day, ['Saturday', 'Sunday']) ? null : '08:00:00',
                        'end_time' => in_array($day, ['Saturday', 'Sunday']) ? null : '17:00:00',
                        'is_closed' => in_array($day, ['Saturday', 'Sunday']),
                        'is_archive' => false,
                    ]
                );
            }

            $closures = [
                now()->addDays(5)->format('Y-m-d'),
                now()->addDays(12)->format('Y-m-d'),
            ];
            foreach ($closures as $date) {
                WorkingHours::updateOrCreate(
                    ['labo_id' => $lab->id, 'date_close' => $date],
                    ['day' => 'Jour de repos exceptionnel', 'is_closed' => true, 'is_archive' => false]
                );
            }
        }

        $consumables = [
            'Tubes Secs avec activateur de coagulation (bouchon rouge)' => 'flacons',
            'Tubes EDTA pour hematologie (bouchon violet)' => 'boites',
            'Tubes Citrate pour coagulation (bouchon bleu)' => 'boites',
            'Tubes Heparine pour biochimie (bouchon vert)' => 'boites',
            'Aiguilles de prelevement steriles (21G)' => 'unites',
            'Corps de prelevement sous vide (Holders)' => 'boites',
            "Tampons d'alcool isopropylique denature 70%" => 'boites',
            'Pansements adherents ronds post-prelevement' => 'boites',
            'Embouts de pipettes jetables neutres (100-1000 uL)' => 'sachets',
            'Plaques de microtitration transparentes ELISA' => 'boites',
            "Gants d'examen jetables en nitrile sans poudre (Taille M)" => 'boites',
            'Masques de protection chirurgicaux type II (3 plis)' => 'boites',
            'Gel hydroalcoolique desinfectant (Flacon pompe 500 mL)' => 'flacons',
            "Flacons de recueil d'urine steriles graduates (60 mL)" => 'boites',
            'Lames porte-objet en verre rodees blanches' => 'boites',
            'Lamelles couvre-objet carrees en verre 22x22mm' => 'boites',
            'Colorant liquide May-Grundwald Giemsa (MGG)' => 'flacons',
            'Ecouvillons nasopharynges steriles avec milieu de transport' => 'boites',
            'Embouts jetables pour analyseurs urinaires' => 'sachets',
            'Kit reactif liquide de dosage quantitatif de la CRP' => 'kits',
        ];

        $equipment = [
            ['name' => 'Centrifugeuse de paillasse universelle', 'type' => 'Preparation'],
            ['name' => "Automate d'Hematologie Laser 5 Populations", 'type' => 'Analyseur'],
            ['name' => 'Analyseur de Biochimie Clinique Automatique', 'type' => 'Analyseur'],
            ['name' => "Automate d'Immunologie Multiparametrique ELISA", 'type' => 'Analyseur'],
            ['name' => 'Analyseur d\'Urine Automatise par Reflectometrie', 'type' => 'Analyseur'],
            ['name' => 'Incubateur bacteriologique thermostaté a 37C', 'type' => 'Incubateur'],
            ['name' => "Autoclave de sterilisation a vapeur d'eau", 'type' => 'Sterilisateur'],
            ['name' => 'Microscope optique trinoculaire LED de recherche', 'type' => 'Optique'],
            ['name' => 'Agitateur de tubes vortex a vitesse variable', 'type' => 'Agitateur'],
            ['name' => 'Bain-marie de laboratoire avec regulateur de temperature', 'type' => 'Thermostatique'],
            ['name' => 'Refrigerateur de conservation medical ventile (+4C)', 'type' => 'Froid'],
            ['name' => 'Congelateur ultra-basse temperature a -80C', 'type' => 'Froid'],
            ['name' => 'Spectrophotometre UV-Visible double faisceau', 'type' => 'Spectroscopie'],
            ['name' => "Automate de Coagulation et d'Hemostase rapide", 'type' => 'Analyseur'],
            ['name' => 'Hotte de securite microbiologique (PSM Classe II)', 'type' => 'Securite'],
            ['name' => 'Balance analytique de precision homologuee', 'type' => 'Metrologie'],
            ['name' => 'Pipette automatique multicanaux electronique', 'type' => 'Pipetage'],
            ['name' => 'pH-metre de paillasse avec sonde de compensation', 'type' => 'Metrologie'],
            ['name' => 'Compteur de cellules sanguines manuel a clavier', 'type' => 'Compteur'],
            ['name' => 'Thermocycleur PCR en temps reel quantitatif (qPCR)', 'type' => 'Genetique'],
        ];

        $labs = Labo::where('is_archive', false)->get();

        foreach ($labs as $lab) {
            foreach ($consumables as $name => $unit) {
                Consumable::updateOrCreate(
                    ['labo_id' => $lab->id, 'name' => $name],
                    ['unit' => $unit, 'quantity' => rand(150, 500), 'min_quantity' => rand(15, 45), 'is_archive' => false]
                );
            }

            foreach ($equipment as $idx => $e) {
                Equipment::updateOrCreate(
                    ['labo_id' => $lab->id, 'name' => $e['name']],
                    [
                        'type' => $e['type'],
                        'serial_number' => 'SN-EQ-'.str_pad($lab->id, 2, '0', STR_PAD_LEFT).'-'.str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                        'status' => 'active', 'is_archive' => false,
                    ]
                );
            }
        }

        $this->command->info($labs->count().' labs with working hours, consumables, equipment seeded.');
    }
}
