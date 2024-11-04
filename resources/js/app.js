import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// import Product from './back/Product.vue';
// import Brand from './back/Brand.vue';
// import Cart from './front/Cart.vue';
// import Login from './front/Login.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const app = createApp({});

// const components = {
//     'back-product': Product,
//     'back-brand': Brand,
//     'front-cart': Cart,
//     'front-login': Login,
// };


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

        app.mount(el);
    },
});

// Object.entries(components).forEach(([name, component]) => {
//     app.component(name, component);
// });
// app.mount('#app');