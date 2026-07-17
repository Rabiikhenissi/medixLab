<?php

namespace Database\Seeders;

use App\Models\Labo;
use App\Models\User;
use App\Models\Staff;
use App\Models\Group;
use App\Models\Consumable;
use App\Models\Equipment;
use App\Models\Exam;
use App\Models\AvailableExam;
use App\Models\ExamConsumable;
use App\Models\ExamEquipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FrenchLaboratoryDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get center group
        $centerGroup = Group::firstOrCreate(['code' => 'center'], ['name' => 'Medical Center']);

        // 1. 20 Realistic French Laboratories
        $labsData = [
            [
                'name' => 'Laboratoire d\'Analyses Médicales Bio-Renard',
                'address' => '15 Avenue de la République',
                'city' => 'Paris',
                'phone' => '+33142005511',
                'email' => 'contact@biorenard-paris.fr',
                'responsible_first' => 'Pierre',
                'responsible_last' => 'Renard',
            ],
            [
                'name' => 'Laboratoire de Biologie Clinique de la Gare',
                'address' => '4 Place Bellecour',
                'city' => 'Lyon',
                'phone' => '+33472008822',
                'email' => 'accueil@biogare-lyon.fr',
                'responsible_first' => 'Marie',
                'responsible_last' => 'Dupond',
            ],
            [
                'name' => 'Centre de Biologie Médicale Prado-Mermoz',
                'address' => '250 Avenue du Prado',
                'city' => 'Marseille',
                'phone' => '+33491003344',
                'email' => 'contact@pradomermoz-bio.fr',
                'responsible_first' => 'Jean',
                'responsible_last' => 'Mermoz',
            ],
            [
                'name' => 'Laboratoire Bio-Esterel',
                'address' => '12 Promenade des Anglais',
                'city' => 'Nice',
                'phone' => '+33493006655',
                'email' => 'contact@bioesterel-nice.fr',
                'responsible_first' => 'Sophie',
                'responsible_last' => 'Vidal',
            ],
            [
                'name' => 'Laboratoire Médical de la Place',
                'address' => '8 Rue Lafayette',
                'city' => 'Toulouse',
                'phone' => '+33561009900',
                'email' => 'secretariat@bioplace-toulouse.fr',
                'responsible_first' => 'Marc',
                'responsible_last' => 'Garrigues',
            ],
            [
                'name' => 'Laboratoire d\'Analyses de l\'Europe',
                'address' => '32 Boulevard d\'Europe',
                'city' => 'Strasbourg',
                'phone' => '+33388002233',
                'email' => 'contact@bioeurope-stras.fr',
                'responsible_first' => 'Elisabeth',
                'responsible_last' => 'Weber',
            ],
            [
                'name' => 'Laboratoire de Biologie Saint-Antoine',
                'address' => '45 Rue Saint-Antoine',
                'city' => 'Bordeaux',
                'phone' => '+33556004455',
                'email' => 'accueil@biosaintantoine.fr',
                'responsible_first' => 'Antoine',
                'responsible_last' => 'Lafont',
            ],
            [
                'name' => 'Centre de Diagnostic Médical du Nord',
                'address' => '88 Rue Nationale',
                'city' => 'Lille',
                'phone' => '+33320005566',
                'email' => 'contact@cdmnord-lille.fr',
                'responsible_first' => 'Charles',
                'responsible_last' => 'Devos',
            ],
            [
                'name' => 'Laboratoire d\'Analyses du Grand-Canyon',
                'address' => '14 Rue Crébillon',
                'city' => 'Nantes',
                'phone' => '+33240003322',
                'email' => 'contact@biocanyon-nantes.fr',
                'responsible_first' => 'Juliette',
                'responsible_last' => 'Moreau',
            ],
            [
                'name' => 'Laboratoire de l\'Horloge',
                'address' => '5 Rue Jeanne d\'Arc',
                'city' => 'Rouen',
                'phone' => '+33235008899',
                'email' => 'contact@biohorloge-rouen.fr',
                'responsible_first' => 'Guillaume',
                'responsible_last' => 'Leroy',
            ],
            [
                'name' => 'Laboratoire Bio-Avenir',
                'address' => '20 Avenue de Lodève',
                'city' => 'Montpellier',
                'phone' => '+33467007788',
                'email' => 'contact@bioavenir-montpellier.fr',
                'responsible_first' => 'Aurore',
                'responsible_last' => 'Dumont',
            ],
            [
                'name' => 'Laboratoire de Biologie Centre-Loire',
                'address' => '55 Rue Royale',
                'city' => 'Orléans',
                'phone' => '+33238001122',
                'email' => 'contact@biocentreloire.fr',
                'responsible_first' => 'François',
                'responsible_last' => 'Boucher',
            ],
            [
                'name' => 'Laboratoire d\'Analyses des Alpes',
                'address' => '18 Avenue Alsace-Lorraine',
                'city' => 'Grenoble',
                'phone' => '+33476003344',
                'email' => 'contact@bioalpes-grenoble.fr',
                'responsible_first' => 'Lucie',
                'responsible_last' => 'Garnier',
            ],
            [
                'name' => 'Laboratoire de Biologie Médicale Aquitaine',
                'address' => '3 Avenue de la Marne',
                'city' => 'Biarritz',
                'phone' => '+33559002211',
                'email' => 'contact@bioaquitaine-biarritz.fr',
                'responsible_first' => 'Xavier',
                'responsible_last' => 'Etcheverry',
            ],
            [
                'name' => 'Centre d\'Analyses Médicales de l\'Est',
                'address' => '10 Place Stanislas',
                'city' => 'Nancy',
                'phone' => '+33383005544',
                'email' => 'contact@camest-nancy.fr',
                'responsible_first' => 'Stéphane',
                'responsible_last' => 'Marchal',
            ],
            [
                'name' => 'Laboratoire de Biologie du Val-de-Marne',
                'address' => '24 Avenue du Général de Gaulle',
                'city' => 'Créteil',
                'phone' => '+33149008877',
                'email' => 'contact@bioval-creteil.fr',
                'responsible_first' => 'Hélène',
                'responsible_last' => 'Petit',
            ],
            [
                'name' => 'Laboratoire d\'Analyses Médicales Saint-Michel',
                'address' => '14 Boulevard de la Liberté',
                'city' => 'Rennes',
                'phone' => '+33299004455',
                'email' => 'contact@biosaintmichel-rennes.fr',
                'responsible_first' => 'Michel',
                'responsible_last' => 'Le Gall',
            ],
            [
                'name' => 'Laboratoire de Biologie Clinique d\'Armor',
                'address' => '8 Rue de Siam',
                'city' => 'Brest',
                'phone' => '+33298007788',
                'email' => 'contact@bioarmor-brest.fr',
                'responsible_first' => 'Yann',
                'responsible_last' => 'Kerebel',
            ],
            [
                'name' => 'Centre de Pathologie et Biologie',
                'address' => '30 Rue Nationale',
                'city' => 'Tours',
                'phone' => '+33247009988',
                'email' => 'contact@biopath-tours.fr',
                'responsible_first' => 'Chantal',
                'responsible_last' => 'Rousseau',
            ],
            [
                'name' => 'Laboratoire d\'Analyses Médicales des Volcans',
                'address' => '12 Place de Jaude',
                'city' => 'Clermont-Ferrand',
                'phone' => '+33473001122',
                'email' => 'contact@biovolcans-clermont.fr',
                'responsible_first' => 'Gerard',
                'responsible_last' => 'Faure',
            ],
        ];

        $createdLabs = [];
        foreach ($labsData as $index => $l) {
            $lab = Labo::updateOrCreate(
                ['email' => $l['email']],
                [
                    'name' => $l['name'],
                    'address' => $l['address'],
                    'city' => $l['city'],
                    'phone' => $l['phone'],
                ]
            );

            $createdLabs[] = $lab;

            // Create corresponding user for lab login
            $user = User::updateOrCreate(
                ['email' => $l['email']],
                [
                    'first_name' => $l['responsible_first'],
                    'last_name' => $l['responsible_last'],
                    'phone' => $l['phone'],
                    'address' => $l['address'] . ', ' . $l['city'],
                    'password' => Hash::make('password123'),
                    'group_id' => $centerGroup->id,
                    'is_archive' => false,
                ]
            );

            // Create corresponding staff record
            Staff::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'laboratory_id' => $lab->id,
                    'staff_code' => 'STF-FR-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'is_archive' => false,
                ]
            );

            // Seed regular working hours for this laboratory
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day) {
                \App\Models\WorkingHours::updateOrCreate(
                    [
                        'labo_id' => $lab->id,
                        'day' => $day,
                        'date_close' => null
                    ],
                    [
                        'start_time' => ($day === 'Sunday' || $day === 'Saturday') ? null : '08:00:00',
                        'end_time' => ($day === 'Sunday' || $day === 'Saturday') ? null : '17:00:00',
                        'is_closed' => ($day === 'Sunday' || $day === 'Saturday') ? true : false,
                        'is_archive' => false
                    ]
                );
            }

            // Seed a couple of exception rest days for demonstration (e.g. 5 days from now and 12 days from now)
            $closures = [
                ['date' => now()->addDays(5)->format('Y-m-d'), 'reason' => 'Jour de repos exceptionnel'],
                ['date' => now()->addDays(12)->format('Y-m-d'), 'reason' => 'Maintenance technique annuelle'],
            ];
            foreach ($closures as $c) {
                \App\Models\WorkingHours::updateOrCreate(
                    [
                        'labo_id' => $lab->id,
                        'date_close' => $c['date']
                    ],
                    [
                        'day' => $c['reason'],
                        'is_closed' => true,
                        'is_archive' => false
                    ]
                );
            }
        }

        // 2. 20 Realistic French Medical Consumables
        $consumablesTemplate = [
            ['name' => 'Tubes Secs avec activateur de coagulation (bouchon rouge)', 'unit' => 'flacons'],
            ['name' => 'Tubes EDTA pour hématologie (bouchon violet)', 'unit' => 'boîtes'],
            ['name' => 'Tubes Citrate pour coagulation (bouchon bleu)', 'unit' => 'boîtes'],
            ['name' => 'Tubes Héparine pour biochimie (bouchon vert)', 'unit' => 'boîtes'],
            ['name' => 'Aiguilles de prélèvement stériles (21G)', 'unit' => 'unités'],
            ['name' => 'Corps de prélèvement sous vide (Holders)', 'unit' => 'boîtes'],
            ['name' => 'Tampons d\'alcool isopropylique dénaturé 70%', 'unit' => 'boîtes'],
            ['name' => 'Pansements adhésifs ronds post-prélèvement', 'unit' => 'boîtes'],
            ['name' => 'Embouts de pipettes jetables neutres (100-1000 µL)', 'unit' => 'sachets'],
            ['name' => 'Plaques de microtitration transparentes ELISA', 'unit' => 'boîtes'],
            ['name' => 'Gants d\'examen jetables en nitrile sans poudre (Taille M)', 'unit' => 'boîtes'],
            ['name' => 'Masques de protection chirurgicaux type II (3 plis)', 'unit' => 'boîtes'],
            ['name' => 'Gel hydroalcoolique désinfectant (Flacon pompe 500 mL)', 'unit' => 'flacons'],
            ['name' => 'Flacons de recueil d\'urine stériles gradués (60 mL)', 'unit' => 'boîtes'],
            ['name' => 'Lames porte-objet en verre rodées blanches', 'unit' => 'boîtes'],
            ['name' => 'Lamelles couvre-objet carrées en verre 22x22mm', 'unit' => 'boîtes'],
            ['name' => 'Colorant liquide May-Grünwald Giemsa (MGG)', 'unit' => 'flacons'],
            ['name' => 'Ecouvillons nasopharyngés stériles avec milieu de transport', 'unit' => 'boîtes'],
            ['name' => 'Embouts jetables pour analyseurs urinaires', 'unit' => 'sachets'],
            ['name' => 'Kit réactif liquide de dosage quantitatif de la CRP', 'unit' => 'kits'],
        ];

        // Seed consumables for ALL laboratories
        $allConsumables = [];
        foreach ($createdLabs as $lab) {
            foreach ($consumablesTemplate as $c) {
                $consumable = Consumable::updateOrCreate(
                    [
                        'labo_id' => $lab->id,
                        'name' => $c['name']
                    ],
                    [
                        'unit' => $c['unit'],
                        'quantity' => rand(150, 500),
                        'min_quantity' => rand(15, 45),
                        'is_archive' => false,
                    ]
                );
                $allConsumables[$lab->id][$c['name']] = $consumable;
            }
        }

        // 3. 20 Realistic French Medical Equipment/Machines
        $equipmentTemplate = [
            ['name' => 'Centrifugeuse de paillasse universelle', 'type' => 'Préparation'],
            ['name' => 'Automate d\'Hématologie Laser 5 Populations', 'type' => 'Analyseur'],
            ['name' => 'Analyseur de Biochimie Clinique Automatique', 'type' => 'Analyseur'],
            ['name' => 'Automate d\'Immunologie Multiparamétrique ELISA', 'type' => 'Analyseur'],
            ['name' => 'Analyseur d\'Urine Automatisé par Réflectométrie', 'type' => 'Analyseur'],
            ['name' => 'Incubateur bactériologique thermostaté à 37°C', 'type' => 'Incubateur'],
            ['name' => 'Autoclave de stérilisation à vapeur d\'eau', 'type' => 'Stérilisateur'],
            ['name' => 'Microscope optique trinoculaire LED de recherche', 'type' => 'Optique'],
            ['name' => 'Agitateur de tubes vortex à vitesse variable', 'type' => 'Agitateur'],
            ['name' => 'Bain-marie de laboratoire avec régulateur de température', 'type' => 'Thermostatique'],
            ['name' => 'Réfrigérateur de conservation médical ventilé (+4°C)', 'type' => 'Froid'],
            ['name' => 'Congélateur ultra-basse température à -80°C', 'type' => 'Froid'],
            ['name' => 'Spectrophotomètre UV-Visible double faisceau', 'type' => 'Spectroscopie'],
            ['name' => 'Automate de Coagulation et d\'Hémostase rapide', 'type' => 'Analyseur'],
            ['name' => 'Hotte de sécurité microbiologique (PSM Classe II)', 'type' => 'Sécurité'],
            ['name' => 'Balance analytique de précision homologuée', 'type' => 'Métrologie'],
            ['name' => 'Pipette automatique multicanaux électronique', 'type' => 'Pipetage'],
            ['name' => 'pH-mètre de paillasse avec sonde de compensation', 'type' => 'Métrologie'],
            ['name' => 'Compteur de cellules sanguines manuel à clavier', 'type' => 'Compteur'],
            ['name' => 'Thermocycleur PCR en temps réel quantitatif (qPCR)', 'type' => 'Génétique'],
        ];

        // Seed equipment for ALL laboratories
        $allEquipment = [];
        foreach ($createdLabs as $lab) {
            foreach ($equipmentTemplate as $idx => $e) {
                $equip = Equipment::updateOrCreate(
                    [
                        'labo_id' => $lab->id,
                        'name' => $e['name']
                    ],
                    [
                        'type' => $e['type'],
                        'serial_number' => 'SN-EQ-' . str_pad($lab->id, 2, '0', STR_PAD_LEFT) . '-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                        'status' => 'active',
                        'is_archive' => false,
                    ]
                );
                $allEquipment[$lab->id][$e['name']] = $equip;
            }
        }

        // 4. 20 Realistic French Medical Exams
        $examsData = [
            [
                'code' => 'LIPID',
                'name' => 'Bilan Lipidique Complet (Cholestérol, HDL, LDL, Triglycérides)',
                'category' => 'biochemistry',
                'description' => 'Mesure des fractions lipidiques pour l\'évaluation du risque cardiovasculaire.',
                'default_normal_range' => 'Cholestérol Total : < 2.00 g/L, HDL : > 0.40 g/L, LDL : < 1.30 g/L, Triglycérides : < 1.50 g/L',
                'preparation_instructions' => 'Être strictement à jeun depuis 12 heures (eau plate autorisée).',
            ],
            [
                'code' => 'ACUR',
                'name' => 'Acide Urique Sanguin (Uricémie)',
                'category' => 'biochemistry',
                'description' => 'Mesure de l\'acide urique pour le diagnostic de la goutte et le suivi rénal.',
                'default_normal_range' => 'Homme : 35 - 70 mg/L, Femme : 25 - 60 mg/L',
                'preparation_instructions' => 'Aucune préparation spécifique requise.',
            ],
            [
                'code' => 'AMY',
                'name' => 'Amylasémie (Amylase plasmatique)',
                'category' => 'biochemistry',
                'description' => 'Diagnostic et surveillance des affections du pancréas.',
                'default_normal_range' => '28 - 100 UI/L',
                'preparation_instructions' => 'Prélèvement sanguin, pas de préparation requise.',
            ],
            [
                'code' => 'BIL',
                'name' => 'Bilirubine Sanguine Totale et Fractionnée',
                'category' => 'biochemistry',
                'description' => 'Exploration d\'un ictère (jaunisse) ou d\'une pathologie hépato-biliaire.',
                'default_normal_range' => 'Bilirubine Totale : < 12 mg/L, Bilirubine Directe : < 2 mg/L',
                'preparation_instructions' => 'Prélèvement sanguin, être à jeun est préférable.',
            ],
            [
                'code' => 'TRANS',
                'name' => 'Transaminases (SGOT et SGPT)',
                'category' => 'biochemistry',
                'description' => 'Enzymes intracellulaires permettant le diagnostic des lésions cellulaires hépatiques.',
                'default_normal_range' => 'SGOT (ASAT) : < 40 UI/L, SGPT (ALAT) : < 45 UI/L',
                'preparation_instructions' => 'Éviter les efforts physiques intenses dans les 24 heures précédant le test.',
            ],
            [
                'code' => 'HEMOC',
                'name' => 'Hémoculture (Mise en culture du sang)',
                'category' => 'microbiology',
                'description' => 'Recherche de micro-organismes pathogènes (bactéries ou levures) dans le sang.',
                'default_normal_range' => 'Absence de croissance microbienne après 5 jours d\'incubation',
                'preparation_instructions' => 'Prélèvement à réaliser au moment d\'un pic fébrile ou de frissons sous asepsie rigoureuse.',
            ],
            [
                'code' => 'CALCI',
                'name' => 'Calcémie (Calcium sérique)',
                'category' => 'biochemistry',
                'description' => 'Dosage du calcium pour l\'évaluation des fonctions parathyroïdiennes et osseuses.',
                'default_normal_range' => '88 - 102 mg/L (2,20 - 2,55 mmol/L)',
                'preparation_instructions' => 'Prélèvement de préférence le matin à jeun.',
            ],
            [
                'code' => 'FER',
                'name' => 'Ferritinémie (Ferritine sérique)',
                'category' => 'immunology',
                'description' => 'Évalue les réserves en fer de l\'organisme.',
                'default_normal_range' => 'Homme : 30 - 300 µg/L, Femme : 15 - 150 µg/L',
                'preparation_instructions' => 'Aucune préparation requise.',
            ],
            [
                'code' => 'VITD',
                'name' => 'Dosage de la Vitamine D (25-OH Vitamine D)',
                'category' => 'biochemistry',
                'description' => 'Dosage permettant de dépister une carence ou une insuffisance en vitamine D.',
                'default_normal_range' => '30 - 100 ng/mL (Valeur optimale)',
                'preparation_instructions' => 'Prélèvement sanguin standard, pas de préparation particulière.',
            ],
            [
                'code' => 'PSA',
                'name' => 'Antigène Prostatique Spécifique (PSA Total)',
                'category' => 'immunology',
                'description' => 'Marqueur de choix dans le dépistage et le suivi du cancer de la prostate.',
                'default_normal_range' => 'Inférieur à 4.0 ng/mL',
                'preparation_instructions' => 'Éviter les rapports sexuels, le vélo ou un examen rectal 48h avant le test.',
            ],
            [
                'code' => 'PROT',
                'name' => 'Protéines Totales Sériques (Protidémie)',
                'category' => 'biochemistry',
                'description' => 'Mesure la concentration totale de protéines dans le sérum.',
                'default_normal_range' => '60 - 80 g/L',
                'preparation_instructions' => 'Aucune préparation requise.',
            ],
            [
                'code' => 'VITB12',
                'name' => 'Dosage de la Vitamine B12',
                'category' => 'biochemistry',
                'description' => 'Recherche d\'une carence en vitamine B12 (anémie, troubles neurologiques).',
                'default_normal_range' => '191 - 663 pmol/L',
                'preparation_instructions' => 'Prélèvement le matin à jeun de préférence.',
            ],
            [
                'code' => 'CLER',
                'name' => 'Clairance de la Créatinine (Estimation du DFG)',
                'category' => 'biochemistry',
                'description' => 'Examen clé pour estimer avec précision le débit de filtration rénale.',
                'default_normal_range' => 'Supérieur à 90 mL/min/1,73 m²',
                'preparation_instructions' => 'Nécessite le recueil de la totalité des urines de 24h et une prise de sang.',
            ],
            [
                'code' => 'ASLO',
                'name' => 'Anticorps Antistreptolysines O (ASLO)',
                'category' => 'immunology',
                'description' => 'Diagnostic des complications post-streptococciques.',
                'default_normal_range' => 'Inférieur à 200 UI/mL',
                'preparation_instructions' => 'Prélèvement sanguin, pas de préparation particulière.',
            ],
            [
                'code' => 'TPINR',
                'name' => 'Taux de Prothrombine (TP) / INR',
                'category' => 'hematology',
                'description' => 'Surveillance des traitements anticoagulants oraux (AVK) et bilan de coagulation.',
                'default_normal_range' => 'TP : 70 - 100 %, INR : 1.0 (Hors traitement anticoagulant)',
                'preparation_instructions' => 'Indiquer tout traitement anticoagulant ou trouble de la coagulation.',
            ],
            [
                'code' => 'TCA',
                'name' => 'Temps de Céphaline Activé (TCA)',
                'category' => 'hematology',
                'description' => 'Exploration globale de la coagulation sanguine (voie endogène).',
                'default_normal_range' => 'Ratio patient / témoin : 0.80 - 1.20',
                'preparation_instructions' => 'Indiquer les traitements médicamenteux en cours.',
            ],
            [
                'code' => 'STREP',
                'name' => 'Test de Diagnostic Rapide du Streptocoque A (TDR Gorge)',
                'category' => 'microbiology',
                'description' => 'Mise en évidence de l\'antigène du streptocoque du groupe A sur écouvillon de gorge.',
                'default_normal_range' => 'Négatif',
                'preparation_instructions' => 'Ne pas utiliser de bains de bouche ou de traitement antibiotique local avant le test.',
            ],
            [
                'code' => 'COPRO',
                'name' => 'Coproculture (Examen bactériologique des selles)',
                'category' => 'microbiology',
                'description' => 'Recherche et identification des principales bactéries pathogènes intestinales.',
                'default_normal_range' => 'Absence de salmonelles, shigelles, yersinia ou campylobacter pathogènes',
                'preparation_instructions' => 'Recueillir une selle fraîche dans un pot stérile adapté.',
            ],
            [
                'code' => 'FACTR',
                'name' => 'Facteur Rhumatoïde (Recherche et Dosage)',
                'category' => 'immunology',
                'description' => 'Aide au diagnostic de la polyarthrite rhumatoïde.',
                'default_normal_range' => 'Inférieur à 14 UI/mL',
                'preparation_instructions' => 'Aucune préparation particulière requise.',
            ],
            [
                'code' => 'LIPAS',
                'name' => 'Lipasémie (Lipase sérique)',
                'category' => 'biochemistry',
                'description' => 'Dosage enzymatique pour diagnostiquer une pancréatite aiguë.',
                'default_normal_range' => 'Inférieur à 60 UI/L',
                'preparation_instructions' => 'Prélèvement de préférence à jeun.',
            ],
        ];

        $createdExams = [];
        foreach ($examsData as $e) {
            $exam = Exam::updateOrCreate(
                ['code' => $e['code']],
                [
                    'name' => $e['name'],
                    'category' => $e['category'],
                    'description' => $e['description'],
                    'default_normal_range' => $e['default_normal_range'],
                    'preparation_instructions' => $e['preparation_instructions'],
                    'is_archive' => false,
                ]
            );
            $createdExams[] = $exam;
        }

        // 5. Intersecting Tables Seeding
        
        // Link all 20 new exams to all 20 new laboratories in AvailableExam table
        foreach ($createdLabs as $lab) {
            foreach ($createdExams as $exam) {
                AvailableExam::updateOrCreate(
                    [
                        'labo_id' => $lab->id,
                        'exam_id' => $exam->id
                    ],
                    [
                        'price' => rand(15, 120) + (rand(0, 99) / 100),
                        'is_active' => true,
                        'is_archive' => false,
                    ]
                );
            }
        }

        // Define which consumables & equipment each exam needs (Global presets mapping to specific items in each lab)
        // Since examConsumables points to a specific lab's consumable, let's map them for all labs!
        foreach ($createdLabs as $lab) {
            
            // Map for LIPID: needs Tubes Secs (bouchon rouge), Embouts de pipettes, uses Centrifugeuse & Analyseur Biochimie
            $cSecs = $allConsumables[$lab->id]['Tubes Secs avec activateur de coagulation (bouchon rouge)'] ?? null;
            $cEmbouts = $allConsumables[$lab->id]['Embouts de pipettes jetables neutres (100-1000 µL)'] ?? null;
            $eCentrifuge = $allEquipment[$lab->id]['Centrifugeuse de paillasse universelle'] ?? null;
            $eBioch = $allEquipment[$lab->id]['Analyseur de Biochimie Clinique Automatique'] ?? null;
            $examLipid = Exam::where('code', 'LIPID')->first();
            
            if ($examLipid && $cSecs && $cEmbouts && $eCentrifuge && $eBioch) {
                ExamConsumable::updateOrCreate(
                    ['exam_id' => $examLipid->id, 'consumable_id' => $cSecs->id],
                    ['quantity_needed' => 1, 'is_archive' => false]
                );
                ExamConsumable::updateOrCreate(
                    ['exam_id' => $examLipid->id, 'consumable_id' => $cEmbouts->id],
                    ['quantity_needed' => 3, 'is_archive' => false]
                );
                ExamEquipment::updateOrCreate(
                    ['exam_id' => $examLipid->id, 'equipment_id' => $eCentrifuge->id],
                    ['is_archive' => false]
                );
                ExamEquipment::updateOrCreate(
                    ['exam_id' => $examLipid->id, 'equipment_id' => $eBioch->id],
                    ['is_archive' => false]
                );
            }

            // Map for ACUR: needs Tubes Secs, Embouts pipettes, uses Centrifugeuse & Biochem
            $examAcur = Exam::where('code', 'ACUR')->first();
            if ($examAcur && $cSecs && $cEmbouts && $eCentrifuge && $eBioch) {
                ExamConsumable::updateOrCreate(
                    ['exam_id' => $examAcur->id, 'consumable_id' => $cSecs->id],
                    ['quantity_needed' => 1, 'is_archive' => false]
                );
                ExamConsumable::updateOrCreate(
                    ['exam_id' => $examAcur->id, 'consumable_id' => $cEmbouts->id],
                    ['quantity_needed' => 2, 'is_archive' => false]
                );
                ExamEquipment::updateOrCreate(
                    ['exam_id' => $examAcur->id, 'equipment_id' => $eCentrifuge->id],
                    ['is_archive' => false]
                );
                ExamEquipment::updateOrCreate(
                    ['exam_id' => $examAcur->id, 'equipment_id' => $eBioch->id],
                    ['is_archive' => false]
                );
            }

            // Map for TPINR: needs Tubes Citrate (bouchon bleu), uses Automate de Coagulation
            $cCitrate = $allConsumables[$lab->id]['Tubes Citrate pour coagulation (bouchon bleu)'] ?? null;
            $eCoag = $allEquipment[$lab->id]['Automate de Coagulation et d\'Hémostase rapide'] ?? null;
            $examTpinr = Exam::where('code', 'TPINR')->first();
            
            if ($examTpinr && $cCitrate && $eCoag) {
                ExamConsumable::updateOrCreate(
                    ['exam_id' => $examTpinr->id, 'consumable_id' => $cCitrate->id],
                    ['quantity_needed' => 1, 'is_archive' => false]
                );
                ExamEquipment::updateOrCreate(
                    ['exam_id' => $examTpinr->id, 'equipment_id' => $eCoag->id],
                    ['is_archive' => false]
                );
            }

            // Map for HEMOC: needs Flacons recueil urine/hemoc, uses Incubateur
            $cFlacons = $allConsumables[$lab->id]['Flacons de recueil d\'urine stériles gradués (60 mL)'] ?? null;
            $eIncub = $allEquipment[$lab->id]['Incubateur bactériologique thermostaté à 37°C'] ?? null;
            $examHemoc = Exam::where('code', 'HEMOC')->first();
            
            if ($examHemoc && $cFlacons && $eIncub) {
                ExamConsumable::updateOrCreate(
                    ['exam_id' => $examHemoc->id, 'consumable_id' => $cFlacons->id],
                    ['quantity_needed' => 2, 'is_archive' => false]
                );
                ExamEquipment::updateOrCreate(
                    ['exam_id' => $examHemoc->id, 'equipment_id' => $eIncub->id],
                    ['is_archive' => false]
                );
            }

            // Map for STREP: needs Ecouvillons, uses microscope
            $cEcouvillons = $allConsumables[$lab->id]['Ecouvillons nasopharyngés stériles avec milieu de transport'] ?? null;
            $eMicroscope = $allEquipment[$lab->id]['Microscope optique trinoculaire LED de recherche'] ?? null;
            $examStrep = Exam::where('code', 'STREP')->first();

            if ($examStrep && $cEcouvillons && $eMicroscope) {
                ExamConsumable::updateOrCreate(
                    ['exam_id' => $examStrep->id, 'consumable_id' => $cEcouvillons->id],
                    ['quantity_needed' => 1, 'is_archive' => false]
                );
                ExamEquipment::updateOrCreate(
                    ['exam_id' => $examStrep->id, 'equipment_id' => $eMicroscope->id],
                    ['is_archive' => false]
                );
            }
        }
    }
}
