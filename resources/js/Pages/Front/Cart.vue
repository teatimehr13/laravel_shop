<template>
    <FrontendLayout />


    <div class="page-container cart-container-basic">
        <!-- 標題區塊 -->
        <div class="bg-gray-100 rounded-md px-4 py-3 shadow-sm mb-6 col-span-12">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-800">購物車</h1>
        </div>

        <div v-if="cartItems.length" class="grid grid-cols-1 md:grid-cols-3 gap-6 col-span-12">
            <!-- 左側：商品列表 -->
            <div class="md:col-span-2 space-y-4">
                <div class="cart-con-layout">
                    <div style="grid-column: span 1; margin: auto;">
                        <el-checkbox :model-value="selectedIds.length === cartItems.length"
                            @change="toggleAll"></el-checkbox>
                    </div>
                </div>

                <div v-for="(item, index) in cartItems" :key="item.productOption.id"
                    class="cart-con-layout bg-white rounded-md shadow-sm p-4 border">
                    <div class="cart-chk">
                        <el-checkbox-group v-model="selectedIds">
                            <el-checkbox size="large" :value="item.productOption.id" />
                        </el-checkbox-group>
                    </div>
                    <div class="cart-img">
                        <img :src="item.productOption.image" class="w-24 h-24 object-cover rounded" />
                    </div>
                    <div class="cart-info py-2 flex-1">
                        <div class="cart-info-con flex justify-between items-start">
                            <div>
                                <div class="cart-info-title font-semibold text-base md:text-lg font-semibold">{{ item.productOption.product.name }}</div>
                                <div class="text-sm text-gray-500">{{ item.productOption.color_name }}</div>
                            </div>
                            <el-popconfirm title="確認要刪除這個商品嗎？" @confirm="deleteCartItem(item)" :width="200"
                                :hide-after="100" v-model:visible="popconfirmVisible[item.productOption.id]">
                                <template #reference>
                                    <el-button size="small">
                                        <el-icon>
                                            <Close />
                                        </el-icon>
                                    </el-button>
                                </template>
                                <template #actions="{ confirm, cancel }">
                                    <el-button size="small" @click="cancel">沒有</el-button>
                                    <el-button type="danger" size="small" @click="confirm">是</el-button>
                                </template>
                            </el-popconfirm>
                        </div>

                        <div class="cart-info-bottom mt-2 flex justify-between items-end">
                            <div class="cart-price text-red-600 font-bold text-lg text-gray-800">
                                {{ toCurrency(parseInt(item.productOption.price) * parseInt(item.quantity)) }}
                            </div>
                            <div class="cart-quantity">
                                <el-input-number v-model="item.quantity" :min="1" :max="20"
                                    @change="updateCartItem(item)" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 右側：結帳明細 -->
            <div class="bg-white shadow-sm rounded-md p-4 border h-fit max-w-80 hidden md:block sticky top-4">
                <h2 class="text-lg font-bold mb-4">結帳明細</h2>
                <div class="flex justify-between mb-2">
                    <span>商品原價總金額</span>
                    <span>{{ toCurrency(cartTotal) }}</span>
                </div>
                <div class="flex justify-between mb-4">
                    <span class="font-semibold">結帳金額</span>
                    <span class="text-red-600 font-bold">{{ toCurrency(cartTotal) }}</span>
                </div>
                <el-button type="primary" size="large" @click="checkout" :disabled="selectedItems.length === 0"
                    class="w-full">
                    {{ isLoggedIn ? '結帳' : '登入後結帳' }}
                </el-button>
            </div>


        </div>
    </div>

    <!-- <div v-if="cartItems.length">
            <div class="cart-con-layout">
                <div style="grid-column: span 1; margin: auto; ">
                    <el-checkbox :model-value="selectedIds.length === cartItems.length"
                        @change="toggleAll"></el-checkbox>
                </div>
            </div>

            <div v-for="(item, index) in cartItems" class="cart-con-layout">
                <div class="cart-chk">
                    <el-checkbox-group v-model="selectedIds">
                        <el-checkbox size="large" :value="item.productOption.id" :key="item.productOption.id" />
                    </el-checkbox-group>
                </div>
                <div class="cart-img">
                    <img :src="item.productOption.image">
                </div>
                <div class="cart-info py-2">
                    <div class="cart-info-con">
                        <div class="cart-info-title">{{ item.productOption.product.name }} </div>
                        <el-popconfirm title="確認要刪除這個商品嗎？" @confirm="deleteCartItem(item)" :width="200"
                            :hide-after="100" v-model:visible="popconfirmVisible[item.productOption.id]">
                            <template #reference>
                                <el-button size="small">
                                    <el-icon>
                                        <Close />
                                    </el-icon>
                                </el-button>
                            </template>
                            <template #actions="{ confirm, cancel }">
                                <el-button size="small" @click="cancel">沒有</el-button>
                                <el-button type="danger" size="small" @click="confirm">
                                    是
                                </el-button>
                            </template>
                        </el-popconfirm>
                    </div>
                    <div>{{ item.productOption.color_name }}</div>
                    <div class="cart-info-bottom">
                        <div class="cart-price">
                            {{ toCurrency(parseInt(item.productOption.price) * parseInt(item.quantity)) }}
                        </div>
                        <div class="cart-quantity">
                            <el-input-number v-model="item.quantity" :min="1" :max="20"
                                @change="updateCartItem(item)" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="cart-con-layout summary">
                <div class="cart-price-total">
                    <div class="count-text">
                        <span>小計</span>
                    </div>
                    <div class="count-price">
                        ({{ selectedCount }} 個商品)
                        {{ toCurrency(cartTotal) }}
                    </div>
                </div>
            </div>
            <div class="chkout">
                <el-button type="primary" size="large" @click="checkout" :disabled="selectedItems.length === 0"
                    class="bg-gray-300 text-white">
                    {{ isLoggedIn ? '結帳' : '登入後結帳' }}
                </el-button>
            </div>
        </div>
        <div v-else>
            沒有任何商品。
        </div> -->

    <div>
        <!-- <el-button type="primary" @click="logout">登出</el-button> -->
    </div>


    <div class="fixed bottom-0 left-0 w-full bg-gray-100 shadow-t p-2 flex justify-between items-center md:hidden z-10">
        <span class="font-bold">結帳金額 
            <span :class="{'text-red-600': selectedItems.length != 0}">
                {{ toCurrency(cartTotal) }}
            </span>
        </span>
        <el-button type="primary" size="default" @click="checkout" :disabled="selectedItems.length === 0">
            結帳
        </el-button>
    </div>

