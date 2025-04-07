<template>
    <FrontendLayout />
    <div class="container">
        <div class="list">
            <div class="line">
                <div class="line-background"></div>
                <div class="line-foreground" :style="{ width: progressWidth }"></div>
            </div>
            <ul class="list-con">
                <li v-for="(step, index) in steps" :key="index" :class="{ active: index <= currentStep }">
                    <el-icon>
                        <component :is="step.icon" />
                    </el-icon>
                    <div>{{ step.title }}</div>
                </li>
            </ul>
        </div>

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
            <div class="grid-header">訂單日期</div>
            <div class="grid-header">訂單編號</div>
            <div class="grid-header">數量</div>
            <div class="grid-header">訂購總額</div>
            <div class="grid-header">付款方式</div>

            <div class="grid-cell">宅配</div>
            <div class="grid-cell">{{ formateDate(order.created_at) }}</div>
            <div class="grid-cell">{{ order.order_number }}</div>
            <div class="grid-cell">{{ total_qty }}</div>
            <div class="grid-cell total-price">{{ toCurrency(order.amount) }}</div>
            <div class="grid-cell">{{ order.payment_method_label ?? '-' }}</div>
        </div>
    </div>
</template>

<script setup>
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import dayjs from 'dayjs';
import { Memo, Money, Van, Checked } from '@element-plus/icons-vue';
import { computed, ref } from 'vue';

const props = defineProps({
    order: {
        type: Object
    },
    total_qty: {
        type: Number
    },
    // payment_method_label:{
    //     type: String
    // }
})

console.log(props.order);
console.log(props.total_qty);
// console.log(props.payment_method_label);


const steps = [
    { title: '訂單成立', icon: Memo },
    { title: '付款資訊確認', icon: Money },
    { title: '運送中', icon: Van },
    { title: '已送達', icon: Checked },
]

const currentStep = props.order.step_index;
// console.log(props.order.step_index);


// 計算進度百分比
const progressWidth = computed(() => {
  if (steps.length <= 1) return '0%'
  const percent = (currentStep / (steps.length - 1)) * ((1000 - 140) / 1000 * 100);  //左右各70px
  return `${percent}%`
})


function toCurrency(num) {
    if (!num && num !== 0) return "NT$"; // 避免 null 或 undefined
    return `NT$${Number(num).toLocaleString("en-US")}`; // 確保是數字再轉換
}

function formateDate(rawTime) {
    const formatted = dayjs(rawTime).format('YYYY/MM/DD HH:mm')
    return formatted;
}
</script>

<style scoped>
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
    grid-template-columns: 1.5fr  2fr 2.5fr 0.75fr 1fr 1.5fr;
    border: 1px solid #ddd;
    font-size: 14px;
    margin: 15px 0;
}

.grid-header {
    background-color: #3d3d3d;
    color: white;
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
</style>