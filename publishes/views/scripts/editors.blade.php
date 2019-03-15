@inject('module', 'scaffold.module')
@php($form = $module->form())

@if($form->hasEditors('markdown'))
    @if (file_exists(public_path('admin/editors/markdown.js')))
        <link rel="stylesheet" href="{{ mix('admin/editors/markdown.css') }}">
        <script src="{{ mix('admin/editors/markdown.js') }}"></script>
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
    <link rel="stylesheet" href="{{ mix('admin/editors/medium.css') }}">
    <script src="{{ mix('admin/editors/medium.js') }}"></script>
@endif

@if($form->hasEditors('ckeditor'))
    <script src="{{ mix('admin/editors/ckeditor.js') }}"></script>
@endif

@if ($form->hasEditors('tinymce'))
    <script src="{{ mix('admin/editors/tinymce.js') }}"></script>
@endif
