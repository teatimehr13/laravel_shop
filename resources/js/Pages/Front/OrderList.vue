<template>
    <FrontendLayout />
    <div class="container">
        <h1 class="order-intro">
            以下為近三年，您的全部訂單
        </h1>

        <div class="order-intro-line"></div>

        <div class="order-info">
            <div class="grid-header">配送方式</div>
            <div class="grid-header">訂單日期</div>
            <div class="grid-header">訂單編號</div>
            <div class="grid-header">數量</div>
            <div class="grid-header">訂購總額</div>
            <div class="grid-header">付款方式</div>
            <div class="grid-header">操作</div>

            <!-- <div class="grid-cell">宅配</div>
            <div class="grid-cell">{{ formateDate(state.order.created_at) }}</div>
            <div class="grid-cell">{{ state.order.order_number }}</div>
            <div class="grid-cell">{{ total_qty }}</div>
            <div class="grid-cell total-price">{{ toCurrency(state.order.amount) }}</div>
            <div class="grid-cell">{{ state.order.payment_method_label ?? '-' }}</div>
            <div class="grid-cell">
                <el-button link type='primary' @click="dialogReturnToggle">
                    我要退貨
                </el-button>
            </div> -->
            <template v-for="(order,key) in orders" :key="order.id">
                <div class="grid-cell">宅配</div>
                <div class="grid-cell">{{ formateDate(order.created_at) }}</div>
                <div class="grid-cell">{{ order.order_number }}</div>
                <div class="grid-cell">{{ order.total_qty ?? '-'}}</div>
                <div class="grid-cell">
                    {{ toCurrency(order.amount) }}
                </div>
                <div class="grid-cell">
                    {{ order.payment_method_label ?? '-' }}
                </div>
                <div class="grid-cell">
                    <el-button link type='primary'>
                        <a :href="`/order/${order.order_number}`">
                            查看訂單
                        </a>
                    </el-button>
                </div>
            </template>
                
        </div>
    </div>
</template>

<script setup>
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import dayjs from 'dayjs';
import { usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';

// console.log(usePage().props.orders);
const orders = reactive(usePage().props.orders);
console.log(orders);


//通知
const showMessage = (type, title) => {
    ElNotification({
        type, // "success" 或 "error"
        title,
        position: 'bottom-left',
    });
};

function toCurrency(num) {
    if (!num && num !== 0) return "$"; // 避免 null 或 undefined
    return `$${Number(num).toLocaleString("en-US")}`; // 確保是數字再轉換
}

function formateDate(rawTime) {
    const formatted = dayjs(rawTime).format('YYYY/MM/DD HH:mm')
    return formatted;
}
</script>

<style>
.container {
    margin: auto;
    max-width: 1000px;
}


.order-item-lists {
    display: flex;
    width: 100%;
    background: #fafafa;
    padding: 10px;
    box-sizing: border-box;
    border-bottom: 1px dashed #e8e7e7;
}

.order-item-lists>div {
    /* width: 100%; */
}

.item-details {
    flex: 2;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 10px;
    gap: 10px;

}

.item-price {
    flex: 1;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.order-info {
    display: grid;
    grid-template-columns: 1.5fr 2fr 2.5fr 0.75fr 1fr 1.5fr 1fr;
    border: 1px solid #ddd;
    font-size: 14px;
    margin: 15px 0;
}

.grid-header {
    /* background-color: #3d3d3d; */
    color: #909399;
    font-weight: bold;
    padding: 8px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.grid-header + .grid-header,
.grid-cell + .grid-cell {
    border-left: 1px solid #ebeef5;
}

.grid-cell {
    padding: 8px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

.list {
    position: relative;
    margin-bottom: 60px;
    padding: 10px 25px;
}

.line {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    /* left: 70px;
    right: 70px; */
    height: 4px;
    z-index: 0;
}

.line-background,
.line-foreground {
    height: 4px;
    position: absolute;
    left: 70px;
    right: 70px;
    border-radius: 3px;
}

.line-background {
    background-color: #e0e0e0;
    z-index: 0;
}

.line-foreground {
    background-color: #3bc371;
    z-index: 1;
    width: 0%;
    transition: width 0.4s;
}

.list-con {
    display: flex;
    justify-content: space-between;
    position: relative;
    z-index: 2;
}

.list-con li {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 80px;
    height: 80px;
    border: 4px solid #3bc371;
    border-radius: 50%;
    justify-content: center;
    font-weight: bolder;
    background-color: white;
    color: #3bc371;
    box-shadow: 0 0 0 3px #fff;
    position: relative;
}

.list-con li:not(.active) {
    border-color: #ddd;
    color: #ddd;
}

.list-con li .el-icon {
    font-size: 2rem;
}

.list-con li div {
    position: absolute;
    bottom: -40px;
    white-space: nowrap;
    color: #55595c;
}

.total-price {
    color: #e42c2c;
    font-weight: bold;
}

.return-dialog {
    min-width: 600px;
    max-width: max-content;
    min-height: 500px;
}

.item-chks {
    margin: auto;

}

.item-imgs {
    margin-inline-end: 10px;
    padding-inline: 5px;
}

.order-intro {
    font-size: 40px;
    font-weight: 600;
    letter-spacing: 0;
    line-height: 1.1;
    padding-bottom: 2px;
    text-align: center;
    margin: 2rem;
}

.order-intro-line {
    border-bottom: 1px solid #d2d2d7;
    margin-bottom: 2rem;
    padding-top: 10px;
    text-align: center;
}
</style>