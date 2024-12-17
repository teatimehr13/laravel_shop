<template>
    <BackendLayout>
        <template #switch>
            <div>
                <div class="filters">
                    <el-select v-model="storeType" placeholder="全部類型" style="width: 150px" @change="applyFilters">
                        <el-option v-for="item in storeTypeArr" :key="item.value" :label="item.name"
                            :value="item.value" />
                            <!-- <el-option label="全部類型" value=""></el-option>
                            <el-option label="直營" value="0"></el-option>
                            <el-option label="特約" value="1"></el-option>
                            <el-option label="授權" value="2"></el-option> -->
                    </el-select>
                    
                    <el-input v-model="addressFilter" type="text" style="width: 240px" placeholder="輸入地址篩選" @keyup.enter="applyFilters"/>
                </div>

                <!-- <table>
                    <thead>
                        <tr>
                            <th>門市名稱</th>
                            <th>圖片</th>
                            <th>類型</th>
                            <th>地址</th>
                            <th>聯絡電話</th>
                            <th>營業時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="store in stores.data" :key="store.id">
                            <td>{{ store.store_name }}</td>
                            <td>
                                <img :src="store.image" alt="" style="max-width: 75px;">
                            </td>
                            <td>{{ store.store_type_name }}</td>
                            <td>{{ store.address }}</td>
                            <td>{{ store.contact_number }}</td>
                            <td>{{ store.opening_hours }}</td>
                        </tr>
                    </tbody>
                </table> -->

                <div>
                    <el-table :data="stores.data" style="width: 100%" :max-height="600" border>
                        <!-- 門市名稱 -->
                        <el-table-column prop="store_name" label="門市名稱" width="200" fixed>
                        </el-table-column>

                        <!-- 圖片 -->
                        <el-table-column label="圖片" width="120">
                            <template #default="scope">
                                <img :src="scope.row.image" alt="店面圖片" style="max-width: 75px; border-radius: 4px;" />
                            </template>
                        </el-table-column>


                        <el-table-column prop="store_type_name" label="類型" width="120">
                        </el-table-column>

                        <el-table-column prop="address" label="地址" width="300">
                            <template #default="scope">
                                <div class="ellipsis" :title="scope.row.address">{{ scope.row.address }}</div>
                            </template>
                        </el-table-column>

                        <el-table-column prop="contact_number" label="聯絡電話" width="150">
                        </el-table-column>

                        <el-table-column prop="opening_hours" label="營業時間" width="250">
                            <template #default="scope">
                                <!-- 使用 computed 返回的 formattedOpeningHours 渲染對應行 -->
                                <div v-html="formattedOpeningHours[scope.$index]"></div>
                            </template>
                        </el-table-column>

                        <!-- 操作 -->
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


                <!-- <div class="table">
                <div class="table-header">
                    <div class="table-row">
                        <div class="table-cell">門市名稱</div>
                        <div class="table-cell">圖片</div>
                        <div class="table-cell">地址</div>
                        <div class="table-cell">聯絡電話</div>
                        <div class="table-cell">營業時間</div>
                    </div>
                </div>
                <div class="table-body">
                    <div class="table-row" v-for="store in stores.data" :key="store.id">
                        <div class="table-cell">{{ store.store_name }}</div>
                        <div class="table-cell">{{ store.image }}</div>
                        <div class="table-cell">{{ store.address }}</div>
                        <div class="table-cell">{{ store.contact_number }}</div>
                        <div class="table-cell">{{ store.opening_hours }}</div>
                    </div>
                </div>
            </div> -->


                <div class="pagination">
                    <!-- <Link :href="stores.prev_page_url">上一頁</Link>
                <Link :href="stores.next_page_url">下一頁</Link> -->
                    <button :disabled="stores.current_page === 1" @click="fetchPage(stores.prev_page_url)">
                        上一頁
                    </button>

                    <button v-for="page in pageNumbers" :key="page" :class="{ active: page === stores.current_page }"
                        @click="goToPage(page)" :disabled="page === stores.current_page || page === '...'">
                        {{ page }}
                    </button>

                    <button v-if="stores.next_page_url" @click="fetchPage(stores.next_page_url)">
                        下一頁
                    </button>
                </div>
            </div>
        </template>
    </BackendLayout>
</template>


<script setup>
import { onMounted, ref, defineProps, reactive, computed } from 'vue';
import api from '@/api/api';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import axios from 'axios';
import debounce from "lodash.debounce";

//初始資料
const props = defineProps({
    stores: {
        required: true
    },
});

