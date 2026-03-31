@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '.tinymce-editor', // Target any element with this class Let me fix this to explicitly use the `.tinymce-editor`
        height: 350,
        menubar: false,
        promotion: false,
        branding: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
        'bold italic | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat',
        skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
        content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
</script>
<style>
    .tox-notifications-container { display: none !important; }
    .tox-tinymce { z-index: 40 !important; border-radius: 0.75rem !important; overflow: hidden; border: 1px solid var(--tw-prose-body, #e5e7eb) !important; }
    .dark .tox-tinymce { border-color: #374151 !important;  }
</style>
@endpush
