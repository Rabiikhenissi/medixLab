<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamParameter;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $exams = [
            ['code' => 'NFS', 'name' => 'Numeration Formule Sanguine', 'category' => 'hematology', 'description' => 'Analyse des cellules sanguines permettant d\'evaluer les globules rouges, globules blancs et plaquettes.', 'default_normal_range' => 'Hemoglobine : Homme 13-17 g/dL, Femme 12-16 g/dL', 'preparation_instructions' => 'Aucune preparation particuliere necessaire.'],
            ['code' => 'GLYC', 'name' => 'Glycemie a jeun', 'category' => 'biochemistry', 'description' => 'Mesure du taux de glucose dans le sang apres une periode de jeûne.', 'default_normal_range' => '0,70 - 1,10 g/L', 'preparation_instructions' => 'Etre a jeûne pendant 8 a 12 heures avant le prelevement.'],
            ['code' => 'HB1AC', 'name' => 'Hemoglobine Glyquee (HbA1c)', 'category' => 'biochemistry', 'description' => 'Evalue la moyenne de la glycemie sur les 2 a 3 derniers mois.', 'default_normal_range' => 'Inferieur a 6,5 %', 'preparation_instructions' => 'Aucune preparation necessaire.'],
            ['code' => 'UREE', 'name' => 'Uree sanguine', 'category' => 'biochemistry', 'description' => 'Evalue la fonction renale et le metabolisme des proteines.', 'default_normal_range' => '0,15 - 0,45 g/L', 'preparation_instructions' => 'Prelevement sanguin recommande a jeun.'],
            ['code' => 'CREAT', 'name' => 'Creatinine sanguine', 'category' => 'biochemistry', 'description' => 'Permet d\'evaluer la fonction des reins.', 'default_normal_range' => 'Homme : 7-13 mg/L, Femme : 6-11 mg/L', 'preparation_instructions' => 'Aucune preparation particuliere.'],
            ['code' => 'CRP', 'name' => 'Proteine C Reactive (CRP)', 'category' => 'immunology', 'description' => 'Marqueur biologique utilise pour detecter une inflammation.', 'default_normal_range' => 'Inferieure a 6 mg/L', 'preparation_instructions' => 'Aucune preparation necessaire.'],
            ['code' => 'VS', 'name' => 'Vitesse de Sedimentation', 'category' => 'hematology', 'description' => 'Analyse permettant de rechercher un syndrome inflammatoire.', 'default_normal_range' => 'Homme : <15 mm/h, Femme : <20 mm/h', 'preparation_instructions' => 'Aucune preparation necessaire.'],
            ['code' => 'IONO', 'name' => 'Ionogramme sanguin', 'category' => 'biochemistry', 'description' => 'Mesure des principaux ions du sang : sodium, potassium et chlore.', 'default_normal_range' => 'Na+: 135-145 mmol/L, K+: 3,5-5 mmol/L', 'preparation_instructions' => 'Prelevement sanguin a jeun conseille.'],
            ['code' => 'TSH', 'name' => 'Hormone Thyreostimulante (TSH)', 'category' => 'biochemistry', 'description' => 'Evalue le fonctionnement de la thyroide.', 'default_normal_range' => '0,4 - 4 mUI/L', 'preparation_instructions' => 'Aucune preparation particuliere.'],
            ['code' => 'ECBU', 'name' => 'Examen Cytobacteriologique des Urines', 'category' => 'urinalysis', 'description' => 'Recherche une infection urinaire et identifie les bacteries responsables.', 'default_normal_range' => 'Absence de germes pathogenes', 'preparation_instructions' => 'Recueillir les urines du matin dans un flacon sterile.'],
            ['code' => 'LIPID', 'name' => 'Bilan Lipidique Complet', 'category' => 'biochemistry', 'description' => 'Mesure des fractions lipidiques pour l\'evaluation du risque cardiovasculaire.', 'default_normal_range' => 'Cholesterol Total : < 2.00 g/L, HDL : > 0.40 g/L', 'preparation_instructions' => 'Etre strictement a jeun depuis 12 heures.'],
            ['code' => 'ACUR', 'name' => 'Acide Urique Sanguin', 'category' => 'biochemistry', 'description' => 'Mesure de l\'acide urique pour le diagnostic de la goutte.', 'default_normal_range' => 'Homme : 35 - 70 mg/L, Femme : 25 - 60 mg/L', 'preparation_instructions' => 'Aucune preparation specifique requise.'],
            ['code' => 'AMY', 'name' => 'Amylasemie', 'category' => 'biochemistry', 'description' => 'Diagnostic et surveillance des affections du pancreas.', 'default_normal_range' => '28 - 100 UI/L', 'preparation_instructions' => 'Prelevement sanguin, pas de preparation requise.'],
            ['code' => 'BIL', 'name' => 'Bilirubine Sanguine', 'category' => 'biochemistry', 'description' => 'Exploration d\'un ictere ou d\'une pathologie hepato-biliaire.', 'default_normal_range' => 'Bilirubine Totale : < 12 mg/L', 'preparation_instructions' => 'Prelevement sanguin, etre a jeun est preferable.'],
            ['code' => 'TRANS', 'name' => 'Transaminases (SGOT et SGPT)', 'category' => 'biochemistry', 'description' => 'Enzymes intracellulaires pour le diagnostic des lesions hepatiques.', 'default_normal_range' => 'SGOT : < 40 UI/L, SGPT : < 45 UI/L', 'preparation_instructions' => 'Eviter les efforts physiques intenses 24h avant.'],
            ['code' => 'HEMOC', 'name' => 'Hemoculture', 'category' => 'microbiology', 'description' => 'Recherche de micro-organismes dans le sang.', 'default_normal_range' => 'Absence de croissance microbienne', 'preparation_instructions' => 'Prelevement au moment d\'un pic febrile sous asepsie.'],
            ['code' => 'CALCI', 'name' => 'Calcemie', 'category' => 'biochemistry', 'description' => 'Dosage du calcium pour l\'evaluation des fonctions parathyroidiennes.', 'default_normal_range' => '88 - 102 mg/L', 'preparation_instructions' => 'Prelevement de preference le matin a jeun.'],
            ['code' => 'FER', 'name' => 'Ferritinemie', 'category' => 'immunology', 'description' => 'Evalue les reserves en fer de l\'organisme.', 'default_normal_range' => 'Homme : 30 - 300 ug/L, Femme : 15 - 150 ug/L', 'preparation_instructions' => 'Aucune preparation requise.'],
            ['code' => 'VITD', 'name' => 'Dosage de la Vitamine D', 'category' => 'biochemistry', 'description' => 'Dosage permettant de depister une carence en vitamine D.', 'default_normal_range' => '30 - 100 ng/mL', 'preparation_instructions' => 'Prelevement sanguin standard.'],
            ['code' => 'PSA', 'name' => 'Antigene Prostatique Specifique (PSA)', 'category' => 'immunology', 'description' => 'Marqueur de choix dans le depistage du cancer de la prostate.', 'default_normal_range' => 'Inferieur a 4.0 ng/mL', 'preparation_instructions' => 'Eviter rapports sexuels et velo 48h avant.'],
            ['code' => 'PROT', 'name' => 'Proteines Totales Seriques', 'category' => 'biochemistry', 'description' => 'Mesure la concentration totale de proteines dans le serum.', 'default_normal_range' => '60 - 80 g/L', 'preparation_instructions' => 'Aucune preparation requise.'],
            ['code' => 'VITB12', 'name' => 'Dosage de la Vitamine B12', 'category' => 'biochemistry', 'description' => 'Recherche d\'une carence en vitamine B12.', 'default_normal_range' => '191 - 663 pmol/L', 'preparation_instructions' => 'Prelevement le matin a jeun de preference.'],
            ['code' => 'CLER', 'name' => 'Clairance de la Creatinine (DFG)', 'category' => 'biochemistry', 'description' => 'Examen cle pour estimer le debit de filtration renale.', 'default_normal_range' => 'Superieur a 90 mL/min/1.73m2', 'preparation_instructions' => 'Recueil des urines de 24h et prise de sang.'],
            ['code' => 'ASLO', 'name' => 'Anticorps Antistreptolysines O', 'category' => 'immunology', 'description' => 'Diagnostic des complications post-streptococciques.', 'default_normal_range' => 'Inferieur a 200 UI/mL', 'preparation_instructions' => 'Prelevement sanguin, pas de preparation particuliere.'],
            ['code' => 'TPINR', 'name' => 'Taux de Prothrombine / INR', 'category' => 'hematology', 'description' => 'Surveillance des traitements anticoagulants oraux.', 'default_normal_range' => 'TP : 70 - 100 %, INR : 1.0', 'preparation_instructions' => 'Indiquer tout traitement anticoagulant.'],
            ['code' => 'TCA', 'name' => 'Temps de Cephaline Active', 'category' => 'hematology', 'description' => 'Exploration globale de la coagulation sanguine.', 'default_normal_range' => 'Ratio patient / temoin : 0.80 - 1.20', 'preparation_instructions' => 'Indiquer les traitements medicamenteux en cours.'],
            ['code' => 'STREP', 'name' => 'Test Rapide Streptocoque A', 'category' => 'microbiology', 'description' => 'Mise en evidence de l\'antigene du streptocoque du groupe A.', 'default_normal_range' => 'Negatif', 'preparation_instructions' => 'Ne pas utiliser de bains de bouche avant le test.'],
            ['code' => 'COPRO', 'name' => 'Coproculture', 'category' => 'microbiology', 'description' => 'Recherche des principales bacteries pathogenes intestinales.', 'default_normal_range' => 'Absence de salmonelles, shigelles pathogenes', 'preparation_instructions' => 'Recueillir une selle fraîche dans un pot sterile.'],
            ['code' => 'FACTR', 'name' => 'Facteur Rhumatoid', 'category' => 'immunology', 'description' => 'Aide au diagnostic de la polyarthrite rhumatoid.', 'default_normal_range' => 'Inferieur a 14 UI/mL', 'preparation_instructions' => 'Aucune preparation particuliere requise.'],
            ['code' => 'LIPAS', 'name' => 'Lipasemie', 'category' => 'biochemistry', 'description' => 'Dosage enzymatique pour diagnostiquer une pancreatite aigue.', 'default_normal_range' => 'Inferieur a 60 UI/L', 'preparation_instructions' => 'Prelevement de preference a jeun.'],
        ];

        foreach ($exams as $e) {
            Exam::updateOrCreate(['code' => $e['code']], array_merge($e, ['is_archive' => false]));
        }

        $params = [
            'NFS' => [
                ['name' => 'Hemoglobine', 'unit' => 'g/dL', 'normal_range' => '13.0 - 17.0 (H) / 12.0 - 16.0 (F)'],
                ['name' => 'Hematocrite', 'unit' => '%', 'normal_range' => '40 - 54 (H) / 36 - 46 (F)'],
                ['name' => 'Leucocytes', 'unit' => 'G/L', 'normal_range' => '4.0 - 10.0'],
                ['name' => 'Plaquettes', 'unit' => 'G/L', 'normal_range' => '150 - 400'],
                ['name' => 'Neutrophiles', 'unit' => '%', 'normal_range' => '40 - 75'],
                ['name' => 'Lymphocytes', 'unit' => '%', 'normal_range' => '20 - 45'],
                ['name' => 'Monocytes', 'unit' => '%', 'normal_range' => '2 - 10'],
                ['name' => 'Eosinophiles', 'unit' => '%', 'normal_range' => '1 - 6'],
                ['name' => 'Basophiles', 'unit' => '%', 'normal_range' => '0 - 1'],
                ['name' => 'VGM', 'unit' => 'fL', 'normal_range' => '80 - 100'],
                ['name' => 'TCMH', 'unit' => 'pg', 'normal_range' => '27 - 33'],
                ['name' => 'CCP', 'unit' => 'g/dL', 'normal_range' => '32 - 36'],
            ],
            'GLYC' => [
                ['name' => 'Glycemie', 'unit' => 'g/L', 'normal_range' => '0.70 - 1.10'],
            ],
            'HB1AC' => [
                ['name' => 'HbA1c', 'unit' => '%', 'normal_range' => '< 5.7'],
                ['name' => 'Glucose moyen estime', 'unit' => 'g/L', 'normal_range' => '0.70 - 1.06'],
            ],
            'UREE' => [
                ['name' => 'Uree', 'unit' => 'g/L', 'normal_range' => '0.15 - 0.45'],
                ['name' => 'Azote ureique', 'unit' => 'g/L', 'normal_range' => '0.10 - 0.30'],
            ],
            'CREAT' => [
                ['name' => 'Creatinine', 'unit' => 'mg/L', 'normal_range' => '7 - 13 (H) / 6 - 11 (F)'],
                ['name' => 'DFG', 'unit' => 'mL/min', 'normal_range' => '> 90'],
                ['name' => 'Clairance creatinine', 'unit' => 'mL/min', 'normal_range' => '90 - 140'],
            ],
            'CRP' => [
                ['name' => 'CRP', 'unit' => 'mg/L', 'normal_range' => '0 - 6'],
            ],
            'VS' => [
                ['name' => 'VS 1ere heure', 'unit' => 'mm/h', 'normal_range' => '0 - 15 (H) / 0 - 20 (F)'],
                ['name' => 'VS 2eme heure', 'unit' => 'mm/h', 'normal_range' => '0 - 30 (H) / 0 - 40 (F)'],
            ],
            'IONO' => [
                ['name' => 'Sodium', 'unit' => 'mmol/L', 'normal_range' => '135 - 145'],
                ['name' => 'Potassium', 'unit' => 'mmol/L', 'normal_range' => '3.5 - 5.0'],
                ['name' => 'Chlorure', 'unit' => 'mmol/L', 'normal_range' => '96 - 106'],
                ['name' => 'Calcium', 'unit' => 'mmol/L', 'normal_range' => '2.20 - 2.60'],
                ['name' => 'Magnesium', 'unit' => 'mmol/L', 'normal_range' => '0.70 - 1.00'],
                ['name' => 'Bicarbonate', 'unit' => 'mmol/L', 'normal_range' => '22 - 29'],
                ['name' => 'Phosphore', 'unit' => 'mmol/L', 'normal_range' => '0.80 - 1.50'],
            ],
            'TSH' => [
                ['name' => 'TSH', 'unit' => 'mUI/L', 'normal_range' => '0.4 - 4.0'],
                ['name' => 'T4L', 'unit' => 'pmol/L', 'normal_range' => '12 - 22'],
                ['name' => 'T3L', 'unit' => 'pmol/L', 'normal_range' => '3.1 - 6.8'],
            ],
            'ECBU' => [
                ['name' => 'pH', 'unit' => '', 'normal_range' => '5.0 - 8.0'],
                ['name' => 'Densite', 'unit' => '', 'normal_range' => '1.005 - 1.030'],
                ['name' => 'Proteines', 'unit' => 'g/L', 'normal_range' => '0 - 0.15'],
                ['name' => 'Glucose', 'unit' => 'g/L', 'normal_range' => '0'],
                ['name' => 'Leucocytes', 'unit' => '/uL', 'normal_range' => '0 - 25'],
                ['name' => 'Erythrocytes', 'unit' => '/uL', 'normal_range' => '0 - 10'],
                ['name' => 'Nitrites', 'unit' => '', 'normal_range' => 'Negatif'],
                ['name' => 'Hematies', 'unit' => '/uL', 'normal_range' => '0 - 5'],
                ['name' => 'Bacteries', 'unit' => '', 'normal_range' => 'Absentes'],
                ['name' => 'Levures', 'unit' => '', 'normal_range' => 'Absentes'],
            ],
            'LIPID' => [
                ['name' => 'Cholesterol Total', 'unit' => 'g/L', 'normal_range' => '< 2.00'],
                ['name' => 'HDL Cholesterol', 'unit' => 'g/L', 'normal_range' => '> 0.40'],
                ['name' => 'LDL Cholesterol', 'unit' => 'g/L', 'normal_range' => '< 1.60'],
                ['name' => 'Trlycerides', 'unit' => 'g/L', 'normal_range' => '< 1.50'],
                ['name' => 'VLDL Cholesterol', 'unit' => 'g/L', 'normal_range' => '< 0.45'],
            ],
            'ACUR' => [
                ['name' => 'Acide Urique', 'unit' => 'g/L', 'normal_range' => '0.020 - 0.070'],
            ],
            'AMY' => [
                ['name' => 'Amylase', 'unit' => 'U/L', 'normal_range' => '30 - 110'],
                ['name' => 'Amylase salivaire', 'unit' => 'U/L', 'normal_range' => '15 - 60'],
            ],
            'BIL' => [
                ['name' => 'Bilirubine Totale', 'unit' => 'mg/L', 'normal_range' => '2.0 - 12.0'],
                ['name' => 'Bilirubine Directe', 'unit' => 'mg/L', 'normal_range' => '< 3.0'],
                ['name' => 'Bilirubine Indirecte', 'unit' => 'mg/L', 'normal_range' => '< 10.0'],
            ],
            'TRANS' => [
                ['name' => 'ASAT (SGOT)', 'unit' => 'U/L', 'normal_range' => '5 - 40'],
                ['name' => 'ALAT (SGPT)', 'unit' => 'U/L', 'normal_range' => '5 - 41'],
                ['name' => 'PAL', 'unit' => 'U/L', 'normal_range' => '44 - 147'],
                ['name' => 'GGT', 'unit' => 'U/L', 'normal_range' => '7 - 56'],
            ],
            'HEMOC' => [
                ['name' => 'Resultat culture', 'unit' => '', 'normal_range' => 'Negatif (pas de croissance)'],
                ['name' => 'Germes identifies', 'unit' => '', 'normal_range' => 'Non applicable'],
                ['name' => 'Antibiogramme', 'unit' => '', 'normal_range' => 'Non applicable'],
            ],
            'CALCI' => [
                ['name' => 'Calcium Total', 'unit' => 'mg/L', 'normal_range' => '85.0 - 105.0'],
                ['name' => 'Calcium Ionise', 'unit' => 'mmol/L', 'normal_range' => '1.15 - 1.30'],
            ],
            'FER' => [
                ['name' => 'Ferritine', 'unit' => 'ng/mL', 'normal_range' => '12 - 300'],
                ['name' => 'Fer Serique', 'unit' => 'ug/dL', 'normal_range' => '60 - 170'],
                ['name' => 'TIBC', 'unit' => 'ug/dL', 'normal_range' => '250 - 370'],
            ],
            'VITD' => [
                ['name' => '25-OH Vitamine D', 'unit' => 'ng/mL', 'normal_range' => '30.0 - 100.0'],
                ['name' => 'Vitamine D (statut)', 'unit' => '', 'normal_range' => 'Optimal: 30-100'],
            ],
            'PSA' => [
                ['name' => 'PSA Total', 'unit' => 'ng/mL', 'normal_range' => '< 4.00'],
                ['name' => 'PSA Libre', 'unit' => 'ng/mL', 'normal_range' => '< 0.80'],
                ['name' => 'Ratio PSA Libre/Total', 'unit' => '%', 'normal_range' => '> 25'],
            ],
            'PROT' => [
                ['name' => 'Proteines Totales', 'unit' => 'g/L', 'normal_range' => '60.0 - 80.0'],
                ['name' => 'Albumine', 'unit' => 'g/L', 'normal_range' => '35.0 - 50.0'],
                ['name' => 'Alpha-1 Globulines', 'unit' => 'g/L', 'normal_range' => '2.0 - 4.0'],
                ['name' => 'Alpha-2 Globulines', 'unit' => 'g/L', 'normal_range' => '5.0 - 10.0'],
                ['name' => 'Beta Globulines', 'unit' => 'g/L', 'normal_range' => '6.0 - 12.0'],
                ['name' => 'Gamma Globulines', 'unit' => 'g/L', 'normal_range' => '10.0 - 20.0'],
            ],
            'VITB12' => [
                ['name' => 'Vitamine B12', 'unit' => 'pg/mL', 'normal_range' => '200.0 - 900.0'],
                ['name' => 'Acide folique', 'unit' => 'ng/mL', 'normal_range' => '2.7 - 17.0'],
            ],
            'CLER' => [
                ['name' => 'DFG (CKD-EPI)', 'unit' => 'mL/min/1.73m2', 'normal_range' => '> 90'],
                ['name' => 'Creatinine', 'unit' => 'mg/L', 'normal_range' => '6.0 - 12.0'],
            ],
            'ASLO' => [
                ['name' => 'ASLO', 'unit' => 'UI/mL', 'normal_range' => '< 200'],
                ['name' => 'Titre ASLO', 'unit' => '', 'normal_range' => 'Negatif (< 200)'],
            ],
            'TPINR' => [
                ['name' => 'TP (Taux de Prothrombine)', 'unit' => '%', 'normal_range' => '70.0 - 100.0'],
                ['name' => 'INR', 'unit' => '', 'normal_range' => '0.85 - 1.15'],
            ],
            'TCA' => [
                ['name' => 'TCA', 'unit' => 'secondes', 'normal_range' => '30.0 - 40.0'],
                ['name' => 'Rapport TCA/Temoin', 'unit' => '', 'normal_range' => '0.85 - 1.15'],
            ],
            'STREP' => [
                ['name' => 'Resultat TDR Streptocoque A', 'unit' => '', 'normal_range' => 'Negatif'],
            ],
            'COPRO' => [
                ['name' => 'Coproculture', 'unit' => '', 'normal_range' => 'Flore saprophyte'],
                ['name' => 'Leucocytes fecaux', 'unit' => '/ champ', 'normal_range' => '< 5'],
                ['name' => 'Sang occulte', 'unit' => '', 'normal_range' => 'Negatif'],
            ],
            'FACTR' => [
                ['name' => 'Facteur Rhumatoid', 'unit' => 'UI/mL', 'normal_range' => '< 14.0'],
                ['name' => 'CRP associee', 'unit' => 'mg/L', 'normal_range' => '< 5.0'],
            ],
            'LIPAS' => [
                ['name' => 'Lipase', 'unit' => 'U/L', 'normal_range' => '13.0 - 60.0'],
            ],
        ];

        foreach ($params as $code => $examParams) {
            $exam = Exam::where('code', $code)->first();
            if (! $exam) {
                continue;
            }
            foreach ($examParams as $p) {
                ExamParameter::updateOrCreate(
                    ['exam_id' => $exam->id, 'name' => $p['name']],
                    ['unit' => $p['unit'], 'normal_range' => $p['normal_range'], 'is_archive' => false]
                );
            }
        }

        $this->command->info('30 exams and their parameters seeded.');
    }
}
