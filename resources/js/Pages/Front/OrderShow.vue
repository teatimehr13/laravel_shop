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
            <div class="grid-header">操作</div>

            <div class="grid-cell">宅配</div>
            <div class="grid-cell">{{ formateDate(order.created_at) }}</div>
            <div class="grid-cell">{{ order.order_number }}</div>
            <div class="grid-cell">{{ total_qty }}</div>
            <div class="grid-cell total-price">{{ toCurrency(order.amount) }}</div>
            <div class="grid-cell">{{ order.payment_method_label ?? '-' }}</div>
            <div class="grid-cell">
                <el-button link type='primary' @click="dialogReturnToggle">
                    我要退貨
                </el-button>
            </div>
        </div>
    </div>

    <el-dialog v-model="dialogReturn" title="退貨" class="return-dialog" align-center>
        <div v-for="(item, idx) in order.order_items" class="return-con-layout" :class="{ 'return-con-border': selected_orderItems[item.id].quantity > 0 }">
            <i class="checkmark" v-if="selected_orderItems[item.id].quantity > 0">
                <el-icon><Select /></el-icon>
            </i>
            <!-- <div class="item-chks">
                <el-checkbox-group v-model="selectedIds">
                    <el-checkbox size="large" :value="item.id" :key="item.id" />
                </el-checkbox-group>
            </div> -->
            <div class="item-imgs">
                <img :src="item.image" style="max-width: 80px;">
            </div>
            <div class="item-details">
                <div class="item-name flex">
                    <div>商品名稱</div>
                    <div>
                        {{ item.name }}
                    </div>
                </div>
                <div class="item-qty flex">
                    <div>訂單數量</div>
                    <div>{{ item.quantity }}</div>
                </div>
                <div class="flex">
                    <div>單價</div>
                    <div>
                        {{ toCurrency(item.price) }}
                    </div>
                </div>
                <div class="flex">
                    <div>退貨數量</div>
                    <div>
                        <!-- <el-select v-model="selected_orderItems[item.id].quantity" placeholder="選取數量"
                            style="width: 70px">
                            <el-option v-for="qty in item.quantity" :key="qty" :label="qty" :value="qty" />
                        </el-select> -->
                        <el-input-number v-model="selected_orderItems[item.id].quantity" :min="0" :max="item.quantity" size="small"  style="width: 100px;"/>
                    </div>
                </div>
                <div class="flex">
                    <div>退貨原因</div>
                    <div>
                        <el-select v-model="selected_orderItems[item.id].quantity" placeholder="選取數量"
                            style="width: 150px">
                            <el-option v-for="qty in item.quantity" :key="qty" :label="qty" :value="qty" />
                        </el-select>
                    </div>
                </div>
                <div class="flex">
                    <div>
                        描述
                    </div>
                    <div>
                        <el-input maxlength="30" style="width: 300px" show-word-limit type="textarea" />
                    </div>
                </div>
            </div>
            <div class="item-fill">
                <!-- <div>
                    <el-select v-model="selected_orderItems[item.id].quantity" placeholder="選取數量" style="width: 100px">
                        <el-option v-for="qty in item.quantity" :key="qty" :label="qty" :value="qty" />
                    </el-select>
                </div>

                <div>
                    <el-input  maxlength="30" style="width: 200px" placeholder="Please input"
                        show-word-limit type="textarea" />
                </div> -->
            </div>
            <!-- <div class="item-price">
                {{ toCurrency(item.price) }}
            </div> -->
        </div>
    </el-dialog>
</template>

<script setup>
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import dayjs from 'dayjs';
import { Memo, Money, Van, Checked } from '@element-plus/icons-vue';
import { computed, ref, reactive, onMounted } from 'vue';

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

const selected_orderItems = reactive({})

onMounted(() => {
    props.order.order_items.forEach((item) => {
        selected_orderItems[item.id] = {
            quantity: 0,
        }
    })
})

const dialogReturn = ref(false);
const selectedIds = ref([])

const dialogReturnToggle = () => {
    dialogReturn.value = !dialogReturn.value;
}

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

::v-deep(.return-dialog) {
    min-width: 700px;
}


.return-con-layout {
    display: flex;
    border-bottom: 1px solid #e8e7e7;
    position: relative;
    margin: 10px 0;
}
.return-con-border {
    border: 1px dashed #ee4d2d;
    border-radius: 3px; 
}

.item-chks {
    margin: auto;

}

.item-imgs {
    margin-inline-end: 10px;
    padding-inline: 5px;
}

.flex {
    display: flex;
    align-items: center;
    padding: 5px;
    flex-wrap: nowrap;
}

.flex:not(:last-of-type) {
    border-bottom: .5px dashed #e8e7e7;
}

.flex div:first-of-type {
    flex: 1;

}

.flex div:last-of-type {
    flex: 6;
}

.checkmark {
    top: -1px;
    height: 1.875rem;
    overflow: hidden;
    position: absolute;
    right: -1px;
    width: 1.875rem;
}

.checkmark .el-icon {
    bottom: 0;
    color: #fff;
    font-size: 8px;
    position: absolute;
    right: 0;
    transform: translate(-50%, 100%);
    top: 0;
}

.checkmark::before {
    /* border: .9375rem solid transparent;
    border-bottom: .9375rem solid var(--brand-primary-color, #ee4d2d); */
    border-top: 1.875rem solid var(--brand-primary-color, #ee4d2d);
    border-left: 1.875rem solid transparent;
    bottom: 0;
    content: "";
    position: absolute;
    /* right: -.9375rem; */
}
</style>


<!-- <i v-show="paymentMethod === 'credit'" class="checkmark">
    <el-icon><Select /></el-icon>
</i> -->

<!-- border: 1px solid #ee4d2d;
border-radius: 3px; -->