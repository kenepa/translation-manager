<div>
    <div class="py-4">
        <h1 class="filament-header-heading text-2xl font-bold tracking-tight">
            {{ __('translation-manager::translations.quick-translate') }}
        </h1>
    </div>

    {{ $this->selectForm }}

    @if ($this->selectedLocale)
        @if ($this->record)
            <div class="mt-2 shadow-md p-3 dark:bg-gray-800 bg-white rounded-md">
                <span class="text-gray-400">
                    {{ __('translation-manager::translations.quick-translate-translation-number', ['total' => $this->totalLanguageLines]) }}:
                    {{ $this->record->group }}.{{ $this->record->key }}
                </span>

                @include('translation-manager::preview-translation')

                <div class="my-4">{{ $this->enterForm }}</div>

                <x-filament::button wire:click="saveAndContinue">{{ __('translation-manager::translations.quick-translate-save-and-continue') }}</x-filament::button>

                <a class="ml-2 text-gray-400 cursor-pointer hover:text-gray-200 inline-block" wire:click="next">
                    {{ __('translation-manager::translations.quick-translate-skip') }}
                </a>
            </div>
        @else
            <p class="mt-4">{{ __('translation-manager::translations.quick-translate-nothing') }}</p>
        @endif
    @endif
</div>