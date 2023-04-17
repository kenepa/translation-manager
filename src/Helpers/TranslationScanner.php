<?php

namespace musa11971\FilamentTranslationManager\Helpers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use musa11971\FilamentTranslationManager\Commands\SynchronizeTranslationsCommand;

class TranslationScanner
{
    private ?Command $command;

    public function __construct(SynchronizeTranslationsCommand $command = null)
    {
        $this->command = $command;
    }

    /**
     * Starts the translation scanning process.
     *
     * @return array An array containing all translation groups and keys found in the application.
     */
    public function start(): array
    {
        $this->command?->loudInfo('starting scanner');

        $files = File::allFiles(lang_path());

        $this->command?->loudInfo('found files count: ' . count($files));

        $allGroupsAndKeys = [];
        $seenCombinations = [];

        // Loop through all groups
        foreach ($files as $file) {
            $this->command?->loudInfo('looping for file ' . $file->getRelativePathname());

            $name = $file->getRelativePathname();

            // Remove the .php extension
            $name = Str::replace('.php', '', $name);

            // Remove the first part (e.g. nl)
            $nameParts = explode(DIRECTORY_SEPARATOR, $name);

            // TODO: add support for vendor translations
            if ($nameParts[0] == 'vendor') {
                $this->command?->loudInfo('skipped vendor: ' . $name);

                continue;
            }

            unset($nameParts[0]);
            $name = implode(DIRECTORY_SEPARATOR, $nameParts);

            // Traverse the array keys in this group
            $groupArray = trans($name, [], config('app.fallback_locale'));

            if (! is_array($groupArray)) {
                $this->command?->loudInfo('skipped non-array: ' . $name);

                continue;
            }

            $this->traverseArray($groupArray, $seenCombinations, $allGroupsAndKeys, $name);
        }

        $this->command?->loudInfo('scanner done');

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

                $this->command?->loudInfo('added ' . $groupName . '.' . $currentKey);

                $allGroupsAndKeys[] = [
                    'group' => $groupName,
                    'key' => $currentKey,
                    'text' => [],
                ];
            }
        }
    }
}
