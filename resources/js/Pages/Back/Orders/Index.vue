<template>
    <BackendLayout>

        <template #switch>
            <!-- <el-pagination v-model:current-page="page" :page-size="10" :total="props.orders.total"
                @current-change="changePage" /> -->
            <div>
                <div class="filters">

                    <el-input v-model="filters.order_number" placeholder="輸入訂單編號" style="width: 240px"
                        @keyup.enter="changePage()" />

                    <el-select v-model="filters.order_status" placeholder="訂單狀態" style="width: 150px"
                        @change="changePage()">
                        <el-option label="全部" value="">全部</el-option>
                        <el-option v-for="(val, key) in props.order_status_select" :key="key" :label="val"
                            :value="key" />
                    </el-select>

                    <el-select v-model="filters.payment_method" placeholder="付款方式" style="width: 150px"
                        @change="changePage()">
                        <el-option label="全部" value="">全部</el-option>
                        <el-option v-for="(val, key) in props.payment_method_select" :key="key" :label="val"
                            :value="key" />
                    </el-select>
                </div>

                <div class="filters">
                    <el-select v-model="selectedSortKey" @change="handleSortChange" placeholder="排序依據"
                        style="width: 200px; margin-right: 10px">
                        <el-option label="訂單時間（新到舊）" :value="0" />
                        <el-option label="訂單時間（舊到新）" :value="1" />
                        <el-option label="金額（高到低）" :value="2" />
                        <el-option label="金額（低到高）" :value="3" />
                    </el-select>
                </div>

                <div>
                    <el-table :data="order_data" border style="width: 100%; height: calc(100vh - 250px)">
                        <el-table-column prop="order_number" label="訂單編號" />
                        <el-table-column prop="created_at" label="建立時間">
                            <template #default="scope">
                                <span> {{ formateDate(scope.row.created_at) }}</span>
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
                        <el-table-column prop="amount" label="訂單金額" align="right" class-name="amount-column">
                            <template #default="scope">
                                {{ toCurrency(scope.row.amount) }}
                            </template>
                        </el-table-column>
                        <el-table-column label="操作" align="center" width="150px">
                            <template #default="scope">
                                <div>
                                    <el-button link type="primary">
                                        <Link :href="route('backorder.show', scope.row.id)">
                                        <el-icon style="vertical-align: bottom;">
                                            <Search />
                                        </el-icon>
                                        訂單詳情
                                        </Link>
                                    </el-button>
                                </div>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
            </div>
        </template>
    </BackendLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { toCurrency, formateDate } from '@/utils/basic_format.js';


const props = defineProps({
    orders: Object,
    filters: Object,
    order_status_select: Array,
    payment_method_select: Object
});

console.log(props.orders);
console.log(props.filters);
console.log(props.order_status_select);
console.log(props.payment_method_select);



const order_data = ref(props.orders.data);
console.log(order_data.value);



// const filters = ref({
//     order_number: '',
//     order_status: '',
//     payment_method: '',
//     created_at: ''
// });

const filters = ref({ ...props.filters });
filters.value.order_status = filters.value.order_status != null ? Number(filters.value.order_status) : filters.value.order_status;
console.log(filters.value);


const page = ref(props.orders.current_page);



const changePage = (p = 1) => {

    console.log(filters.value);

    const cleanFilters = Object.fromEntries(Object.entries(filters.value).filter(([_, v]) => v !== ''));
    console.log(cleanFilters);
    // router.get(route('backorder.index'),
    //     ...cleanFilters,
    //     // page: p
    //  {
    //     preserveState: true,
    //     preserveScroll: true
    // });
    // router.get(route('backorder.index'), { order_number: filters.value.order_number })
    router.get(route('backorder.index'), cleanFilters);
};

//篩選

const sortOptionsMap = {
    0: { by: 'created_at', dir: 'desc' },
    1: { by: 'created_at', dir: 'asc' },
    2: { by: 'amount', dir: 'desc' },
    3: { by: 'amount', dir: 'asc' },
};

const entry = Object.entries(sortOptionsMap).find(
    ([_, val]) => val.by === filters.value.sort_by && val.dir === filters.value.sort_dir
);

const selectedSortKey = ref(entry ? Number(entry[0]) : 0); // 預設值為 0：時間新到舊
console.log(selectedSortKey.value);

// console.log(Number(entry[0]) || 0);


const handleSortChange = (key) => {
    const option = sortOptionsMap[key];
    if (option) {
        filters.value.sort_by = option.by;
        filters.value.sort_dir = option.dir;
        changePage();
    }

    console.log(filters.value);
};

</script>

<style scoped>
::v-deep(.amount-column .cell) {
    padding-right: 15px;
}

::v-deep(.el-input__inner:focus) {
    outline: none;
    box-shadow: none;
}

.filters {
    margin: 10px 0 15px;
    display: flex;
    gap: 10px;
}
</style>