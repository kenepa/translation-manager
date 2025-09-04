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
        @if (isset($currentLanguage) && $showFlags)
            <x-filament::link
                    tag="button"
            >
                {{ try_svg('flag-1x1-'.$currentLanguage['flag'], 'rounded-full w-8 h-8') }}
            </x-filament::link>
        @else
            <x-filament::icon-button
                    icon="heroicon-o-language"
                    label="New label"
            />
        @endif
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($otherLanguages as $language)
            @php
                $isCurrent = false;
                if (isset($currentLanguage)) {
                    $isCurrent = $currentLanguage['code'] === $language['code'];
                }
            @endphp
            <x-filament::dropdown.list.item :href="route('translation-manager.switch', ['code' => $language['code']])" tag="a">
                  <span class="filament-dropdown-list-item-label truncate w-full text-start flex justify-content-start gap-3">
                    @if ($showFlags)
                          {{ try_svg('flag-4x3-'.$language['flag'], 'w-6 h-6') }}
                          <span class="pl">{{ $language['name'] }}</span>
                      @else
                          <span @class(['font-semibold' => $isCurrent])>{{ str($language['code'])->upper()->value() . " - {$language['name']}" }}</span>
                      @endif
                </span>
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>

