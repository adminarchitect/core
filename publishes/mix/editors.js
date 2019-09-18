class EditorManager {
    constructor(mix) {
        this.mix = {...mix}

        this.KNOWN_EDITORS = [
            'TinyMce',
            'Medium',
            'Markdown',
            'Ck'
        ]
    }

    /**
     * Assemble required editors.
     */
    enable(editors) {
        if (!Array.isArray(editors)) {
            editors = Array.from(arguments)
        }

        editors.forEach((editor) => {
            if (this.KNOWN_EDITORS.indexOf(editor) === -1) {
                throw new Error(`Unknown editor: ${editor}`)
            }

            const method = `handle${editor}Editor`
            this[method]()
        })
    }

    /**
     * Assembles TinyMCE editor
     *
     * @requires `tinymce@^4.6.4` package
     * @note npm i tinymce@^4.6.4 --save-dev
     */
    handleTinyMceEditor() {
        this.mix.copy('node_modules/tinymce/skins', 'build/editors/skins')

        this.mix.js('resources/js/editors/tinymce.js', 'build/editors/tinymce.js')
    }

    /**
     * Assembles CK editor
     *
     * @requires `ckeditor@^4.7.0` package
     * @note npm i ckeditor@^4.7.0 --save-dev
     */
    handleCkEditor() {
        this.mix.copy([
            'node_modules/ckeditor/config.js',
            'node_modules/ckeditor/styles.js',
            'node_modules/ckeditor/contents.css'
        ], 'build/editors')
        this.mix.copy('node_modules/ckeditor/lang/en.js', 'build/editors/lang/en.js')
        this.mix.copy('node_modules/ckeditor/skins', 'build/editors/skins')
        this.mix.copy('node_modules/ckeditor/plugins', 'build/editors/plugins')

        this.mix.js('resources/js/editors/ckeditor.js', 'build/editors/ckeditor.js')
    }

    /**
     * Assembles Medium editor.
     *
     * @requires `medium-editor@^5.23.1` package
     * @note npm i medium-editor@^5.23.1 --save-dev
     */
    handleMediumEditor() {
        this.mix.js('resources/js/editors/medium.js', 'build/editors/medium.js')
        this.mix.sass('resources/sass/editors/medium.scss', 'build/editors/medium.css')
    }

    /**
     * Assembles Markdown editor.
     *
     * @requires `simplemde` package
     * @note npm i simplemde --save-dev
     */
    handleMarkdownEditor() {
        this.mix.js('resources/js/editors/markdown.js', 'build/editors/markdown.js')
        this.mix.sass('resources/sass/editors/markdown.scss', 'build/editors/markdown.css')
    }
}

module.exports = EditorManager
