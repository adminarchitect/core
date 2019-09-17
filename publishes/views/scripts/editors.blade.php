@php($form = $resource->form())

@if($form->hasEditors('markdown'))
    @if (file_exists(public_path('admin/editors/markdown.js')))
        <link rel="stylesheet" href="{{ mix('editors/markdown.css', 'admin') }}">
        <script src="{{ mix('editors/markdown.js', 'admin') }}"></script>
    @else
        /**
        * It is not working as well using npm version, so temporary decided to use cdn version.
        * Checkout resources/views/vendor/administrator/scripts/editors.blade.php for change this behavior
        */
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
        <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
        <script>
            $(function () {
                let elements = document.querySelectorAll('[data-editor="markdown"]');
                elements.forEach((e) => {
                    new SimpleMDE({element: e});
                });
            });
        </script>
    @endif;
@endif

@if($form->hasEditors('medium'))
    <link rel="stylesheet" href="{{ mix('editors/medium.css', 'admin') }}">
    <script src="{{ mix('editors/medium.js', 'admin') }}"></script>
@endif

@if($form->hasEditors('ckeditor'))
    <script src="{{ mix('editors/ckeditor.js', 'admin') }}"></script>
@endif

@if ($form->hasEditors('tinymce'))
    <script src="{{ mix('editors/tinymce.js', 'admin') }}"></script>
@endif
