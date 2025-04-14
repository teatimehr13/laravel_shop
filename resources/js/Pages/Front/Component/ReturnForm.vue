<template>
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
const props = defineProps({
    order: Object
})
</script>

<style>
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