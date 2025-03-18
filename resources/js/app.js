import 'element-plus/dist/index.css';
// import 'element-plus/es/components/infinite-scroll/style/css';

import './bootstrap';
import '../css/app.css';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import * as ElementPlusIconsVue from '@element-plus/icons-vue';
import ElTableInfiniteScroll from "el-table-infinite-scroll";
import { QuillEditor } from '@vueup/vue-quill';



const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const app = createApp({});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    // resolvePageComponent(`./${name}.vue`, import.meta.glob('./**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);

        // 使用 Ziggy 路由插件
        app.use(ZiggyVue);
        app.use(ElTableInfiniteScroll);

        // 註冊 Element Plus 圖標
        for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
            app.component(key, component);
        }

        app.component('QuillEditor', QuillEditor);
        app.mount(el);
    },
});