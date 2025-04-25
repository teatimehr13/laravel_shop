<template>
    <Header />
    <!-- 回訂單列表 -->
    <div class="order-layout">
        <div class="order-list-layout">
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

        </div>

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
</template>

<script setup>
import { reactive } from 'vue';
import Header from '@/Components/Back/Header.vue';
import { toCurrency, formateDate } from '@/utils/basic_format.js';

const props = defineProps({
    order_items: Object
})


console.log(props.order_items);

const order_items = reactive(props.order_items);

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
    max-width: 100%;
    gap: 100px;
}

.flex {
    display: flex;
    align-items: center;
    padding: 5px;
    flex-wrap: nowrap;
    width: 100%;
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
    /* border: 1px solid #ddd; */
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

.grid-cell {
    padding: 8px;
    text-align: center;
    border-bottom: 1px solid #ebeef5;
    display: grid;
    align-items: center;
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
    justify-content: center;
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

.grid-co-all > div {
    min-width: 100px;
    text-align: end;
}

.grid-cell.grid-co-all {
    border-bottom: none;
}

.grid-total {
    border-top: 1px solid #ddd;
    font-weight: bold;
}

.grid-total > div:nth-of-type(2){
    font-size: 20px;
}
</style>