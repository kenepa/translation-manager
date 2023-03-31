<?php

namespace musa11971\FilamentTranslationManager\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TranslationScanner
{
    /**
     * Starts the translation scanning process.
     *
     * @return array An array containing all translation groups and keys found in the application.
     */
    public function start(): array
    {
        $files = File::allFiles(lang_path());

        $allGroupsAndKeys = [];
        $seenCombinations = [];

        // Loop through all groups
        foreach ($files as $file) {
            $name = $file->getRelativePathname();

            // Remove the .php extension
            $name = Str::replace('.php', '', $name);

            // Remove the first part (e.g. nl)
            $name = explode('/', $name);
            unset($name[0]);
            $name = implode('/', $name);

            // Traverse the array keys in this group
            $this->traverseArray(trans($name), $seenCombinations, $allGroupsAndKeys, $name);
        }

        return $allGroupsAndKeys;
    }

    /**
     * Recursively traverses an array and adds all keys to the `$allGroupsAndKeys` array.
     *
     * @param  array  $array The array to traverse.
     * @param  array  $allGroupsAndKeys The array to add the keys to.
     * @param  string  $groupName The name of the translation group.
     * @param  string|null  $parentKey The parent key path, if applicable.
     */
    private function traverseArray($array, &$seenCombinations, &$allGroupsAndKeys, $groupName, $parentKey = null): void
    {
        foreach ($array as $key => $value) {
            $currentKey = $parentKey ? $parentKey . '.' . $key : $key;

            if (is_array($value)) {
                $this->traverseArray($value, $seenCombinations, $allGroupsAndKeys, $groupName, $currentKey);
            } else {
                // Skip if this group and key pair has already been added
                if (isset($seenCombinations[$groupName][$currentKey])) {
                    continue;
                }

                // Add this group and key pair to the array
                $seenCombinations[$groupName][$currentKey] = true;

                $allGroupsAndKeys[] = [
                    'group' => $groupName,
                    'key' => $currentKey,
                    'text' => [],
                ];
            }
        }
    }
}
