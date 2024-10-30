import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // 引入 Vue 插件

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'], // 恢復原始的 input 配置
            refresh: true,
        }),
        vue(), // 添加 Vue 插件
    ],
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js' // 使用包含編譯器的 Vue 版本
        },
    },
});
