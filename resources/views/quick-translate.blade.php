<div>
    <div class="py-4">
        <h1 class="filament-header-heading text-2xl font-bold tracking-tight">
            {{ __('translation-manager::translations.quick-translate') }}
        </h1>
    </div>

    {{ $this->selectForm }}

    @if ($this->selectedLocale)
        @if ($this->record)
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-lg mt-6">
                <div class="mb-4">
                    <h1 class="font-bold text-lg text-gray-600 dark:text-gray-200">
                        {{ $this->record->group }}.{{ $this->record->key }}
                    </h1>
                    <p class="text-sm text-gray-400  dark:text-gray-300">
                        {{ __('translation-manager::translations.quick-translate-translation-number', ['total' => $this->totalLanguageLines]) }}
                    </p>
                </div>

                <div class="mt-4 mb-4 text-gray-600 dark:text-gray-200">
                    @include('translation-manager::preview-translation')
                </div>

                <div class="mt-4 mb-4">{{ $this->enterForm }}</div>

                <div class="mt-4 flex gap-4 items-center">
                    <x-filament::button wire:click="saveAndContinue">{{ __('translation-manager::translations.quick-translate-save-and-continue') }}</x-filament::button>

                    <a class="text-gray-600 dark:text-gray-200 hover:text-gray-400 dark:hover:text-gray-600 cursor-pointer inline-block" wire:click="next">
                        {{ __('translation-manager::translations.quick-translate-skip') }}
                    </a>
                </div>

            </div>
        @else
            <p class="mt-4 text-gray-800 dark:text-gray-200">{{ __('translation-manager::translations.quick-translate-nothing') }}</p>
        @endif
    @endif
</div>