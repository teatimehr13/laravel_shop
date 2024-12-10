<template>
    <BackendLayout />

    <div>
        <div class="filters">
            <select v-model="storeType" @change="applyFilters">
                <option value="">全部類型</option>
                <option value="0">直營</option>
                <option value="1">特約</option>
                <option value="2">授權</option>
            </select>
            <input v-model="addressFilter" type="text" placeholder="輸入地址篩選" @keyup.enter="applyFilters" />
        </div>

        <table>
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
                        <img :src=" store.image" alt="" style="max-width: 75px;">
                    </td>
                    <td>{{ store.store_type_name }}</td>
                    <td>{{ store.address }}</td>
                    <td>{{ store.contact_number }}</td>
                    <td>{{ store.opening_hours }}</td>
                </tr>
            </tbody>
        </table>

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


<script setup>
import { onMounted, ref, defineProps, reactive, computed } from 'vue';
import api from '@/api/api';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import axios from 'axios';

//初始資料
const props = defineProps({
    stores: {
        required: true
    },
});

const stores = reactive(props.stores);
// const stores = ref({
//     current_page: 1,
//     per_page: 10,
//     total: 100, // 總筆數
//     next_page_url: "/back/stores?page=2",
//     prev_page_url: null,
//     data: [], // 當前頁的資料
// });
console.log(stores);


const storeType = ref("");
const addressFilter = ref("");

const getParams = () => {
    return {
        store_type: storeType.value || null,
        address: addressFilter.value || null,
    };
}

function applyFilters() {
    axios.get('/back/stores', { params: getParams() })
        .then(response => {
            // 更新 stores 的內容
            console.log(response.data);
            updateStoresData(response)
        })
        .catch(error => {
            console.error("Error fetching filtered stores:", error);
        });
}
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
    // if (typeof page !== 'number' || page === stores.current_page) {
    //     return; // 不執行任何操作
    // }
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
    const data = response.data;
    stores.data = data.data;
    stores.links = data.links;
    stores.total = data.total;
    stores.per_page = data.per_page;
    stores.current_page = data.current_page;
    stores.next_page_url = data.next_page_url;
    stores.prev_page_url = data.prev_page_url;
    stores.last_page = data.last_page;
}

</script>


<style>
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


/* 整個表格容器 */
.table {
    display: flex;
    flex-direction: column;
    border: 1px solid #ccc;
    width: 100%;
    /* 或者其他適合的寬度 */
    border-collapse: collapse;
}

/* 表頭 */
.table-header {
    background-color: #f8f8f8;
    font-weight: bold;
}

/* 每行 */
.table-row {
    display: flex;
    border-bottom: 1px solid #ccc;
}

/* 單元格 */
.table-cell {
    flex: 1;
    /* 平均分配空間 */
    padding: 8px;
    text-align: left;
    /* 根據需要調整對齊 */
    border-right: 1px solid #ccc;
}

/* 移除最後一列的右邊框 */
.table-row .table-cell:last-child {
    border-right: none;
}

.table-row:hover {
    background-color: #f0f0f0;
}

.table-header {
    position: sticky;
    top: 0;
    z-index: 1;
}

.table-body .table-row:nth-child(odd) {
    background-color: #f9f9f9;
}


/* 響應式：窄屏時變成垂直堆疊 */
@media (max-width: 600px) {
    .table-row {
        flex-direction: column;
        align-items: flex-start;
        border-bottom: 1px solid #ddd;
    }

    .table-cell {
        flex: none;
        width: 100%;
    }
}
</style>