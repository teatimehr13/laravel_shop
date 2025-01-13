<template>
    <!-- <FrontendLayout /> -->
    <div>
        <div v-for="(item, index) in cartItems">
            <label>名稱: {{ item.productOption.name }}</label>
            <button type="button" @click="changeValue(item.productOption.id, -1, index)">-</button>
            <input type="text" v-model="item.quantity" />
            <button type="button" @click="changeValue(item.productOption.id, 1, index)">+</button>
            <button type="button" @click="deleteCartItem(item.productOption.id)">Del</button>
        </div>

        <div>
            <label for="">Total Price : <span>{{ endPrice }}</span></label>
        </div>
    </div>

    <div>
        <div>
            <button type="button" @click="checkout">結帳</button>
        </div>
        <!-- <form @submit.prevent="logout">
            <button type="submit">logout</button>
        </form> -->
        <!-- <button @click="logout">Logout</button> -->
        <Link href="/logout" method="post" as="button" type="button">Logout</Link>
    </div>

</template>

<script setup>
import { onMounted, ref } from 'vue';
import web from '@/web/web';
import api from '@/api/api';
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3'
import FrontendLayout from '@/Layouts/FrontendLayout.vue';

// let productOptions = ref({
//     '28': 1,
//     '29': 1
// });

// let productOptions = ref({
//     '28': {'quantity':5},
//     '29': {'quantity':6}
// });

const updateCartTotal = (cartItems) => {
    const total = cartItems.value.reduce((sum, item) =>
        sum + item.productOption.price * item.quantity, 0);
    endPrice = total;
}

let changeValue = (optionKey, value, index) => {
    // console.log(cartItems.value[index]['quantity']);

    cartItems.value[index]['quantity'] += value;
    if (cartItems.value[index]['quantity'] < 1) {
        //或詢問是否刪除該項目
        cartItems.value[index]['quantity'] = 1;
        return
    }
    // console.log(optionKey);
    updateCartTotal(cartItems);
    updateCartItem(index);
}

async function deleteCartItem(id) {
    try {
        const response = await api.delete('/cart/deleteCartItem', {
            data: {
                product_option_id: id
            },
        });

        if (response.status === 200 && response.data == 'success') {
            console.log(123);
        }
    } catch (error) {
        console.error('fail to delete this cartItem');
    }
}

async function updateCartItem() {
    try {
        let transformedProductOptions = ref({});
        cartItems.value.forEach(({ productOption, quantity }) => {
            transformedProductOptions.value[productOption.id] = { quantity }; // 使用產品 ID 作為鍵，並將數量作為值
        });
        // console.log(transformedProductOptions.value);

        // const response = await api.post('/cart/updateCartItem', {
        //     data: {
        //         productOptions: transformedProductOptions.value,
        //         _method: 'patch'
        //     },
        // });

        if (response.status === 200 && response.data == 'success') {
            console.log(123);
        }
    } catch (error) {
        console.error('fail to update this cartItem');
    }
}

// async function logout(){
//     try {
//         await web.post('/logout');
//         // await api.post('/logout'); //登出請求
//         // window.location.href = '/login';
//         // console.log('Logged out successfully');
//     }catch(error){
//         console.error('Logout failed:', error);
//     }
// }


const logout = () => {
    // This will send a POST request to the /logout route
    useForm().post(route('logout'));
};

async function checkout() {
    try {
        // http://127.0.0.1:8000/api
        await api.get('/cart/checkout', {

        });
    } catch (error) {
        console.error('fail to checkout')
    }
}

let cartItems = ref();
let endPrice = ref();
onMounted(
    async () => {
        try {
            const response = await api.get('/cart'); // 使用 api 發送請求
            cartItems.value = response.data.cartItems; // 獲取產品數據
            endPrice.value = response.data.endPrice;
            console.log(response.data);

        } catch (error) {
            console.error('Failed to fetch products:', error);
        }

    })
</script>
