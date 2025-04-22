<template>
    <div v-if="historyData && historyData.length > 0">
        <div v-for="(item, idx) in historyData" class="return-con-layout" :key="item.id">
            <div class="item-details">
                <div class="flex">
                    <div>退貨單號</div>
                    <div>
                        {{ item.return_number }}
                    </div>
                </div>
                <div class="flex">
                    <div>申請時間</div>
                    <div>{{ formateDate(item.created_at) }}</div>
                </div>
                <div class="flex">
                    <div>退貨狀態</div>
                    <div>
                        {{ item.status }}
                    </div>
                </div>
                <div class="flex">
                    <div>退款總額</div>
                    <div>
                        {{ toCurrency(item.total_refund_amount) }}
                    </div>
                </div>

                <div class="flex">

                    <el-collapse v-model="activeNames" @change="handleChange">
                        <el-collapse-item title="退貨明細" :name="item.id">
                            <div class="flex" style="align-items: flex-start;"
                                v-for="(d_item, d_idx) in item.return_items" :key="d_idx">
                                <div>
                                    <img :src="d_item.order_item.image" style="max-width: 50px;">
                                </div>
                                <div>
                                    <div class="flex">
                                        <div>商品名稱</div>
                                        <div>{{ d_item.name }}</div>
                                    </div>
                                    <div class="flex">
                                        <div>退款金額</div>
                                        <div>
                                            {{ toCurrency(d_item.unit_price) }} x
                                            {{ d_item.quantity }} =
                                            {{ toCurrency(d_item.final_refund) }}
                                        </div>
                                    </div>
                                    <div class="flex">
                                        <div>原因</div>
                                        <div>{{ d_item.reason }}</div>
                                    </div>
                                    <div class="flex" v-if="d_item.description">
                                        <div>備註</div>
                                        <div>{{ d_item.description }}</div>
                                    </div>
                                </div>
                            </div>
                        </el-collapse-item>
                    </el-collapse>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <span>尚無退貨歷史紀錄</span>
    </div>
</template>

<script setup>
import { reactive, watch, ref } from 'vue';

const props = defineProps({
    historyData: Array,
    formateDate: Function,
    toCurrency: Function,
})

const activeNames = ref();

watch(
    () => props.historyData,
    (newVal) => {
        if (newVal) {
            const historyData = reactive(props.historyData);
            console.log(historyData);
            activeNames.value = historyData[0].id;
            console.log(activeNames.value);
        }
    },
    { immediate: true }
);

// const activeNames = ref([47]);

const handleChange = (val) => {
    console.log(val)
}

</script>

<style scoped>
.return-con-layout {
    display: flex;
    border-bottom: 1px solid #e8e7e7;
    position: relative;
    margin: 10px 0;
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
</style>