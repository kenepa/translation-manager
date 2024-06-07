<div x-data>
    <template x-ref="template">
        @foreach ($getRecord()->text as $key => $translation)
            <div class="block"><span class="font-bold">{{ $key }}</span> - {{ $translation }} </div>
        @endforeach

    </template>

    <button x-tooltip="{
        content: () => $refs.template.innerHTML,
        allowHTML: true,
        appendTo: $root,
    }">
        {{ $this->getTranslationPreview($getRecord(), 50) }}
    </button>
</div>
