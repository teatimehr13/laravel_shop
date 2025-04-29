<template>
    <Header />
    <!-- 回訂單列表 -->
    <main style="margin: 32px auto;">
        <div class="order-layout">
            <p style="margin-bottom: 32px;">
                <el-button link @click="goBack">
                    <el-icon>
                        <ArrowLeft />
                    </el-icon>
                    回上一頁
                </el-button>
                <hr style="margin-top: 8px;">
            </p>

            <el-descriptions class="margin-top" title="訂單列表" :column="3" size="default" border>
                <template #extra>
                    <!-- <el-button type="primary">Operation</el-button> -->
                </template>
                <el-descriptions-item>
                    <template #label>
                        <div class="cell-item">
                            訂單編號
                        </div>
                    </template>
                    {{ order_items.order_number }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <div class="cell-item">
                            訂單狀態
                        </div>
                    </template>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        {{ order_items.order_status_label }}
                        <el-button link type='r' ref="buttonRef">
                            <el-icon size="20px">
                                <Edit />
                            </el-icon>
                        </el-button>

                        <el-popover ref="popoverRef" :virtual-ref="buttonRef" trigger="click" title="更改訂單狀態"
                            v-model:visible="popoverVisible" virtual-triggering width>
                            <div style="margin-top: 20px">
                                <el-radio-group v-model="order_items.order_status" class="radio-wrap">
                                    <el-radio-button v-for="(val, key) in order_status_select" :key="Number(key)"
                                        :value="Number(key)" @dblclick="handleDoubleClick(Number(key))">
                                        {{ val }}
                                    </el-radio-button>
                                </el-radio-group>
                            </div>
                            <div style="margin-top: 4px;">
                                <el-text class="m-2" size="small" type="info">點兩下按鈕進行更新</el-text>
                            </div>
                        </el-popover>
                    </div>
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <div class="cell-item">
                            建立時間
                        </div>
                    </template>
                    {{ formateDate(order_items.created_at) }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <div class="cell-item">
                            收件人
                        </div>
                    </template>
                    {{ order_items.recipient_name }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <div class="cell-item">
                            電話
                        </div>
                    </template>
                    {{ order_items.recipient_phone }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <div class="cell-item">
                            付款方式
                        </div>
                    </template>
                    {{ order_items.payment_method_label }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <div class="cell-item">
                            運送地址
                        </div>
                    </template>
                    {{ order_items.address }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <div class="cell-item">
                            備註
                        </div>
                    </template>
                    {{ order_items.note }}
                </el-descriptions-item>
            </el-descriptions>

            <!-- <div class="order-list-layout">
                 <div style="width: 100%;">
                     <div class="flex">
                         <div>訂單編號</div>
                         <div>
                             {{ order_items.order_number }}
                         </div>
                     </div>
                     <div class="flex">
                         <div>建立時間</div>
                         <div>
                             {{ order_items.created_at }}
                         </div>
                     </div>
                     <div class="flex">
                         <div>訂單狀態</div>
                         <div>
                             {{ order_items.order_status }}
                         </div>
                     </div>
                 </div>
                 <div style="width: 100%;">
                     <div class="flex">
                         <div>收件人</div>
                         <div>
                             {{ order_items.recipient_name }}
                         </div>
                     </div>
                     <div class="flex">
                         <div>電話</div>
                         <div>
                             {{ order_items.recipient_phone }}
                         </div>
                     </div>
                     <div class="flex">
                         <div>付款方式</div>
                         <div>
                             {{ order_items.payment_method }}
                         </div>
                     </div>
                     <div class="flex">
                         <div>運送地址</div>
                         <div>
                             {{ order_items.address }}
                         </div>
                     </div>
                     <div class="flex">
                         <div>備註</div>
                         <div>
                             {{ order_items.note }}
                         </div>
                     </div>
                 </div>
     
             </div> -->


            <div class="order-detail-text">訂單詳情</div>
            <div class="order-detail-layout">
                <div class="grid-header">商品</div>
                <div class="grid-header">數量</div>
                <div class="grid-header" style="text-align: right;">單價</div>
                <div class="grid-header" style="text-align: right;">金額</div>

                <!-- 商品資料 -->
                <template v-for="(item, index) in order_items.order_items" :key="index">
                    <div class="grid-cell item-flex">
                        <div>
                            <img :src="item.image" width="50px">
                        </div>
                        <div>
                            {{ item.name }}
                        </div>
                    </div>
                    <div class="grid-cell">{{ item.quantity }}</div>
                    <div class="grid-cell" style="text-align: right;">{{ toCurrency(item.price + 1000) }}</div>
                    <div class="grid-cell" style="text-align: right;">{{ item.quantity > 0 && item.price > 0
                        ? toCurrency(Number(item.quantity) * Number(item.price))
                        : '-' }}</div>
                </template>
            </div>

            <div class="order-detail-layout">
                <div class="grid-co-all grid-cell">
                    <div>小計</div>
                    <div>
                        {{ toCurrency(order_items.amount) }}
                    </div>
                </div>

                <div class="grid-co-all grid-cell">
                    <div>運費</div>
                    <div>
                        {{ toCurrency(order_items.shippingFee) }}
                    </div>
                </div>

                <div class="grid-co-all grid-cell grid-total">
                    <div>總計</div>
                    <div>
                        {{ toCurrency(Number(order_items.amount) + Number(order_items.shippingFee)) }}
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
import { reactive, ref, unref } from 'vue';
import Header from '@/Components/Back/Header.vue';
import { toCurrency, formateDate } from '@/utils/basic_format.js';
import { router } from '@inertiajs/vue3';
const props = defineProps({
    order_items: Object,
    order_status_select: Array
})

const order_status_select = {
    0: '待付款',
    1: '已付款',
    2: '備貨中',
    3: '配送中',
    4: '已送達',
    5: '已完成',
    6: '已取消',
    7: '已退款',
    8: '部分退款',
};

console.log(props.order_items);
console.log(props.order_status_select);




const order_items = reactive(props.order_items);
console.log(order_items.order_status);
const popoverVisible = ref(false)


function goBack() {
    window.history.back();
}

const buttonRef = ref();
const popoverRef = ref();
const radio2 = ref('New York')


const handleDoubleClick = (selectedValue) => {
    console.log(selectedValue);
    console.log(order_items.id);

    router.put(`/back/backorder/${order_items.id}/status`, {
        order_status: selectedValue,
    }, {
        preserveScroll: true, // 保持捲動位置
        preserveState: true,  // 不重新 reload 畫面，保留當前頁面資料
        replace: true,        // 不新增一條新的歷史紀錄
        onSuccess: () => {
            console.log('訂單狀態更新成功')
            // router.visit(`/back/backorder/${order_items.id}`, {
            // preserveScroll: true,});
            order_items.order_status = selectedValue;
            order_items.order_status_label = order_status_select[selectedValue];
            popoverVisible.value = false;
        },
        onError: (errors) => {
            console.log('訂單狀態更新失敗', errors)
        }
    })
}

</script>

<style scoped>
.order-layout {
    max-width: 1000px;
    margin: auto;
}

.order-list-layout {
    display: flex;
    /* border-bottom: 1px solid #e8e7e7; */
    position: relative;
    max-width: 80%;
    gap: 50px;
}

.order-detail-text {
    font-weight: bold;
    margin-top: 32px;
    margin-bottom: 16px;
}

.flex {
    display: flex;
    align-items: center;
    padding: 5px;
    flex-wrap: nowrap;
    width: 100%;
    gap: 10px;
}

.flex:not(:last-of-type) {
    border-bottom: .5px dashed #e8e7e7;
}

.flex div:first-of-type {
    flex: 1.25;
}

.flex div:last-of-type {
    flex: 5;
}


.order-detail-layout {
    display: grid;
    grid-template-columns: 6fr 2fr 2fr 2fr;
    /* border: 1px solid #ebeef5; */
    /* border-left: 1px solid #ebeef5;
    border-right: 1px solid #ebeef5; */
    border-top: none;
    font-size: 14px;
    /* margin: 15px 0; */
}

.grid-header {
    background-color: #f5f7fa;
    color: #606266;
    padding: 8px;
    text-align: left;
    border: 1px solid #ebeef5;
}

.grid-header+.grid-header {
    border-left: none;
}

.grid-cell {
    padding: 8px;
    text-align: left;
    border-bottom: 1px dashed #ebeef5;
    display: grid;
    align-items: center;
}

.grid-cell:nth-of-type(4n-3) {
    border-left: 1px solid #ebeef5;
}

.grid-cell:nth-of-type(4n) {
    border-right: 1px solid #ebeef5;
}

/* 第一個沒有border-left */
/* .grid-cell + .grid-cell, .grid-header + .grid-header {
    border-left: 1px solid #ebeef5;
} */

/* .grid-cell:nth-last-child(-n+4) {
    border-bottom: none;
} */

.item-flex {
    display: flex;
    align-items: center;
    gap: 10px;
}

.grid-co-3-4 {
    grid-column: 3 / 4;
    text-align: end;
}

.grid-co-4-5 {
    grid-column: 4 / 5;
    text-align: end;
}

.grid-co-12 {
    grid-column: 12;
}

.grid-co-all {
    grid-column: 1 / 5;
    display: flex;
    justify-content: flex-end;
    gap: 68px;
}

.grid-co-all>div {
    min-width: 100px;
    text-align: end;
}

.grid-cell.grid-co-all {
    border-bottom: none;
    border-left: 1px solid #ebeef5;
    border-right: 1px solid #ebeef5;
}

.grid-total {
    border-top: 1px solid #ebeef5;
    border-bottom: 1px solid #ebeef5 !important;
    font-weight: bold;
    /* border-left: none !important;
    border-right: none !important; */
}

.grid-total>div:nth-of-type(2) {
    font-size: 20px;
}

.radio-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    width: 400px;
}

::v-deep(.el-radio-button__inner) {
    border-left: 1px solid #dcdfe6 !important;
    border-radius: 3px !important;
    box-shadow: none !important;
}
</style>