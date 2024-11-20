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
                        <!-- <td><img :src="option.image" alt="Product Image" width="50"></td> -->
                         <td>{{ option.image }}</td>
                        <td>{{ option.price }}</td>
                        <td>{{ option.enable ? '是' : '否' }}</td>
                        <td>
                            <button @click="editOption(option)">編輯</button>
                        </td>
                        <td>
                            <button @click="deleteOption(option.id)">刪除</button>
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


const props = defineProps({
    // product: {
    //     type: Object,
    //     required: true
    // },
    productOptions: {
        type: Array,
        required: true
    }
});

console.log(props);

// 編輯選項的方法
const editOption = (option) => {
  console.log('Editing option:', option);
};

// 刪除選項的方法
const deleteOption = (id) => {
  console.log('Deleting option with id:', id);
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