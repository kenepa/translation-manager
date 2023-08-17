<div class="bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-lg">
    <h3 class="text-lg font-bold mb-2">{{ __('translation-manager::translations.preview') }}</h3>
    <p class="text-sm text-gray-600 dark:text-white mb-4">{{ __('translation-manager::translations.preview-description', ['lang' => app()->getLocale()]) }}</p>
    <div class="bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-lg">
        <p class="text-base text-gray-800">{{ trans($this->record->group . '.' . $this->record->key) }}</p>
    </div>
</div>