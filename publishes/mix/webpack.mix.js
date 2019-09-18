let mix = require('laravel-mix')

mix.setPublicPath('build')

mix
    /**
     * Assembles TinyMCE editor
     *
     * @requires `tinymce@^4.6.4` package
     * @note npm i tinymce@^4.6.4 --save-dev
     */
    .copy('node_modules/tinymce/skins', 'build/editors/skins')
    .js('resources/js/editors/tinymce.js', 'build/editors/tinymce.js')

    /**
     * Assembles CK editor
     *
     * @requires `ckeditor@^4.7.0` package
     * @note npm i ckeditor@^4.7.0 --save-dev
     */
    // .copy([
    //     'node_modules/ckeditor/config.js',
    //     'node_modules/ckeditor/styles.js',
    //     'node_modules/ckeditor/contents.css'
    // ], 'build/editors')
    // .copy('node_modules/ckeditor/lang/en.js', 'build/editors/lang/en.js')
    // .copy('node_modules/ckeditor/skins', 'build/editors/skins')
    // .copy('node_modules/ckeditor/plugins', 'build/editors/plugins')
    // .js('resources/js/editors/ckeditor.js', 'build/editors/ckeditor.js')

    /**
     * Assembles Medium editor.
     *
     * @requires `medium-editor@^5.23.1` package
     * @note npm i medium-editor@^5.23.1 --save-dev
     */
    // .js('resources/js/editors/medium.js', 'build/editors/medium.js')
    // .sass('resources/sass/editors/medium.scss', 'build/editors/medium.css')

    /**
     * Assembles Markdown editor.
     *
     * @requires `simplemde` package
     * @note npm i simplemde --save-dev
     */
    // .js('resources/js/editors/markdown.js', 'build/editors/markdown.js')
    // .sass('resources/sass/editors/markdown.scss', 'build/editors/markdown.css')

    .js('resources/js/app.js', 'build')
    .js('resources/js/theme.js', 'build')
    .js('resources/js/vendor.js', 'build')
    .extract([
        'vue',
        'vue-clip',
        'axios',
        'element-ui',
        'bootstrap',
        '@fancyapps/fancybox',
        // 'tinymce',
        // 'ckeditor',
    ])
    .options({
        fileLoaderDirs: {
            "fonts": "../fonts"
        }
    })
    .sass('resources/sass/app.scss', 'build/app.css')
    .sass('resources/sass/vendor.scss', 'build/vendor.css')
    .less('resources/less/glyphicons.less', 'build/glyphicons.css')
    .copy('build', '../public/admin')
    .copy('resources/images', '../public/admin/images')
    .copy('fonts', '../public/fonts')
    .disableSuccessNotifications()
    .sourceMaps()
    .webpackConfig({
        resolve: {
            alias: {
                // Enable if using jquery-ui/sortable
                //'jquery-ui/sortable': 'jquery-ui/ui/widgets/sortable',
            },
        },
        /* Do not load MomentJS locales */
        plugins: [
            new (require('webpack')).IgnorePlugin(/^\.\/locale$/, /moment$/),
        ]
    })
