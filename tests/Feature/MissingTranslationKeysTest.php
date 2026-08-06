<?php

namespace Tests\Feature;

use Tests\TestCase;

class MissingTranslationKeysTest extends TestCase
{
    private function langFiles(): array
    {
        $fr = json_decode((string) file_get_contents(base_path('lang/fr.json')), true);
        $en = json_decode((string) file_get_contents(base_path('lang/en.json')), true);

        $this->assertIsArray($fr, 'lang/fr.json is not valid JSON');
        $this->assertIsArray($en, 'lang/en.json is not valid JSON');

        return [$fr, $en];
    }

    private function bladeFiles(): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)
        );

        $files = [];
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php' && str_ends_with($file->getFilename(), 'blade.php')) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    public function test_fr_and_en_lang_files_have_identical_key_sets(): void
    {
        [$fr, $en] = $this->langFiles();

        $this->assertSame(
            count($fr),
            count($en),
            'lang/fr.json and lang/en.json must have the same number of keys.'
        );

        $missingInEn = array_diff_key($fr, $en);
        $missingInFr = array_diff_key($en, $fr);

        $this->assertEmpty($missingInEn, 'Keys present in fr.json but missing in en.json: '.implode(', ', array_keys($missingInEn)));
        $this->assertEmpty($missingInFr, 'Keys present in en.json but missing in fr.json: '.implode(', ', array_keys($missingInFr)));
    }

    public function test_every_translation_key_used_in_views_exists_in_both_lang_files(): void
    {
        [$fr, $en] = $this->langFiles();

        $pattern = '/(?:__|@lang|trans)\s*\(\s*([\'"])([^\'"]+)\1|@json\s*\(\s*__\s*\(\s*([\'"])([^\'"]+)\3\s*\)/';
        $missing = [];

        foreach ($this->bladeFiles() as $path) {
            $content = (string) file_get_contents($path);
            if (! preg_match_all($pattern, $content, $matches)) {
                continue;
            }

            foreach ($matches[2] as $i => $key) {
                if (! empty($matches[4][$i])) {
                    $key = $matches[4][$i];
                }

                // Skip dynamically built keys such as __('admin.groups.role_'.$role)
                if ($key === '' || preg_match('/[${}]/', $key) || str_ends_with($key, '.') || str_ends_with($key, '_')) {
                    continue;
                }

                $file = substr($path, strlen(resource_path('views/')));

                if (! array_key_exists($key, $fr)) {
                    $missing[] = "{$file}: key '{$key}' missing in lang/fr.json";
                }
                if (! array_key_exists($key, $en)) {
                    $missing[] = "{$file}: key '{$key}' missing in lang/en.json";
                }
            }
        }

        $this->assertEmpty($missing, implode(PHP_EOL, $missing));
    }

    public function test_no_hardcoded_accented_french_remains_in_views_except_brand(): void
    {
        $accentPattern = '/[àâäéèêëîïôöùûüçœÀÂÄÉÈÊËÎÏÔÖÙÛÜÇŒ]/u';
        $offenders = [];

        foreach ($this->bladeFiles() as $path) {
            $content = (string) file_get_contents($path);

            // Mask any content that sits inside a translation call or @json wrapper.
            $masked = preg_replace('/(?:__|@lang|trans)\s*\([^)]*\)|@json\s*\([^)]*\)/', '', $content);
            $masked = str_replace(['Medix eSanté', 'eSanté', 'eSant'], '', $masked);

            if (preg_match_all($accentPattern, $masked, $lines, PREG_OFFSET_CAPTURE)) {
                $lineNumbers = [];
                foreach ($lines[0] as $hit) {
                    $lineNumbers[] = substr_count(substr($content, 0, $hit[1]), "\n") + 1;
                }
                $file = substr($path, strlen(resource_path('views/')));
                $offenders[] = "{$file} (lines ".implode(',', array_unique($lineNumbers)).')';
            }
        }

        $this->assertEmpty($offenders, implode(PHP_EOL, $offenders));
    }

    public function test_no_common_french_ui_words_remain_hardcoded_in_views(): void
    {
        $frenchWords = [
            'Rechercher', 'Supprimer', 'Annuler', 'Chargement', 'Enregistrer', 'Aucun',
            'Confirmer', 'Imprimer', 'Tutoriel', 'Connexion', 'Inscription', 'Suivant',
            'Précédent', 'Annulé', 'Annulée', 'Sélectionner', 'Modifier', 'Envoyer',
            'Fermer', 'Ajouter', 'Afficher', 'Voir', 'Créer', 'Terminé', 'Terminée',
        ];

        $callPattern = '/(?:__|@lang|trans|@json)\s*\([^)]*\)/';
        $offenders = [];

        foreach ($this->bladeFiles() as $path) {
            $content = (string) file_get_contents($path);
            $masked = preg_replace($callPattern, '', $content);
            $masked = preg_replace('/<!--.*?-->/s', '', $masked);
            $masked = str_replace('Medix eSanté', '', $masked);

            foreach ($frenchWords as $word) {
                if (str_contains($masked, $word)) {
                    $file = substr($path, strlen(resource_path('views/')));
                    $offenders[] = "{$file}: contains hardcoded '{$word}'";
                }
            }
        }

        $this->assertEmpty($offenders, implode(PHP_EOL, $offenders));
    }
}
