<template>
    <BackendLayout>
        <template #switch>
            <div>
                <!-- 篩選器 -->
                <div class="filters">
                    <el-select v-model="storeType" placeholder="全部類型" style="width: 150px" @change="applyFilters">
                        <el-option v-for="item in storeTypeArr" :key="item.value" :label="item.name"
                            :value="item.value" />
                    </el-select>

                    <el-input v-model="addressFilter" placeholder="輸入地址篩選" style="width: 240px"
                        @keyup.enter="applyFilters" />
                </div>

                <!-- Table 與懶加載 -->
                <div>
                    <el-table :data="stores.data" style="width: 100%; height: calc(100vh - 200px)"
                        v-el-table-infinite-scroll="loadMore" v-loading="loading" :infinite-scroll-disabled="loading"
                        element-loading-text="加載中..." border>

                        <!-- <el-table-column prop="store_name" label="門市名稱" width="200"/> -->

                        <el-table-column width="220" :fixed="isFixedStore ? 'left' : false" prop="store_name">
                            <template #header>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span>門市名稱</span>
                                    <el-tooltip content="固定/取消固定門市列">
                                        <el-checkbox v-model="isFixedStore"></el-checkbox>
                                    </el-tooltip>
                                </div>
                            </template>
                        </el-table-column>


                        <el-table-column label="圖片" width="120">
                            <template #default="scope">
                                <img :src="scope.row.image" alt="店面圖片" style="max-width: 75px; border-radius: 4px" />
                            </template>
                        </el-table-column>
                        <el-table-column prop="store_type_name" label="類型" width="120" />
                        <el-table-column prop="address" label="地址" width="300" />
                        <el-table-column prop="contact_number" label="聯絡電話" width="150" />
                        <el-table-column prop="opening_hours" label="營業時間" width="200">
                            <template #default="scope">
                                <div v-html="formattedOpeningHours[scope.$index]"></div>
                            </template>
                        </el-table-column>

                        <el-table-column width="140" :fixed="isFixed ? 'right' : false">
                            <template #header>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span>操作</span>
                                    <el-tooltip content="固定/取消固定操作列">
                                        <el-checkbox v-model="isFixed"></el-checkbox>
                                    </el-tooltip>
                                </div>
                            </template>

                            <el-button size="small">編輯</el-button>
                            <el-button size="small" type="danger">移除</el-button>
                        </el-table-column>
                    </el-table>
                </div>

                <!-- 加載提示 -->
                <div v-if="noMoreData" style="text-align: center; margin: 10px">沒有更多數據了</div>
            </div>
        </template>
    </BackendLayout>
</template>


<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import axios from "axios";
import BackendLayout from '@/Layouts/BackendLayout.vue';
import debounce from "lodash.debounce";

// 初始數據
const stores = reactive({
    data: [],
    current_page: 1,
    last_page: null,
});
const loading = ref(false);
const noMoreData = ref(false);
const storeType = ref("");
const addressFilter = ref("");
const isFixed = ref(false); // 默認不固定
const isFixedStore = ref(false); // 默認不固定


// 篩選條件
const storeTypeArr = [
    { name: "全部類型", value: "" },
    { name: "直營", value: "0" },
    { name: "特約", value: "1" },
    { name: "授權", value: "2" },
];

onMounted(() => {
    // 初始化加載
    loadMore();
})


// 加載更多數據
const loadMore = debounce(async () => {
    // console.log(loading.value);

    if (loading.value || noMoreData.value) return;
    loading.value = true;

    try {
        const response = await axios.get("/back/stores", {
            params: {
                page: stores.current_page,
                store_type: storeType.value || null,
                address: addressFilter.value || null,
            },
        });

        // console.log(response.data);        

        const newData = response.data.data; // 新數據 
        const lastPage = response.data.last_page; // 總頁數 ex:11

        // 如果有數據，追加到 stores.data
        if (newData.length > 0) {
            stores.data.push(...newData);
            stores.current_page += 1; // 頁碼 +1
            stores.last_page = lastPage;

            // 如果當前頁碼超過最後一頁，標記沒有更多數據
            if (stores.current_page > stores.last_page) {
                noMoreData.value = true;
            }
        } else {
            noMoreData.value = true;
        }

    } catch (error) {
        console.error("Error loading more data:", error);
    } finally {
        loading.value = false;
    }
}, 300);

// 篩選條件發生變化時重新加載
const applyFilters = debounce(() => {
    stores.data = [];
    stores.current_page = 1;
    noMoreData.value = false;
    loadMore();
}, 500);

//格式化營業時間顯示
const formattedOpeningHours = computed(() => {
    return stores.data.map((store) =>
        store.opening_hours
            .split("\n") // 按 \n 分割
            .map((line) => line.trim()) // 去除多餘空白
            .filter((line) => line !== "") // 過濾空行
            .join("<br>") // 拼接成 HTML 的換行
    );
});
</script>

<style scoped>
.el-checkbox__label {
    padding-left: 3px;
}

.filters {
    margin: 10px 0 15px;
}

.el-select {
    margin-right: 10px;
}
</style>