import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';  // このインポートを追加


export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        // vue({
        //     template: {
        //         transformAssetUrls: {
        //             base: null,
        //             includeAbsolute: false,
        //         },
        //     },
        // }),
        vue(),
    ],
    publicDir: 'public',

    resolve: {
        alias: {
            // vue: 'vue/dist/vue.esm-bundler.js',
            '~': path.resolve(__dirname, 'public'),

        },
        // extensions: ['.vue', '.js'],
    },
    build: {
        rollupOptions: {
            external: [
                /^\/images\/.*/  // 外部リソースとして画像を指定
            ]
        }
    }


});
