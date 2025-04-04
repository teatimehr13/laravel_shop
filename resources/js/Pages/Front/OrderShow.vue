<template>
    <div style="max-width: 1000px; margin: auto;">
        <div class="order-item-lists" v-for="(item, idx) in order.order_items">
            <div class="item-imgs">
                <img :src="item.image" style="max-width: 100px;">
            </div>
            <div class="item-details">
                <div class="item-name">
                    {{ item.name }}
                </div>
                <div class="item-qty">
                    x{{ item.quantity }}
                </div>
            </div>
            <div class="item-price">
                {{ toCurrency(item.price) }}
            </div>
        </div>

        <div class="order-info">
            <div class="grid-header">配送方式</div>
            <div class="grid-header">訂購通路</div>
            <div class="grid-header">訂單日期</div>
            <div class="grid-header">訂單編號</div>
            <div class="grid-header">數量</div>
            <div class="grid-header">訂購總額</div>
            <div class="grid-header">付款方式</div>

            <div class="grid-cell">宅配／電子票券</div>
            <div class="grid-cell">網路訂單</div>
            <div class="grid-cell">{{ formateDate(order.created_at) }}</div>
            <div class="grid-cell">{{ order.order_number }}</div>
            <div class="grid-cell">{{ total_qty }}</div>
            <div class="grid-cell price">{{ toCurrency(order.amount) }}</div>
            <div class="grid-cell">LINE Pay</div>
        </div>
    </div>
</template>

<script setup>
import dayjs from 'dayjs';
const props = defineProps({
    order: {
        type: Object
    },
    total_qty:{
        type: Number
    }
})

console.log(props.order);
console.log(props.total_qty);

function toCurrency(num) {
    if (!num && num !== 0) return "NT$"; // 避免 null 或 undefined
    return `NT$${Number(num).toLocaleString("en-US")}`; // 確保是數字再轉換
}

function formateDate(rawTime){
    const formatted = dayjs(rawTime).format('YYYY/MM/DD HH:mm')
    return formatted;
}
</script>

<style scoped>
.order-item-lists {
    display: flex;
    width: 100%;
}

.order-item-lists>div {
    /* width: 100%; */
}

.item-details {
    flex: 1;
}

.item-details {
    flex: 3;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 10px;
}

.item-price {
    flex: 1;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.order-info {
    display: grid;
    grid-template-columns: 1.5fr 1.5fr 2fr 2.5fr 0.5fr 1fr 1.5fr;
    border: 1px solid #ddd;
    font-size: 14px;
}

.grid-header {
  background-color: #fdeef1;
  font-weight: bold;
  padding: 8px;
  text-align: center;
  border-bottom: 1px solid #ddd;
}

.grid-cell {
  padding: 8px;
  text-align: center;
  border-bottom: 1px solid #eee;
}
</style>