</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import axios from 'axios';
import { debounce } from 'lodash';
import { router, useForm } from '@inertiajs/vue3';

const cartForm = useForm({})

const props = defineProps({
    cartItems: {
        type: Array,
        require: true
    },
    endPrice: {
        type: Number,
        require: true
    },
    auth: {
        type: Object
    }
})

// console.log(props.cartItems.value);
// console.log(props.endPrice);


const isLoggedIn = computed(() => !!props.auth.user)
// console.log(isLoggedIn);


let cartItems = ref([...props.cartItems]);
let endPrice = ref(props.endPrice);

console.log(cartItems.value);
console.log(endPrice.value);

const logout = () => {
    cartForm.post(route('logout'));
};

async function checkout() {
    // try {
    //     // http://127.0.0.1:8000/api
    //     await api.get('/cart/checkout', {

    //     });
    // } catch (error) {
    //     console.error('fail to checkout')
    // }

    if (isLoggedIn.value) {
        router.visit('/checkout', {
            method: 'get',
            data: {
                selected_ids: selectedIds.value
            }
        })
    } else {
        router.visit('/login?redirect=checkout')
    }
}

const updateCartItem = debounce(async (item) => {
    let obj = {
        product_option_id: item.productOption.id,
        quantity: item.quantity
    }

    console.log(obj);

    const response = await axios.patch('/cart/updateCartItem', obj);
}, 500);

