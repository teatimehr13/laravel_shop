// import './bootstrap';
// import '../css/app.css';

// import { createApp, h } from 'vue';
// import { createInertiaApp } from '@inertiajs/vue3';
// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// import Product from './back/Product.vue';
// import Brand from './back/Brand.vue';
// import Cart from './front/Cart.vue';
// import Login from './front/Login.vue';

// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
// const app = createApp({});
// // app.component([{'back-product': Product}, {'back-brand':Brand}]);
// const components = {
//     'back-product': Product,
//     'back-brand': Brand,
//     'front-cart': Cart,
//     'front-login': Login
// };

// createInertiaApp({
//     title: (title) => `${title} - ${appName}`,
//     resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
//     setup({ el, App, props, plugin }) {
//         return createApp({ render: () => h(App, props) })
//             .use(plugin)
//             .use(ZiggyVue)
//             .mount(el);
//     },
//     progress: {
//         color: '#4B5563',
//     },
// });

// Object.entries(components).forEach(([name, component]) => {
//     app.component(name, component);
// });

// app.mount('#app');

import './bootstrap';

import { createApp } from 'vue';
import Product from './back/Product.vue';
import Brand from './back/Brand.vue';
import Cart from './front/Cart.vue';
import Login from './front/Login.vue';

const app = createApp({});
// app.component([{'back-product': Product}, {'back-brand':Brand}]);
const components = {
    'back-product': Product,
    'back-brand': Brand,
    'front-cart': Cart,
    'front-login': Login
};

Object.entries(components).forEach(([name, component]) => {
    app.component(name, component);
});

app.mount('#app');