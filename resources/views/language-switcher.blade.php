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
    <button class="ml-4 pt-2" id="filament-language-switcher" class="block" x-on:click="toggle">
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-200 bg-cover bg-center dark:bg-gray-900">
            <span class="opacity-70">
                {{ try_svg('flag-1x1-'.$currentLanguage['flag'], 'rounded-full w-10 h-10') }}
            </span>
        </div>
    </button>

    <div x-ref="panel" x-float.placement.bottom-end.flip.offset="{ offset: 8 }" x-transition:enter-start="opacity-0 scale-95" x-transition:leave-end="opacity-0 scale-95" class="filament-dropdown-panel absolute z-10 w-full divide-y divide-gray-100 rounded-lg bg-white shadow-lg ring-1 ring-black/5 transition dark:divide-gray-700 dark:bg-gray-800 dark:ring-white/10 max-w-[14rem]" style="display: none; left: 1152px; top: 59.5px;">
        <div class="filament-dropdown-list p-1">
            @foreach ($otherLanguages as $language)
            <a class="filament-dropdown-list-item filament-dropdown-item group flex w-full items-center whitespace-nowrap rounded-md p-2 text-sm outline-none hover:text-white focus:text-white hover:bg-primary-500 focus:bg-primary-500" href="{{ route('translation-manager.switch', ['code' => $language['code']]) }}">
                <span class="filament-dropdown-list-item-label truncate w-full text-start flex justify-content-start gap-1">
                    {{ try_svg('flag-4x3-'.$language['flag'], 'w-6 h-6') }}

                    <span>{{ $language['name'] }}</span>
                </span>
            </a>
            @endforeach
        </div>
    </div>
</div>