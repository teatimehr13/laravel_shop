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

    <el-dialog v-model="dialogReturn" title="" class="return-dialog" align-center>
        <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick">
            <el-tab-pane label="退貨申請" name="first">
                <!-- <ReturnForm /> -->
                <div>
                    <el-checkbox v-model="selectReturnAll" label="全選" @change="handleSelectReturnAll" border />
                </div>
                <div v-for="(item, idx) in order.order_items" class="return-con-layout"
                    :class="{ 'return-con-border': selected_orderItems[item.id].quantity > 0 }">
                    <i class="checkmark" v-if="selected_orderItems[item.id].quantity > 0">
                        <el-icon><Select /></el-icon>
                    </i>
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
                                <el-input-number v-model="selected_orderItems[item.id].quantity" :min="0"
                                    :max="item.available_qty" size="small" style="width: 100px;"
                                    @change="(val) => handleQuantityChange(item.id, val)" />
                                <el-text style="margin: 0 10px;" type="danger">(可退數量：{{ item.available_qty }})</el-text>
                            </div>
                        </div>
                        <div class="flex" v-show="selected_orderItems[item.id].quantity > 0">
                            <div>退貨原因</div>
                            <div>
                                <el-form :model="selected_orderItems[item.id]" :rules="rules"
                                    :ref="el => (formRefs[item.id] = el)">
                                    <el-form-item prop="reason">
                                        <el-select v-model="selected_orderItems[item.id].reason" placeholder="選擇退貨原因"
                                            style="width: 300px; display: block; ">
                                            <el-option v-for="(reason, idx) in return_reasons" :key="idx"
                                                :label="reason.label" :value="reason.value" />
                                        </el-select>
                                    </el-form-item>
                                </el-form>
                            </div>
                        </div>
                        <div class="flex" v-show="selected_orderItems[item.id].quantity > 0">
                            <div>
                                描述
                            </div>
                            <div>
                                <el-input maxlength="30" style="width: 300px" show-word-limit type="textarea"
                                    placeholder="選填" v-model="selected_orderItems[item.id].description" />
                            </div>
                        </div>
                    </div>
                </div>
                <el-button type="danger" @click="submitToReturn" :disabled="!canSubmit" style="margin: 10px; float: right;">提交</el-button>

            </el-tab-pane>
            <el-tab-pane label="退貨紀錄" name="second">Config</el-tab-pane>
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

const selected_orderItems = reactive({});

onMounted(() => {
    props.order.order_items.forEach((item) => {
        selected_orderItems[item.id] = {
            quantity: 0,
            reason: null,
            description: ''
        }
    })
})

const canSubmit = computed(() => {
    return Object.values(selected_orderItems).some(item => {
        return item.quantity > 0 && item.reason;
    });

});

console.log(canSubmit.value);

const activeName = ref('first')

const handleClick = (tab, event) => {
    console.log(tab, event)
}

const dialogReturn = ref(false);
const selectReturnAll = ref(false);

const dialogReturnToggle = () => {
    dialogReturn.value = !dialogReturn.value;
}

// 計算是否所有商品都已選 (計算狀態)
const isAllSelected = computed(() => {
    return props.order.order_items.every(item => {
        return selected_orderItems[item.id]?.quantity === item.available_qty;
    });
});

// 監聽isAllSelected 狀態 (做出反應)
watch(isAllSelected, (val) => {
    selectReturnAll.value = val;
});

// 點擊 checkbox 時，選或取消所有
function handleSelectReturnAll(checked) {
    props.order.order_items.forEach(item => {
        selected_orderItems[item.id].quantity = checked ? item.available_qty : 0;
    });
}

const rules = {
    reason: [
        { required: true, message: '請選擇退貨原因', trigger: 'change' }
    ]
};


const submitToReturn = async () => {
    try {
        await formValidate();
        await submit_return();
    } catch (error) {

    }
}

const formRefs = reactive({});

const formValidate = async () => {
    for (const item of props.order.order_items) {
        const id = item.id;
        const quantity = selected_orderItems[id]?.quantity ?? 0;
        const form = formRefs[id];

        //跳過數量為 0 的項目
        if (quantity === 0) {
            // selected_orderItems[id].reason = '';
            selected_orderItems[id].description = '';
            form.resetFields('reason');
        };

        const valid = await form.validate();
        if (!valid) {
            throw new Error(`商品 ${id} 驗證失敗`);
        }
    }
}

const submit_return = async () => {
    // console.log(selected_orderItems); //多維物件

    const submit_data = {
        order_id: props.order.id,
        items: Object.entries(selected_orderItems).filter(([id, e]) => e.quantity > 0 && e.reason != null)
            .map(([id, e]) => ({
                order_item_id: parseInt(id),
                quantity: e.quantity,
                reason: e.reason,
                description: e.description || '',
            }))
    }

    console.log(submit_data);

    try {
        const response = await axios.post('/return/returnRequest', submit_data);
        showMessage('success', response.data.msg);
    } catch (error) {
        console.error(error.response?.data);
        if (error.response && error.response.data) {
            const res = error.response.data;
            console.log(error);

            showMessage('error', res.error || '發生未知錯誤');
        } else {
            // 其他錯誤，如連線問題
            showMessage('error', '無法連接伺服器，請稍後再試');
        }
    }


}


function handleQuantityChange(id, val) {
    if (val === 0) {
        // selected_orderItems[id].description = '';
        formRefs[id].resetFields('reason');
    }
}

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
    border: 1px dashed #409eff;
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
    border-top: 1.875rem solid var(--brand-primary-color, #409eff);
    border-left: 1.875rem solid transparent;
    bottom: 0;
    content: "";
    position: absolute;
    /* right: -.9375rem; */
}

::v-deep(.el-form-item.is-required) {
    margin: auto 0;
    width: 300px;
}
</style>