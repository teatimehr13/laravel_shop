import 'element-plus/dist/index.css';
// import 'element-plus/es/components/infinite-scroll/style/css';

import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import ElTableInfiniteScroll from "el-table-infinite-scroll";


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
        app.mount(el);
    },
});