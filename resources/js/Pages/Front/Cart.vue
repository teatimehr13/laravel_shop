<template>
    <!-- <FrontendLayout /> -->
    <!-- <div>
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
    </div> -->
    <div v-for="(item, index) in cartItems" class="cart-con">
        <div class="cart-img">
            <img :src="item.productOption.image">
        </div>
        <div class="cart-info">
            <div class="cart-info-con">
                <div class="cart-info-title">{{ item.productOption.product.name }}</div>
                <div class="cart-del"><el-icon>
                        <Close />
                    </el-icon></div>
            </div>
            <div>{{ item.productOption.product.title }}</div>
            <div class="cart-info-bottom">
                <div class="cart-price">
                    {{ item.productOption.price }}
                </div>
                <div class="cart-quantity">
                    <el-input-number v-model="item.quantity" :min="1" :max="20" @change="updateCartItem(item)" />
                    <!-- {{ item.quantity }} -->
                </div>
            </div>
        </div>
    </div>

    <div>
        <div>
            <!-- <button type="button" @click="checkout">結帳</button> -->
        </div>
        <!-- <form @submit.prevent="logout">
            <button type="submit">logout</button>
        </form> -->
        <!-- <button @click="logout">Logout</button> -->
        <!-- <Link href="/logout" method="post" as="button" type="button">Logout</Link> -->
    </div>

</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import axios from 'axios';
import { debounce } from 'lodash';

const props = defineProps({
    cartItems: {
        type: Array,
        require: true
    },
    endPrice: {
        type: Number,
        require: true
    }
})

console.log(props.cartItems);
console.log(props.endPrice);



let cartItems = reactive({ ...props.cartItems });
let endPrice = ref(props.endPrice);

console.log(cartItems);
console.log(endPrice.value);


// const updateCartTotal = (cartItems) => {
//     const total = cartItems.value.reduce((sum, item) =>
//         sum + item.productOption.price * item.quantity, 0);
//     endPrice = total;
// }

// let changeValue = (optionKey, value, index) => {
//     // console.log(cartItems.value[index]['quantity']);

//     cartItems.value[index]['quantity'] += value;
//     if (cartItems.value[index]['quantity'] < 1) {
//         //或詢問是否刪除該項目
//         cartItems.value[index]['quantity'] = 1;
//         return
//     }
//     // console.log(optionKey);
//     updateCartTotal(cartItems);
//     updateCartItem(index);
// }

// async function deleteCartItem(id) {
//     try {
//         const response = await api.delete('/cart/deleteCartItem', {
//             data: {
//                 product_option_id: id
//             },
//         });

//         if (response.status === 200 && response.data == 'success') {
//             console.log(123);
//         }
//     } catch (error) {
//         console.error('fail to delete this cartItem');
//     }
// }

// async function updateCartItem() {
//     try {
//         let transformedProductOptions = ref({});
//         cartItems.value.forEach(({ productOption, quantity }) => {
//             transformedProductOptions.value[productOption.id] = { quantity }; // 使用產品 ID 作為鍵，並將數量作為值
//         });
//         // console.log(transformedProductOptions.value);

//         const response = await api.post('/cart/updateCartItem', {
//             data: {
//                 productOptions: transformedProductOptions.value,
//                 _method: 'patch'
//             },
//         });

//         if (response.status === 200 && response.data == 'success') {
//             console.log(123);
//         }
//     } catch (error) {
//         console.error('fail to update this cartItem');
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

const updateCartItem = debounce(async (item) => {
    let obj = {
        product_option_id: item.productOption.id,
        quantity: item.quantity
    }

    console.log(obj);

    const response = await axios.patch('/cart/updateCartItem', obj);
    // console.log(response.data);
    // console.log(item);
}, 500);

// onMounted(
//     async () => {
//         try {
//             const response = await api.get('/cart'); // 使用 api 發送請求
//             cartItems.value = response.data.cartItems; // 獲取產品數據
//             endPrice.value = response.data.endPrice;
//             console.log(response.data);

//         } catch (error) {
//             console.error('Failed to fetch products:', error);
//         }

//     })
</script>

<style scoped>
.cart-con {
    display: flex;
    padding: 20px;
    border-bottom: 1px solid rgb(211, 211, 211);
    max-width: 600px;
}

.cart-img img {
    width: 120px;
    height: 120px;
    margin: auto;
}

.cart-img {
    display: flex;
    flex-grow: .3;
}

.cart-info {
    display: flex;
    flex-direction: column;
    flex-grow: .7;
}

.cart-info-con {
    display: flex;
}

.cart-info-title {
    flex: 1 1 auto;
    overflow: hidden;
}

.cart-del {
    flex: 0 0 auto;
    overflow: hidden;
}

.cart-info-bottom {
    display: flex;
    flex-grow: 1;
    align-items: end;
}

.cart-price {
    flex: 1 1 auto;
    overflow: hidden;
}

.cart-quantity {
    flex: 0 0 auto;
    overflow: hidden;
}
</style>