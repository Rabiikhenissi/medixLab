<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $exams = [

            [
                'code' => 'NFS',
                'name' => 'Numération Formule Sanguine',
                'category' => 'hematology',
                'description' => 'Analyse des cellules sanguines permettant d’évaluer les globules rouges, globules blancs et plaquettes.',
                'default_normal_range' => 'Hémoglobine : Homme 13-17 g/dL, Femme 12-16 g/dL',
                'preparation_instructions' => 'Aucune préparation particulière nécessaire.',
            ],

            [
                'code' => 'GLYC',
                'name' => 'Glycémie à jeun',
                'category' => 'biochemistry',
                'description' => 'Mesure du taux de glucose dans le sang après une période de jeûne.',
                'default_normal_range' => '0,70 - 1,10 g/L',
                'preparation_instructions' => 'Être à jeun pendant 8 à 12 heures avant le prélèvement.',
            ],

            [
                'code' => 'HB1AC',
                'name' => 'Hémoglobine Glyquée (HbA1c)',
                'category' => 'biochemistry',
                'description' => 'Évalue la moyenne de la glycémie sur les 2 à 3 derniers mois.',
                'default_normal_range' => 'Inférieur à 6,5 %',
                'preparation_instructions' => 'Aucune préparation nécessaire.',
            ],

            [
                'code' => 'UREE',
                'name' => 'Urée sanguine',
                'category' => 'biochemistry',
                'description' => 'Évalue la fonction rénale et le métabolisme des protéines.',
                'default_normal_range' => '0,15 - 0,45 g/L',
                'preparation_instructions' => 'Prélèvement sanguin recommandé à jeun.',
            ],

            [
                'code' => 'CREAT',
                'name' => 'Créatinine sanguine',
                'category' => 'biochemistry',
                'description' => 'Permet d’évaluer la fonction des reins.',
                'default_normal_range' => 'Homme : 7-13 mg/L, Femme : 6-11 mg/L',
                'preparation_instructions' => 'Aucune préparation particulière.',
            ],

            [
                'code' => 'CRP',
                'name' => 'Protéine C Réactive (CRP)',
                'category' => 'immunology',
                'description' => 'Marqueur biologique utilisé pour détecter une inflammation.',
                'default_normal_range' => 'Inférieure à 6 mg/L',
                'preparation_instructions' => 'Aucune préparation nécessaire.',
            ],

            [
                'code' => 'VS',
                'name' => 'Vitesse de Sédimentation',
                'category' => 'hematology',
                'description' => 'Analyse permettant de rechercher un syndrome inflammatoire.',
                'default_normal_range' => 'Homme : <15 mm/h, Femme : <20 mm/h',
                'preparation_instructions' => 'Aucune préparation nécessaire.',
            ],

            [
                'code' => 'IONO',
                'name' => 'Ionogramme sanguin',
                'category' => 'biochemistry',
                'description' => 'Mesure des principaux ions du sang : sodium, potassium et chlore.',
                'default_normal_range' => 'Na+: 135-145 mmol/L, K+: 3,5-5 mmol/L',
                'preparation_instructions' => 'Prélèvement sanguin à jeun conseillé.',
            ],

            [
                'code' => 'TSH',
                'name' => 'Hormone Thyréostimulante (TSH)',
                'category' => 'biochemistry',
                'description' => 'Évalue le fonctionnement de la thyroïde.',
                'default_normal_range' => '0,4 - 4 mUI/L',
                'preparation_instructions' => 'Aucune préparation particulière.',
            ],

            [
                'code' => 'ECBU',
                'name' => 'Examen Cytobactériologique des Urines',
                'category' => 'urinalysis',
                'description' => 'Recherche une infection urinaire et identifie les bactéries responsables.',
                'default_normal_range' => 'Absence de germes pathogènes',
                'preparation_instructions' => 'Recueillir les urines du matin dans un flacon stérile.',
            ],

        ];

        foreach ($exams as $exam) {
            Exam::updateOrCreate(
                ['code' => $exam['code']],
                $exam
            );
        }
    }
}