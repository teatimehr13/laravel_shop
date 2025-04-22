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

        <div class="order-item-lists" v-for="(item, idx) in state.order.order_items">
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
            <div class="grid-header">操作</div>

            <div class="grid-cell">宅配</div>
            <div class="grid-cell">{{ formateDate(state.order.created_at) }}</div>
            <div class="grid-cell">{{ state.order.order_number }}</div>
            <div class="grid-cell">{{ total_qty }}</div>
            <div class="grid-cell total-price">{{ toCurrency(state.order.amount) }}</div>
            <div class="grid-cell">{{ state.order.payment_method_label ?? '-' }}</div>
            <div class="grid-cell">
                <el-button link type='primary' @click="dialogReturnToggle">
                    我要退貨
                </el-button>
            </div>
        </div>
    </div>

    <el-dialog v-model="dialogReturn" title="" class="return-dialog" align-center>
        <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick">
            <el-tab-pane label="退貨申請" name="first">
                <ReturnForm ref="returnRef" :order="state.order" :return_reasons="return_reasons"
                    :to-currency="toCurrency" @get-order-num="getOrderNum" />
            </el-tab-pane>
            <el-tab-pane label="退貨紀錄" name="second" v-loading="loading">
                <ReturnHistory ref="historyRef" :history-data="historyData" :formate-date="formateDate" :to-currency="toCurrency"/>

            </el-tab-pane>
        </el-tabs>

        <template #footer>
            <!-- <el-button type="danger" @click="submitToReturn" :disabled="!canSubmit">提交</el-button> -->
            <!-- <el-button @click="dialogReturnToggle">關閉</el-button> -->
        </template>


    </el-dialog>
</template>

<script setup>
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import ReturnForm from './Component/ReturnForm.vue';
import ReturnHistory from './Component/ReturnHistory.vue';
import dayjs from 'dayjs';
import { Memo, Money, Van, Checked } from '@element-plus/icons-vue';
import { computed, ref, reactive, onMounted, watch, watchEffect } from 'vue';
import axios from 'axios';

const props = defineProps({
    order: {
        type: Object
    },
    total_qty: {
        type: Number
    },
    return_reasons: {
        type: Array
    }
    // payment_method_label:{
    //     type: String
    // }
})

console.log(props.order);
console.log(props.total_qty);
console.log(props.return_reasons);

const state = reactive({
    order: props.order,
    total_qty: props.total_qty,
    return_reasons: props.return_reasons,
});

// console.log(props.payment_method_label);


const steps = [
    { title: '訂單成立', icon: Memo },
    { title: '付款資訊確認', icon: Money },
    { title: '運送中', icon: Van },
    { title: '已送達', icon: Checked },
]

const currentStep = state.order.step_index;
// console.log(props.order.step_index);


// 計算進度百分比
const progressWidth = computed(() => {
    if (steps.length <= 1) return '0%'
    const percent = (currentStep / (steps.length - 1)) * ((1000 - 140) / 1000 * 100);  //左右各70px
    return `${percent}%`
})


const activeName = ref('first');

const handleClick = (tab, event) => {
    // console.log(tab, event)
    console.log(tab.props.name);

    switch (tab.props.name) {
        case "first":

            break
        case "second":
            getReturnHistory();
            break
    }

}

const dialogReturn = ref(false);
// const selectReturnAll = ref(false);

const dialogReturnToggle = () => {
    dialogReturn.value = !dialogReturn.value;
}

const returnRef = ref('');
const getOrderNum = async (order_number) => {
    const response = await axios.get(`/order/fetchOrderData/${order_number}`);
    console.log(response.data);
    state.order = response.data.order;
    state.total_qty = response.data.total_qty;
    console.log(state);
    await resetReturnForm();
}

const resetReturnForm = () => {
    return returnRef.value.resetForm();
}


const showHistory = ref(false);
const historyData = ref();
const getReturnHistory = async () => {
    const orderId = props.order.id;
    const response = await axios.get(`/return/return-history/${orderId}`);
    console.log(response.data);

    try {
        const response = await axios.get(`/return/return-history/${orderId}`);
        historyData.value = response.data;
    } catch (error) {
        console.error('抓退貨資料失敗', error);
        showMessage('error', '資料讀取失敗');
    } finally {
        loading.value = false;
    }
}

const loading = ref(true);

// const handleBeforeLeave = async(tab) => {
//     console.log(tab);

//     switch (tab) {
//         case "first":
//             return true;

//         case "second":
//             return true;
//             const data = await getReturnHistory();
//             // console.log(data);
//             historyData.value = data;
//             loading.value = false;
//     }

//     return false
// }

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
</style>