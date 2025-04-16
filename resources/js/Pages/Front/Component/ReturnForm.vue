<template ref="returnRef">
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
                    <el-input-number v-model="selected_orderItems[item.id].quantity" :min="0" :max="item.available_qty"
                        size="small" style="width: 100px;" @change="(val) => handleQuantityChange(item.id, val)" />
                    <el-text style="margin: 0 10px;" type="danger">(可退數量：{{ item.available_qty }})</el-text>
                </div>
            </div>
            <div class="flex" v-show="selected_orderItems[item.id].quantity > 0">
                <div>退貨原因</div>
                <div>
                    <el-form :model="selected_orderItems[item.id]" :rules="rules" :ref="el => (formRefs[item.id] = el)">
                        <el-form-item prop="reason">
                            <el-select v-model="selected_orderItems[item.id].reason" placeholder="選擇退貨原因"
                                style="width: 300px; display: block; ">
                                <el-option v-for="(reason, idx) in return_reasons" :key="idx" :label="reason.label"
                                    :value="reason.value" />
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
                    <el-input maxlength="30" style="width: 300px" show-word-limit type="textarea" placeholder="選填"
                        v-model="selected_orderItems[item.id].description" />
                </div>
            </div>
        </div>
    </div>
    <el-button type="danger" @click="submitToReturn" :disabled="!canSubmit"
        style="margin: 10px; float: right;">提交</el-button>
</template>

<script setup>
import { ref, reactive, watch, onMounted, computed } from 'vue';

const props = defineProps({
    order: Object,
    toCurrency: Function,
    return_reasons: Array,
    // showMessage: Function
})

console.log(props.order.order_items);
// console.log(props.showMessage);

const emit = defineEmits([
    'get-order-num'
])

const returnRef = ref('');

const selected_orderItems = reactive({});
props.order.order_items.forEach((item) => {
    selected_orderItems[item.id] = {
        quantity: 0,
        reason: null,
        description: ''
    }
})

console.log(selected_orderItems);



const canSubmit = computed(() => {
    return Object.values(selected_orderItems).some(item => {
        return item.quantity > 0 && item.reason;
    });

});

console.log(canSubmit.value);

const selectReturnAll = ref(false);

// 計算是否所有商品都已選 (計算狀態)
const isAllSelected = computed(() => {
    return props.order.order_items.every(item => {
        return  selected_orderItems[item.id]?.quantity === item.available_qty;
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

const resetForm = () => {
    for (const item of props.order.order_items) {
        const id = item.id;
        const form = formRefs[id];
        selected_orderItems[id].description = '';
        selected_orderItems[id].quantity = 0;
        form.resetFields('reason');
    }

    selectReturnAll.value = false;
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
        console.log(response.data);

        showMessage('success', response.data.msg);
    } catch (error) {
        // console.error(error.response?.data);
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

const formRefs = reactive({});

function handleQuantityChange(id, val) {
    if (val === 0) {
        formRefs[id].resetFields('reason');
    }
}

const submitToReturn = async () => {
    // emit('test01', 87)
    try {
        await formValidate();
        await submit_return();
        await emit('get-order-num', props.order.order_number)
    } catch (error) {

    }
}


defineExpose({
    returnRef,
    resetForm
})

//通知
const showMessage = (type, title) => {
    ElNotification({
        type, // "success" 或 "error"
        title,
        position: 'bottom-left',
    });
};

</script>

<style scoped>
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

.flex {
    display: flex;
    align-items: center;
    padding: 5px;
    flex-wrap: nowrap;
    /* gap: 20px; */
}

.flex:not(:last-of-type) {
    border-bottom: .5px dashed #e8e7e7;
}

.flex div:first-of-type {
    flex: 1.5;

}

.flex div:last-of-type {
    flex: 6;
}

::v-deep(.el-form-item.is-required) {
    margin: auto 0;
    width: 300px;
}
</style>