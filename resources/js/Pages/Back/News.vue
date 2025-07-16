<template>
    <BackendLayout>
        <template #switch>
            <div>
                <div class="filters">
                    <select>
                        <option value="">全部類型</option>
                        <option value="0">促銷優惠</option>
                        <option value="1">公告</option>
                    </select>
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

//初始資料
const props = defineProps({
    news: {
        required: true
    },
});

const news = reactive(props.news);
console.log(news);


// const storeType = ref("");
// const addressFilter = ref("");

// const getParams = () => {
//     return {
//         store_type: storeType.value || null,
//         address: addressFilter.value || null,
//     };
// }

// function applyFilters() {
//     axios.get('/back/stores', { params: getParams() })
//         .then(response => {
//             // 更新 stores 的內容
//             console.log(response.data);
//             updateStoresData(response)
//         })
//         .catch(error => {
//             console.error("Error fetching filtered stores:", error);
//         });
// }
// // console.log(props.stores);
// function fetchPage(url) {
//     axios.get(url, { params: getParams() })
//         .then(response => {
//             console.log(response.data);
//             // 更新 stores 的內容
//             updateStoresData(response)
//         })
//         .catch(error => {
//             console.error("Error fetching page:", error);
//         });
// }

// function goToPage(page) {
//     const url = `/back/stores?page=${page}`;
//     fetchPage(url);
// }


// function updateStoresData(response) {
//     const data = response.data;
//     stores.data = data.data;
//     stores.links = data.links;
//     stores.total = data.total;
//     stores.per_page = data.per_page;
//     stores.current_page = data.current_page;
//     stores.next_page_url = data.next_page_url;
//     stores.prev_page_url = data.prev_page_url;
//     stores.last_page = data.last_page;
// }

</script>