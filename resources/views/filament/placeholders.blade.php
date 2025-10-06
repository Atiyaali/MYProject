
<div class="space-y-2">
    <p class="text-sm text-gray-600">Fillables for Email body:</p>
    <div class="flex flex-wrap gap-2">
        @foreach ($fields as $field)
            <x-filament::button
                type="button"
                size="sm"
                color="gray"
                outlined
                onclick="insertPlaceholder('{{ '{' }}{{ $field }}{{ '}' }}')"
            >
                {{ ucfirst(str_replace('_', ' ', $field)) }}
            </x-filament::button>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    function insertPlaceholder(placeholder) {
        const editorEl = document.querySelector('#campaign-body-editor [contenteditable="true"]');
        if (editorEl) {
            editorEl.focus();
            document.execCommand('insertText', false, placeholder);
        } else {
            console.error("RichEditor not found");
        }
    }
</script>
@endpush
