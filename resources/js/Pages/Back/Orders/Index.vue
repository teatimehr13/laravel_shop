<template>
    <BackendLayout>
        <el-pagination v-model:current-page="page" :page-size="10" :total="props.orders.total"
            @current-change="changePage" />

        <template #switch>
            <div>
                <el-table :data="order_data" border style="width: 100%; height: calc(100vh - 250px)" >
                    <el-table-column prop="order_number" label="訂單編號"  />
                    <el-table-column prop="created_at" label="建立時間" >
                        <template #default="scope">
                            <span> {{  formateDate(scope.row.created_at) }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="payment_method" label="付款方式">
                        <template #default="scope">
                            <span>
                                {{ scope.row.payment_method_label }}
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="order_status" label="訂單狀態">
                        <template #default="scope">
                            <span>
                                {{ scope.row.order_status_label }}
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="amount" label="訂單金額" align="right"  class-name="amount-column">
                        <template #default="scope">
                            {{ toCurrency(scope.row.amount) }}
                        </template>
                    </el-table-column>
                    <el-table-column label="操作" align="center" width="150px">
                        <template #default="scope">
                            <div>
                                <el-button link type="primary" >
                                    <el-icon><Search /></el-icon>
                                    <span>
                                        查看
                                    </span>
                                </el-button>
                             </div>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </template>
    </BackendLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { toCurrency, formateDate } from '@/utils/basic_format.js';


const props = defineProps({
    orders: Object,
    filters: Object
});

console.log(props.orders);
console.log(props.filters);

const order_data = ref(props.orders.data);
console.log(order_data.value);



const filters = ref({
    order_number: '2025',
    order_status: '',
    payment_method: 'credit',
    created_at: ''
});

const page = ref(props.orders.current_page);

const cleanFilters = Object.fromEntries(Object.entries(filters.value).filter(([_, v]) => v !== ''));

const changePage = (p) => {
    router.get(route('backorder.index'), {
        ...cleanFilters,
        page: p
    }, {
        preserveState: true,
        preserveScroll: true
    });
};




</script>

<style scoped>
::v-deep(.amount-column .cell) {
  padding-right: 15px;
}
</style>