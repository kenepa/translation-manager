@php
if(!function_exists('try_svg')) {
    function try_svg($name, $classes) {
        try {
            return svg($name, $classes);
        }
        catch(\Exception $e) {
            return '❓';
        }
    }
}
@endphp

<x-filament::dropdown placement="bottom-start">
    <x-slot name="trigger">
        <button
                @class([
                    'block hover:opacity-75',
                    'pt-0' => $showFlags,
                ])
                id="filament-language-switcher"
                x-on:click="toggle"
        >
            <div
                    @class([
                        'flex items-center justify-center rounded-full bg-cover bg-center',
                        'w-8 h-8 bg-gray-200 dark:bg-gray-900' => $showFlags,
                        'w-[2.3rem] h-[2.3rem] bg-[#030712]' => !$showFlags,
                    ])
            >
            <span class="opacity-100">
                @if ($showFlags)
                    {{ try_svg('flag-1x1-'.$currentLanguage['flag'], 'rounded-full w-8 h-8') }}
                @else
                    <x-icon
                            name="heroicon-o-language"
                            class="w-5 h-5"
                    />
                @endif
            </span>
            </div>
        </button>
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($otherLanguages as $language)
            @php $isCurrent = $currentLanguage['code'] === $language['code']; @endphp
            <x-filament::dropdown.list.item>
                <a
                        @class([
                            'filament-dropdown-list-item filament-dropdown-item group flex w-full items-center whitespace-nowrap rounded-md p-2 text-sm outline-none text-gray-500 dark:text-gray-200',
                            'hover:bg-gray-50 focus:bg-gray-50 dark:hover:bg-white/5 dark:focus:bg-white/5 hover:text-gray-700 focus:text-gray-500 dark:hover:text-gray-200 dark:focus:text-gray-400' => !$isCurrent,
                            'cursor-default' => $isCurrent,
                        ])
                        @if (!$isCurrent)
                            href="{{ route('translation-manager.switch', ['code' => $language['code']]) }}"
                        @endif
                >
                    <span class="filament-dropdown-list-item-label truncate w-full text-start flex justify-content-start gap-3">
                        @if ($showFlags)
                            {{ try_svg('flag-4x3-'.$language['flag'], 'w-6 h-6') }}

                            <span class="pl">{{ $language['name'] }}</span>
                        @else
                            <span @class(['font-semibold' => $isCurrent])>{{ str($language['code'])->upper()->value() . " - {$language['name']}" }}</span>
                        @endif
                    </span>
                </a>
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>

