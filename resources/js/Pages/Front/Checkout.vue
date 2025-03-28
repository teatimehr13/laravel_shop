<template>
    <!-- <div class="checkout-page">
        <h2>訂單商品</h2>

        <div v-for="item in cartItems" :key="item.productOption.id" class="checkout-item">
            <img :src="item.productOption.image" alt="商品圖片" width="100" />
            <div>{{ item.productOption.name }} - {{ item.productOption.color_name }}</div>
            <div>單價：{{ formatCurrency(item.productOption.price) }}</div>
            <div>數量：{{ item.quantity }}</div>
            <div>小計：{{ formatCurrency(item.productOption.price * item.quantity) }}</div>
        </div>

        <hr />

        <h3>寄送資訊</h3>
        <label>
            姓名：<input v-model="form.name" type="text" />
        </label>
        <label>
            電話：<input v-model="form.phone" type="text" />
        </label>
        <label>
            地址：<input v-model="form.address" type="text" />
        </label>

        <label>
            備註：<input v-model="form.remark" type="text" />
        </label>

        <div class="total">
            運費：{{ formatCurrency(shippingFee) }}<br />
            總付款金額：<strong>{{ formatCurrency(totalPrice + shippingFee) }}</strong>
        </div>

        <button @click="submitOrder">下訂單</button>
    </div> -->

    <div class="checkout-container">
        <div v-if="checkoutItems.length">
            <div class="checkout-con-layout">
                <div class="chk-product">訂單商品名稱</div>
                <div class="empty"></div>
                <div class="chk-price">單價</div>
                <div class="chk-qty">數量</div>
                <div class="chk-total-price">總價</div>
            </div>
            <div class="checkout-con-layout" v-for="(item, idx) in checkoutItems" :key="item.productOption.id">
                <div class="chk-product">
                    <div class="chk-product-img">
                        <img :src="item.productOption.image" style="max-width: 50px;">
                    </div>
                    <div class="chk-product-name">
                        {{ item.product.name }}
                        ({{ item.productOption.color_name }})
                    </div>
                </div>
                <div class="empty"></div>
                <div class="chk-price">{{ toCurrency(item.productOption.price) }}</div>
                <div class="chk-qty">{{ item.quantity }}</div>
                <div class="chk-subtotal-price">{{ toCurrency(item.subtotal) }}</div>
            </div>

            <div class="checkout-con-layout">
                <div class="chk-total-text">
                    訂單總金額
                </div>
                <div class="chk-total-price">
                    {{ toCurrency(checkoutTotal) }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// 模擬購物車資料（你可從 props 傳入）
const props = defineProps({
    checkoutItems: {
        type: Array,
    },
    checkoutTotal: {
        type: Number
    }
});

console.log(props.checkoutItems);
console.log(props.checkoutTotal);

function toCurrency(num) {
    if (!num && num !== 0) return "NT$"; // 避免 null 或 undefined
    return `NT$${Number(num).toLocaleString("en-US")}`; // 確保是數字再轉換
}


// const cartItems = ref([

// ])


</script>

<style scoped>
.checkout-container {
    --grid-columns: 12;
    --offset-col-4: calc(100% / var(--grid-columns) * 4);
    --grid-span-2: span 2 / span 2;
    --grid-span-4: span 4 / span 4;
    --grid-span-8: span 8 / span 8;
    --grid-span-1: span 1 / span 1;
    --grid-span-3: span 3 / span 3;
}

.checkout-container {
    max-width: 1200px;
    margin: auto;
    padding: 0px 40px;
}

.checkout-con-layout {
    display: grid;
    grid-template-columns: repeat(var(--grid-columns), 1fr);
    padding: 10px;
    border-bottom: 1px solid rgb(211, 211, 211);
    margin-bottom: 10px;
}

.chk-product {
    grid-column: var(--grid-span-4);
    display: flex;
    align-items: center;
    gap: 10px;
}

.chk-price, .chk-qty, .chk-subtotal-price, .empty, .chk-total-price {
    grid-column: var(--grid-span-2);
}

.chk-total-text {
    grid-column: 9 / 11;
}

.chk-total-price {
    grid-column: 11 / 12;
}
</style>