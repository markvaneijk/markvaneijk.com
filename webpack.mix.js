let mix = require('laravel-mix');

require('laravel-mix-purgecss');
require('laravel-mix-alias');

mix.alias({
    '@': '/resources/js',
    '~': '/node_modules',
    '@components': '/resources/js/components'
});

mix.babelConfig({
    plugins: ['@babel/plugin-syntax-dynamic-import']
});

mix.options({
    fileLoaderDirs: {
        images: 'img',
        fonts: 'fonts'
    },
    postCss: [require('tailwindcss')]
});

mix.setResourceRoot('/');

mix.browserSync({
    proxy: process.env.APP_URL,
    port: 3000,
    ui: false,
    files: ['app/**/**/*.php', 'resources/**/**/**/*', 'public/**/**/*'],
    snippetOptions: {
        rule: {
            match: /<\/head>/i,
            fn: function (snippet, match) {
                return snippet + match;
            }
        }
    }
});

mix.js('resources/js/app.js', 'public/js');

mix.scripts('resources/js/critical.js', 'public/js/critical.js');

mix.stylus('resources/stylus/app.styl', 'public/css', {
    includeCss: true,
    use: [require('rupture')()]
});

mix.stylus('resources/stylus/critical.styl', 'public/css', {
    includeCss: true,
    use: [require('rupture')()]
});

mix.purgeCss({
    enabled: true,
    whitelistPatternsChildren: [/pagination/]
});

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}

mix.disableSuccessNotifications();
