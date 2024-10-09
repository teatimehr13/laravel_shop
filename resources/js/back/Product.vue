<template>
    <div>
        <h2>Product</h2>
        <input type="text" v-model="test">
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from '@/api/api';


let test = ref('');
let products = ref();

onMounted(
    async () => {
        try {
            const response = await api.get('/products'); // 使用 api 發送請求
            products.value = response.data; // 獲取產品數據
            console.log(products.value);

        } catch (error) {
            console.error('Failed to fetch products:', error);
        }

    })
</script>