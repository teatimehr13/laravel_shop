import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // 引入 Vue 插件

import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'], // 恢復原始的 input 配置
            refresh: true,
        }),
        vue(), // 添加 Vue 插件
        AutoImport({
            resolvers: [ElementPlusResolver()],
          }),
          Components({
            resolvers: [ElementPlusResolver()],
          }),
    ],
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js' // 使用包含編譯器的 Vue 版本
        },
    },
});