const stores = reactive(props.stores);
console.log(stores);

let storeTypeArr = ref([
    { name: "全部類型", value: "" },
    { name: "直營", value: "0" },
    { name: "特約", value: "1" },
    { name: "授權", value: "2" },
]);


const storeType = ref("");
const addressFilter = ref("");

const getParams = () => {
    return {
        store_type: storeType.value || null,
        address: addressFilter.value || null,
    };
}

const applyFilters = debounce(() => {
  console.log("storeType:", storeType.value);
  axios
    .get("/back/stores", { params: getParams() })
    .then((response) => {
      console.log("Filtered data:", response.data);
      updateStoresData(response);
    })
    .catch((error) => {
      console.error("Error fetching filtered stores:", error);
    });
}, 500); 


// console.log(props.stores);
function fetchPage(url) {
    axios.get(url, { params: getParams() })
        .then(response => {
            console.log(response.data);
            // 更新 stores 的內容
            updateStoresData(response)
        })
        .catch(error => {
            console.error("Error fetching page:", error);
        });
}

function goToPage(page) {
    const url = `/back/stores?page=${page}`;
    fetchPage(url);
}

// 計算分頁數字
const pageNumbers = computed(() => {
    const totalPages = stores.last_page; // 總頁數
    const currentPage = stores.current_page; // 當前頁碼
    const maxVisiblePages = 5; // 最大可見頁數

    // 如果總頁數小於等於最大顯示頁數，直接返回所有頁碼
    if (totalPages <= maxVisiblePages) {
        return Array.from({ length: totalPages }, (_, i) => i + 1);
    }

    const pages = [];

    // 當前頁碼大於3時，顯示第一頁和省略號
    if (currentPage > 3) {
        pages.push(1); // 第一頁
        if (currentPage > 4) pages.push("..."); // 省略號
    }

    // 動態計算 start 和 end，確保顯示範圍合理
    let start = Math.max(1, currentPage - 1); //傳回最小值
    let end = Math.min(totalPages, currentPage + 1);  //傳回最大值

    // 當前頁為第一頁時，顯示前 5 頁
    if (currentPage === 1) {
        end = Math.min(5, totalPages - 1);
    }

    // 當前頁為最後一頁時，顯示最後 5 頁
    if (currentPage === totalPages) {
        start = Math.max(1, totalPages - 4);
        end = totalPages;
    }
    // 添加中間的頁碼
    for (let i = start; i <= end; i++) {
        // console.log(i);
        pages.push(i);
    }

    // 當前頁碼小於總頁數減 2 時，顯示省略號和最後一頁
    if (currentPage < totalPages - 1) {
        if (currentPage < totalPages - 3 || (currentPage === 1 && totalPages > maxVisiblePages)) {
            pages.push("..."); // 省略號
        }
        pages.push(totalPages); // 最後一頁
    }

    return pages;
});

function updateStoresData(response) {
    // const data = response.data;
    // stores.data = data.data;
    // stores.links = data.links;
    // stores.total = data.total;
    // stores.per_page = data.per_page;
    // stores.current_page = data.current_page;
    // stores.next_page_url = data.next_page_url;
    // stores.prev_page_url = data.prev_page_url;
    // stores.last_page = data.last_page;
    Object.assign(stores, response.data);
}

const formattedOpeningHours = computed(() => {
    return stores.data.map((store) =>
        store.opening_hours
            .split("\n") // 按 \n 分割
            .map((line) => line.trim()) // 去除多餘空白
            .filter((line) => line !== "") // 過濾空行
            .join("<br>") // 拼接成 HTML 的換行
    );
});

const isFixed = ref(false); // 默認不固定

</script>


<style scoped> 
.pagination {
    display: flex;
    gap: 8px;
    align-items: center;
}

.pagination button {
    padding: 8px 12px;
    border: 1px solid #ccc;
    background-color: white;
    cursor: pointer;
}

.pagination button.active {
    font-weight: bold;
    background-color: #007bff;
    color: white;
    cursor: default;
}

.pagination button:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}


th,
td {
    word-wrap: break-word;
    white-space: normal;
}

td img {
    max-width: 75px;
    border-radius: 4px;
    display: block;
}


/* 行間隔樣式 */
tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}

/* 滾動條美化 */
.table-container::-webkit-scrollbar {
    height: 8px;
}

.table-container::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 4px;
}

.table-container {
    width: 100%;
}

.ellipsis {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.el-table__header,
.el-table__body {
    width: 100% !important;
    max-width: none;
}

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