const deleteCartItem = async (item) => {
    try {
        let obj = {
            product_option_id: item.productOption.id
        }
        console.log(obj);
        const response = await axios.delete('/cart/deleteCartItem', { params: obj });
        if (response.data.status === 'success') {
            showMessage(response.data.status, response.data.msg);
            cartItems.value = cartItems.value.filter(cartItem => cartItem.productOption.id != item.productOption.id);
        }
    } catch (error) {
        showMessage(response.data.ststus, response.data.msg);
    }


}

// Popconfirm 的顯示狀態
const popconfirmVisible = ref({});

//通知
const showMessage = (type, title) => {
    ElNotification({
        type, // "success" 或 "error"
        title,
        position: 'bottom-left',
    });
};

function toCurrency(num) {
    if (!num && num !== 0) return "NT$"; // 避免 null 或 undefined
    return `NT$${Number(num).toLocaleString("en-US")}`; // 確保是數字再轉換
}

const selectedIds = ref([])

const selectedItems = computed(() =>
    cartItems.value.filter(item =>
        selectedIds.value.includes(item.productOption.id)
    )
)

const selectedCount = computed(() => selectedItems.value.length)

const cartTotal = computed(() =>
    selectedItems.value.reduce((sum, item) => {
        const price = Number(item.productOption?.price || 0)
        const quantity = Number(item.quantity || 0)
        return sum + price * quantity
    }, 0)
)

const toggleAll = () => {
    if (selectedIds.value.length === cartItems.value.length) {
        selectedIds.value = []
    } else {
        selectedIds.value = cartItems.value.map(i => i.productOption.id)
    }
}
</script>

<style scoped>
.cart-container-basic {
    --grid-columns: 12;
    --offset-col-4: calc(100% / var(--grid-columns) * 4);
    --grid-span-4: span 4 / span 4;
    --grid-span-8: span 8 / span 8;
    --grid-span-1: span 1 / span 1;
    --grid-span-3: span 3 / span 3;
}

.cart-container {
    max-width: 1200px;
    margin: auto;
    padding: 1rem 2rem;
}

.cart-con-layout {
    display: grid;
    grid-template-columns: repeat(var(--grid-columns), 1fr);
    padding: 10px;
    border-bottom: 1px solid rgb(211, 211, 211);
    margin-bottom: 10px;
}

.cart-img img {
    max-width: 120px;
    width: 100%;
}

.cart-img {
    display: flex;
    margin: auto;
    grid-column: var(--grid-span-3);
    /* flex-grow: .3; */
}

.cart-info {
    display: flex;
    flex-direction: column;
    grid-column: var(--grid-span-8);
    /* flex-grow: .7; */
}

.cart-chk {
    grid-column: var(--grid-span-1);
    margin: auto;
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
    font-size: 1.25rem;
    font-weight: bold;
    color: #55595c;
}

.cart-quantity {
    flex: 0 0 auto;
    overflow: hidden;
}

.cart-price-total {
    display: flex;
}

.count-text {
    flex: 1 1 auto;
    overflow: hidden;
}

.count-price {
    flex: 0 0 auto;
    overflow: hidden;
    font-size: 1.25rem;
    font-weight: bold;
    color: #55595c;
}

.chkout {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
}

.chkout button {
    font-size: 1.25rem;
    padding: 25px 40px;
}


.cart-title {
    padding-bottom: 50px
}

.cart-title h1 {
    font-size: 2rem;
    font-weight: bold;
    background: #f5f7fa;
    padding: 10px;
    border-radius: 3px;
    color: #606266;
}

.summary {
    margin-inline-start: var(--offset-col-4);
    grid-template-columns: 100%;
    padding-left: 0;
}
</style>