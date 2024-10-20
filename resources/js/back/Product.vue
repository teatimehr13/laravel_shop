<!-- <template>
    <div>
        <h2>Product</h2>
        <input type="text" v-model="test">
    </div>
</template> -->
<template>
    <div>
        <!-- <form @submit.prevent="addToCart"> -->
        <div>
            <label for="product_option_1">Product Option 1:</label>
            <input type="number" v-model="productOptions['product_option_28']" />
        </div>
        <div>
            <label for="product_option_2">Product Option 2:</label>
            <input type="number" v-model="productOptions['product_option_29']" />
        </div>
        <button type="button" @click="addToCart">Add to Cart</button>
        <button type="button" @click="deleteCartItem">Del to CartItem</button>
        <!-- </form> -->
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from '@/api/api';

let productOptions = ref({
    product_option_28: 10,
    product_option_29: 20
});

async function addToCart() {
    // console.log(productOptions.value);
    try {
        const response = await api.post('/cart/addToCart', {
            data: {
                productOptions: productOptions.value,
            },
        });
        console.log(response);
    } catch (error) {
        console.error('Error adding to cart:', error);
    }
};


async function deleteCartItem(params) {
    try {
        const response = await api.delete('/cart/deleteCartItem', {
            data: {
                product_option_id: 28
            },
        });

        if (response.status === 200 && response.data == 'success') {
            console.log(123);
        }
    } catch (error) {
        console.error('fail to delete this cartItem');

    }
}

// const token = '1|Vu96QIXL4BdC2KlqtvNfeKLV85iZ2fST5x7vyE5uce3dc6e8';
// axios.get('/api/user', {
//     headers: {
//         Authorization: `Bearer ${token}`
//     }
// })
//     .then(response => {
//         console.log(response.data);
//     })
//     .catch(error => {
//         console.error(error);
//     });

// let test = ref('');
// let products = ref();

// onMounted(
//     async () => {
//         try {
//             const response = await api.get('/products'); // 使用 api 發送請求
//             products.value = response.data; // 獲取產品數據
//             console.log(products.value);

//         } catch (error) {
//             console.error('Failed to fetch products:', error);
//         }

//     })
</script>