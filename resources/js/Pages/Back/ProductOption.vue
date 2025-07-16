<template>
    <BackendLayout />
    <div>
        <h1>Product Options</h1>
        <table border="1">
            <thead>
                <tr>
                    <th>顏色名稱</th>
                    <th>顏色</th>
                    <th>主圖片</th>
                    <th>價格</th>
                    <th>顯示選項</th>
                    <th>編輯</th>
                    <th>刪除</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="option in productOptions" :key="option.id">
                    <td>{{ option.color_name }}</td>
                    <td>{{ option.color_code }}</td>
                    <td>{{ option.image }}</td>
                    <td>{{ option.price }}</td>
                    <td>{{ option.enable ? '是' : '否' }}</td>
                    <td>
                        <button @click="editOption(option)">編輯</button>
                    </td>
                    <td>
                        <button @click="submit(option.id)">刪除</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { onMounted, ref, defineProps } from 'vue';
import api from '@/api/api';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    productOptions: {
        type: Array,
        required: true
    }
});

console.log(props);

const submit = (productOptionId) => {
    const url = '/back/products/product_images';  // 簡化的 API 路徑，專注於功能性而非語義化的 RESTful URL

    const formData = {
        po_id: productOptionId, // 只傳 product_option 的 id
    };

    // 使用 axios 發送 POST 請求
    api.post(url, formData)
        .then(response => {
            // 假設後端返回了需要渲染到模態框的數據
            const data = response.data;
            console.log(data);

            // 這裡可以直接將返回的數據渲染到模態框中
            // renderModal(data);
        })
        .catch(error => {
            console.error('表單提交失敗', error);
        });
};

// 編輯選項的方法
const editOption = (option) => {
    console.log('Editing option:', option);
};

// 刪除選項的方法
const deleteOption = (option) => {
    form.po_id = id;
};



</script>

<style scoped>
table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

td img {
    display: block;
}
</style>