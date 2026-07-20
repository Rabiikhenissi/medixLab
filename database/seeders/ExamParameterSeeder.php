<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamParameter;
use Illuminate\Database\Seeder;

class ExamParameterSeeder extends Seeder
{
    public function run(): void
    {
        $parameters = [
            'NFS' => [
                ['name' => 'Hémoglobine',      'unit' => 'g/dL',  'normal_range' => '13.0 - 17.0 (H) / 12.0 - 16.0 (F)'],
                ['name' => 'Hématocrite',        'unit' => '%',     'normal_range' => '40 - 54 (H) / 36 - 46 (F)'],
                ['name' => 'Leucocytes',         'unit' => 'G/L',   'normal_range' => '4.0 - 10.0'],
                ['name' => 'Plaquettes',         'unit' => 'G/L',   'normal_range' => '150 - 400'],
                ['name' => 'Neutrophiles',       'unit' => '%',     'normal_range' => '40 - 75'],
                ['name' => 'Lymphocytes',        'unit' => '%',     'normal_range' => '20 - 45'],
                ['name' => 'Monocytes',          'unit' => '%',     'normal_range' => '2 - 10'],
                ['name' => 'Éosinophiles',       'unit' => '%',     'normal_range' => '1 - 6'],
                ['name' => 'Basophiles',         'unit' => '%',     'normal_range' => '0 - 1'],
                ['name' => 'VGM',                'unit' => 'fL',    'normal_range' => '80 - 100'],
                ['name' => 'TCMH',               'unit' => 'pg',    'normal_range' => '27 - 33'],
                ['name' => 'CCP',                'unit' => 'g/dL',  'normal_range' => '32 - 36'],
            ],
            'GLYC' => [
                ['name' => 'Glycémie',           'unit' => 'g/L',   'normal_range' => '0.70 - 1.10'],
            ],
            'HB1AC' => [
                ['name' => 'HbA1c',              'unit' => '%',     'normal_range' => '4.0 - 5.7'],
                ['name' => 'Estimation moyenne glycémique', 'unit' => 'g/L', 'normal_range' => '0.70 - 1.30'],
            ],
            'UREE' => [
                ['name' => 'Urée',               'unit' => 'g/L',   'normal_range' => '0.15 - 0.45'],
                ['name' => 'Azote uréique',      'unit' => 'g/L',   'normal_range' => '0.10 - 0.30'],
            ],
            'CREAT' => [
                ['name' => 'Créatinine',         'unit' => 'mg/L',  'normal_range' => '7 - 13 (H) / 6 - 11 (F)'],
                ['name' => 'DFG',                'unit' => 'mL/min','normal_range' => '> 90'],
                ['name' => 'Clairance créatinine','unit' => 'mL/min','normal_range' => '90 - 140'],
            ],
            'CRP' => [
                ['name' => 'CRP',                'unit' => 'mg/L',  'normal_range' => '0 - 6'],
            ],
            'VS' => [
                ['name' => 'VS 1ère heure',     'unit' => 'mm/h',  'normal_range' => '0 - 15 (H) / 0 - 20 (F)'],
                ['name' => 'VS 2ème heure',     'unit' => 'mm/h',  'normal_range' => '0 - 30 (H) / 0 - 40 (F)'],
            ],
            'IONO' => [
                ['name' => 'Sodium',             'unit' => 'mmol/L','normal_range' => '135 - 145'],
                ['name' => 'Potassium',          'unit' => 'mmol/L','normal_range' => '3.5 - 5.0'],
                ['name' => 'Chlorure',           'unit' => 'mmol/L','normal_range' => '96 - 106'],
                ['name' => 'Calcium',            'unit' => 'mmol/L','normal_range' => '2.20 - 2.60'],
                ['name' => 'Magnésium',          'unit' => 'mmol/L','normal_range' => '0.70 - 1.00'],
                ['name' => 'Bicarbonate',        'unit' => 'mmol/L','normal_range' => '22 - 29'],
                ['name' => 'Phosphore',          'unit' => 'mmol/L','normal_range' => '0.80 - 1.50'],
            ],
            'TSH' => [
                ['name' => 'TSH',                'unit' => 'mUI/L','normal_range' => '0.4 - 4.0'],
                ['name' => 'T4L',                'unit' => 'pmol/L','normal_range' => '12 - 22'],
                ['name' => 'T3L',                'unit' => 'pmol/L','normal_range' => '3.1 - 6.8'],
            ],
            'ECBU' => [
                ['name' => 'pH',                 'unit' => '',      'normal_range' => '5.0 - 8.0'],
                ['name' => 'Densité',            'unit' => '',      'normal_range' => '1.005 - 1.030'],
                ['name' => 'Protéines',          'unit' => 'g/L',   'normal_range' => '0 - 0.15'],
                ['name' => 'Glucose',            'unit' => 'g/L',   'normal_range' => '0'],
                ['name' => 'Leucocytes',         'unit' => '/µL',   'normal_range' => '0 - 25'],
                ['name' => 'Érythrocytes',       'unit' => '/µL',   'normal_range' => '0 - 10'],
                ['name' => 'Nitrites',           'unit' => '',      'normal_range' => 'Négatif'],
                ['name' => 'Hématies',           'unit' => '/µL',   'normal_range' => '0 - 5'],
                ['name' => 'Cellules épithéliales', 'unit' => '/µL','normal_range' => '0 - 10'],
                ['name' => 'Bactéries',          'unit' => '',      'normal_range' => 'Absentes'],
                ['name' => 'Levures',            'unit' => '',      'normal_range' => 'Absentes'],
            ],
        ];

        foreach ($parameters as $code => $params) {
            $exam = Exam::where('code', $code)->first();
            if (!$exam) continue;

            foreach ($params as $param) {
                ExamParameter::updateOrCreate(
                    ['exam_id' => $exam->id, 'name' => $param['name']],
                    [
                        'unit' => $param['unit'],
                        'normal_range' => $param['normal_range'],
                        'is_archive' => false,
                    ]
                );
            }
        }

        $this->command->info('Exam parameters seeded for all 10 exams.');
    }
}
