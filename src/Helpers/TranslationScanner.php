<?php

namespace Kenepa\TranslationManager\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TranslationScanner
{
    /**
     * Starts the translation scanning process.
     *
     * @return array An array containing all translation groups and keys found in the application.
     */
    public static function scan(): array
    {
        $allGroupsAndKeys = [];
        $files = File::allFiles(lang_path());

        // Loop through all groups
        foreach ($files as $file) {
            // Remove the first part (e.g. nl)
            $nameParts = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());

            // TODO: add support for vendor translations
            if ($nameParts[0] == 'vendor') {
                continue;
            }

            // load the dat from the file
            $data = require $file;

            foreach ($data as $key => $value) {
                $found = false;
                $group = Str::replace('.php', '', $nameParts[1]);

                // check if the translation is already present and append it
                foreach ($allGroupsAndKeys as &$GroupAndKey) {
                    if ($GroupAndKey['group'] === $group && $GroupAndKey['key'] === $key) {
                        $GroupAndKey['text'][$nameParts[0]] = $value;
                        $found = true;
                    }
                }

                // new translation found
                if (! $found) {
                    $allGroupsAndKeys[] = [
                        'group' => $group,
                        'key' => $key,
                        'text' => [$nameParts[0] => $value],
                    ];
                }
            }
        }

        return $allGroupsAndKeys;
    }
}
