<?php

namespace musa11971\FilamentTranslationManager\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TranslationScanner
{
    public function start(): array
    {
        $files = File::allFiles(lang_path());

        $allGroupsAndKeys = [];

        // Loop through all groups
        foreach ($files as $file) {
            $name = $file->getRelativePathname();

            // Remove the .php extension
            $name = Str::replace('.php', '', $name);

            // Remove the first part (e.g. nl)
            $name = explode('/', $name);
            unset($name[0]);
            $name = implode('/', $name);

            // Loop through the keys in this group
            $keyGroup = trans($name);

            foreach ($keyGroup as $key => $translation) {
                // TODO: Fix this bug.
                // Arrays should be added recursively
                if (is_array(trans($name . '.' . $key))) {
                    continue;
                }

                $allGroupsAndKeys[] = [
                    'group' => $name,
                    'key' => $key,
                    'text' => [],
                ];
            }
        }

        return $allGroupsAndKeys;
    }
}
