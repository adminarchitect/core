class AdminMix {
    constructor(Mix) {
        this.Mix = Mix;

        this.handleAliases();
    }

    static resource(path) {
        return 'resources/assets/administrator/' + path.trim('/');
    }

    static asset(path) {
        return 'public/admin/' + path.trim('/');
    }

    handleFiles() {
        this.Mix.copy(AdminMix.resource('images'), AdminMix.asset('images'));
    };

    handle() {
        this.handleFiles();

        this.handleScripts();

        this.handleStyles();

        this.handleEditors();
    }

    handleStyles() {
        this.Mix.sass(AdminMix.resource('sass/app.scss'), AdminMix.asset('app.css'));
        this.Mix.sass(AdminMix.resource('sass/vendor.scss'), AdminMix.asset('vendor.css'));
        this.Mix.less(AdminMix.resource('less/glyphicons.less'), AdminMix.asset('glyphicons.css'));
    }

    handleScripts() {
        this.Mix.js('resources/assets/administrator/js/vendor.js', 'public/admin/vendor.js');
        this.Mix.js('resources/assets/administrator/js/app.js', 'public/admin/app.js');
        this.Mix.scripts([
            'resources/assets/administrator/js/media/{app,helpers,templates}.js',
            'resources/assets/administrator/js/media/{controllers,services,directives}/**/*.js',
        ], 'public/admin/media.js');
    }

    handleAliases() {
        this.Mix.webpackConfig({
            resolve: {
                alias: {
                    'jquery-ui/sortable': 'jquery-ui/ui/widgets/sortable',
                },
            },
            /* Do not load MomentJS locales */
            plugins: [
                new (require('webpack')).IgnorePlugin(/^\.\/locale$/, /moment$/),
            ]
        });
    }

    handleEditors() {
        //this.handleMarkdownEditor();
        this.handleMediumEditor();
        this.handleTinyMce();
        this.handleCkEditor();
    }

    handleTinyMce() {
        this.Mix.copy('node_modules/tinymce/skins', AdminMix.asset('editors/skins'));
        this.Mix.js(AdminMix.resource('js/editors/tinymce.js'), AdminMix.asset('editors/tinymce.js'));
    }

    handleCkEditor() {
        this.Mix.copy([
            'node_modules/ckeditor/config.js',
            'node_modules/ckeditor/styles.js',
            'node_modules/ckeditor/contents.css'
        ], AdminMix.asset('editors'));
        this.Mix.copy('node_modules/ckeditor/lang/en.js', AdminMix.asset('editors/lang/en.js'));
        this.Mix.copy('node_modules/ckeditor/skins', AdminMix.asset('editors/skins'));
        this.Mix.copy('node_modules/ckeditor/plugins', AdminMix.asset('editors/plugins'));

        this.Mix.js(AdminMix.resource('js/editors/ckeditor.js'), AdminMix.asset('editors/ckeditor.js'));
    }

    handleMediumEditor() {
        this.Mix.js(AdminMix.resource('js/editors/medium.js'), AdminMix.asset('editors/medium.js'));
        this.Mix.sass(AdminMix.resource('sass/editors/medium.scss'), AdminMix.asset('editors/medium.css'));
    }

    /**
     * @uses `simplemde` package
     * @note yarn add simplemde | npm install simplemde --save
     */
    handleMarkdownEditor() {
        this.Mix.js(AdminMix.resource('js/editors/markdown.js'), AdminMix.asset('editors/markdown.js'));
        this.Mix.sass(AdminMix.resource('sass/editors/markdown.scss'), AdminMix.asset('editors/markdown.css'));
    }
}

module.exports = AdminMix;