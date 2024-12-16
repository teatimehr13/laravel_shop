<template>
    <BackendLayout>
        <template #switch>
            <div>
                <h1>Product</h1>
                <!-- <pre>{{ products }}</pre> -->
                <table border="1">
                    <thead>
                        <tr>
                            <th>產品名稱</th>
                            <th>描述</th>
                            <th>圖片</th>
                            <th>類別</th>
                            <th>狀態</th>
                            <th>顏色</th>
                            <th>編輯</th>
                            <th>刪除</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products" :key="products.id">
                            <td>{{ product.title }}</td>
                            <td>{{ product.name }}</td>
                            <td>{{ product.image }}</td>
                            <td>{{ product.subcategory.name }}</td>
                            <td>{{ product.published_status }}</td>
                            <td>
                                <ul style="display: flex; column-gap: 7px;">
                                    <li v-for="(color, index) in product.color_codes" :key="index">
                                        {{ color }}
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <!-- <Link :href="`/back/products/${product.id}/productOptions`">顏色管理</Link> -->
                                        <Link :href="route('back.products.productOptions.index', { id: product.id })">
                                        顏色管理</Link>
                                    </li>
                                </ul>
                            </td>
                            <!-- <td><img :src="option.image" alt="Product Image" width="50"></td>
                            <td>{{ option.price }}</td>
                            <td>{{ option.enable ? '是' : '否' }}</td> -->
                            <td>
                                <button @click="editProduct(product)">編輯</button>
                            </td>
                            <td>
                                <button @click="deleteProduct(product.id)">刪除</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </BackendLayout>>
</template>

<script setup>
import { onMounted, ref, defineProps } from 'vue';
import api from '@/api/api';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    products: {
        type: Array,
        required: true
    }
});

console.log(props);

// 編輯選項的方法
const editProduct = (product) => {
    console.log('Editing product:', product);
};

// 刪除選項的方法
const deleteProduct = (id) => {
    console.log('Deleting product with id:', id);
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