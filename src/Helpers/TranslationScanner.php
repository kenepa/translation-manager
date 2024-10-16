<?php

namespace Kenepa\TranslationManager\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TranslationScanner
{
    private static $seenCombinations = [];

    private static $allGroupsAndKeys = [];

    /**
     * Starts the translation scanning process.
     *
     * @return array An array containing all translation groups and keys found in the application.
     */
    public static function scan(): array
    {
        $directories = File::glob(base_path('Modules/*/lang/'));

        $files = File::allFiles(lang_path());

        if (!empty($directories)) {
            $files = array_merge($files, File::allFiles($directories));
        }

        // Loop through all groups
        foreach ($files as $file) {
            // Skip any other files than .php language files e.g. JSON files
            if ($file->getExtension() != 'php') {
                continue;
            }

            // Sanitize the file name and explode to allow checking
            $name = Str::replace('.php', '', $file->getRelativePathname());
            $nameParts = explode(DIRECTORY_SEPARATOR, $name);

            // TODO: add support for vendor translations
            if ($nameParts[0] == 'vendor') {
                continue;
            }

            $groupName = $file->getFilenameWithoutExtension();

            if (config('translation-manager.prepend_directory_path_to_group_name')) {
                //if the file is in a directory, append the path to the groupname
                $groupName = implode('/', array_slice($nameParts, 1));
            }

            // Load the data from the file
            self::parseTranslation(require $file, $nameParts[0], $groupName);
        }

        return self::$allGroupsAndKeys;
    }

    /**
     * Recursively parses a translation and adds all keys to the `$allGroupsAndKeys` array.
     *
     * @param  array  $translationArray  The translation array to parse.
     * @param  string  $locale  The locale of the translation.
     * @param  string  $groupName  The name of the translation group.
     * @param  string|null  $parentKey  The parent key path, if applicable.
     */
    private static function parseTranslation(array $translationArray, string $locale, string $groupName, ?string $parentKey = null): void
    {
        foreach ($translationArray as $key => $value) {
            $currentKey = $parentKey ? $parentKey . '.' . $key : $key;

            if (is_array($value)) {
                self::parseTranslation($value, $locale, $groupName, $currentKey);
            } else {
                $found = false;

                // Check if the translation is already present and append it
                foreach (self::$allGroupsAndKeys as &$groupAndKey) {
                    if ($groupAndKey['group'] === $groupName && $groupAndKey['key'] === $currentKey) {
                        $groupAndKey['text'][$locale] = $value;
                        $found = true;
                    }
                }

                // Add this group and key pair to the array
                if (! $found) {
                    self::$seenCombinations[$groupName][$currentKey] = true;

                    self::$allGroupsAndKeys[] = [
                        'group' => $groupName,
                        'key' => $currentKey,
                        'text' => [$locale => $value],
                    ];
                }
            }
        }
    }
}
