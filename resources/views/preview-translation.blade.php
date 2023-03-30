<div class="p-4 bg-gray-100 dark:bg-gray-700 border border-gray-300 rounded-md shadow-sm">
    <h3 class="text-lg font-medium mb-2">{{ __('filament-translation-manager::translations.preview') }}</h3>
    <p class="text-sm text-gray-600 dark:text-white mb-4">{{ __('filament-translation-manager::translations.preview-description', ['lang' => app()->getLocale()]) }}</p>
    <div class="bg-white rounded-md shadow-sm p-4">
        <p class="text-base text-gray-800">{{ trans($this->record->group . '.' . $this->record->key) }}</p>
    </div>
</div>