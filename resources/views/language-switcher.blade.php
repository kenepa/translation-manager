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

<div x-data="{
        toggle: function (event) {
            $refs.panel.toggle(event)
        },
        open: function (event) {
            $refs.panel.open(event)
        },
        close: function (event) {
            $refs.panel.close(event)
        },
    }">

    <button
        @class([
            'ml-4 block hover:opacity-75',
            'pt-2' => $showFlags,
        ])
        id="filament-language-switcher"
        x-on:click="toggle"
    >
        <div
            @class([
                'flex items-center justify-center rounded-full bg-cover bg-center',
                'w-10 h-10 bg-gray-200 dark:bg-gray-900' => $showFlags,
                'w-[2.3rem] h-[2.3rem] bg-[#030712]' => !$showFlags,
            ])
        >
            <span class="opacity-100">
                @if ($showFlags)
                    {{ try_svg('flag-1x1-'.$currentLanguage['flag'], 'rounded-full w-10 h-10') }}
                @else
                    <x-icon
                        name="heroicon-o-language"
                        class="w-5 h-5"
                    />
                @endif
            </span>
        </div>
    </button>

    <div x-ref="panel" x-float.placement.bottom-end.flip.offset="{ offset: 8 }" x-transition:enter-start="opacity-0 scale-95" x-transition:leave-end="opacity-0 scale-95" class="ffi-dropdown-panel absolute z-10 w-screen divide-y divide-gray-100 rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 transition dark:divide-white/5 dark:bg-gray-900 dark:ring-white/10 max-w-[14rem]" style="display: none; left: 1152px; top: 59.5px;">
        <div class="filament-dropdown-list p-1">
            @foreach ($otherLanguages as $language)
                @php $isCurrent = $currentLanguage['code'] === $language['code']; @endphp
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
            @endforeach
        </div>
    </div>
</div>
