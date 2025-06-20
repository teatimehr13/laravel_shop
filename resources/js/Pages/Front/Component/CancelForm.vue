<template ref="returnRef">
    <div v-for="(item, idx) in order.order_items" class="return-con-layout">
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
        </div>
    </div>
    <el-button type="danger" v-if="order.order_status != 6" @click="cancel_form" style="margin: 10px; float: right;">取消訂單</el-button>
</template>

<script setup>
import axios from 'axios';
import { ref, reactive, watch, onMounted, computed } from 'vue';

const props = defineProps({
    order: Object,
    toCurrency: Function,
    return_reasons: Array,
    // showMessage: Function
})

console.log(props.order);
const order = reactive({ ...props.order })
// console.log(props.showMessage);

const emit  = defineEmits(['orderCanceled'])

const cancel_form = () => {
    axios.patch(route('order.cancel', { order: props.order.order_number })).then(response => {
        console.log(response.data.msg)
        order.order_status = 6 ;
        emit('orderCanceled', 6) 
        showMessage(response.data.type, response.data.msg)
    })
}


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