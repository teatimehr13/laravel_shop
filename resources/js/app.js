import './bootstrap';

import { createApp } from 'vue';
import Product from './back/Product.vue';
import Brand from './back/Brand.vue';
import Cart from './front/Cart.vue';

const app = createApp({});
// app.component([{'back-product': Product}, {'back-brand':Brand}]);
const components = {
    'back-product': Product,
    'back-brand': Brand,
    'front-cart': Cart
};

Object.entries(components).forEach(([name, component]) => {
    app.component(name, component);
});

app.mount('#app');