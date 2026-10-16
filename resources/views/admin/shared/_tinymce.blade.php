@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '.tinymce-editor',
        height: 380,
        menubar: true,
        promotion: false,
        branding: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'image | removeformat | help',
        images_upload_handler: function (blobInfo, progress) {
            return new Promise((resolve, reject) => {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.blog-posts.upload-image') }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };
                xhr.onload = function() {
                    if (xhr.status === 403) { reject({ message: 'HTTP Error: 403 Forbidden', remove: true }); return; }
                    if (xhr.status === 419) { reject({ message: 'Session expired. Please refresh the page.', remove: true }); return; }
                    if (xhr.status === 422) { 
                        try {
                            var json = JSON.parse(xhr.responseText);
                            reject({ message: json.message || 'Validation Error', remove: true });
                        } catch (e) {
                            reject({ message: 'Validation Error (422)', remove: true });
                        }
                        return;
                    }
                    if (xhr.status < 200 || xhr.status >= 300) { reject({ message: 'HTTP Error: ' + xhr.status, remove: true }); return; }
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location != 'string') { reject({ message: 'Invalid JSON format', remove: true }); return; }
                        resolve(json.location);
                    } catch (err) {
                        reject({ message: 'Invalid response from server', remove: true });
                    }
                };
                xhr.onerror = function () { reject({ message: 'Image upload failed due to a network error. Code: ' + xhr.status, remove: true }); };
                formData = new FormData();
                
                var filename = blobInfo.filename();
                if (!filename.includes('.')) filename += '.png';
                
                formData.append('file', blobInfo.blob(), filename);
                xhr.send(formData);
            });
        },
        skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
        content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
        setup: function(editor) {
            editor.on('change input', function() {
                editor.save();
            });
        }
    });

    // Ensure TinyMCE editors save on form submit
    document.addEventListener('submit', function() {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }
    });

    // Ensure TinyMCE editors render properly when switching Alpine.js tabs or toggling visibility
    document.addEventListener('click', function(e) {
        if (e.target.closest('button') || e.target.closest('[x-on\\:click]') || e.target.closest('[\@click]')) {
            setTimeout(function() {
                if (typeof tinymce !== 'undefined') {
                    tinymce.editors.forEach(function(editor) {
                        var container = editor.getContainer();
                        if (container && container.offsetParent !== null) {
                            editor.fire('SkinLoaded');
                        }
                    });
                }
            }, 50);
        }
    });
</script>
<style>
    .tox-notifications-container { display: none !important; }
    .tox-tinymce { z-index: 40 !important; border-radius: 0.75rem !important; overflow: hidden; border: 1px solid var(--tw-prose-body, #e5e7eb) !important; }
    .dark .tox-tinymce { border-color: #374151 !important;  }
</style>
@endpush
