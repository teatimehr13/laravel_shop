<template>
    <FrontendLayout>
        <template #switch>
            <Breadcrumb :category="category" :subcategory="subcategory" />

            <section>
                <div class="layout-container">
                    <Sidebar :categoryLists="categoryLists" />

                    <div style="margin: auto; width: 100%;" class="product-container">

                        <div>
                            <h1>{{ subcategory_name }}</h1>
                        </div>


                        <div class="product-list-card-con">
                            <div class="product-list-card" v-for="(productList, idx) in productLists"
                                :key="productList.id">
                                <div class="demo-image">
                                    <a v-if="productList.image" :href="`/product/show/${productList.slug}`">
                                        <img :src="productList.image" fit="fill" />
                                    </a>

                                    <a v-else :href="`/product/show/${productList.slug}`">
                                        <div class="empty-image">
                                            <div>
                                                <span>
                                                    尚無圖片
                                                </span>
                                            </div>
                                        </div>
                                    </a>

                                </div>

                                <div class="product-text-content">
                                    <div>
                                        <h1>
                                            {{ productList.name }}
                                        </h1>
                                    </div>
                                    <h3>
                                        {{ productList.title }}
                                    </h3>
                                    <div class="price-text">
                                        售價: <span>{{ toCurrency(productList.price) }}</span>
                                    </div>

                                    <div>
                                        <a :href="`/product/show/${productList.slug}`" style="display: grid;">
                                            <el-button color="#626aef" plain size="large">查看商品</el-button>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>
    </FrontendLayout>
</template>


<script setup>
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import { ref, onMounted } from "vue";
import Breadcrumb from './Component/Breadcrumb.vue';
import Sidebar from './Component/Sidebar.vue';


const props = defineProps({
    productLists: {
        type: Array,
        required: true
    },
    subcategory_name: {
        type: String,
        required: true
    },
    category: {
        type: Object
    },
    subcategory: {
        type: Object
    },
    categoryLists: {
        type: Array
    }
})


const productLists = props.productLists;
const subcategory_name = props.subcategory_name;
// console.log(props.categories);
console.log(productLists);
console.log(subcategory_name);
console.log(props.category);
console.log(props.subcategory);
// console.log(props.categoryLists);


function toCurrency(num) {
    if (!num && num !== 0) return "$"; // 避免 null 或 undefined
    return `$${Number(num).toLocaleString("en-US")}`; // 確保是數字再轉換
}


</script>

<style scoped>
.demo-image {
    margin: auto;
    /* min-height: 60%; */
    flex: 6;
    overflow: hidden;
    position: relative;
    width: 100%;
}

.demo-image img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    /* 讓圖片完全顯示，不裁切 */
    transition: transform .5s ease;
}

.demo-image img:hover {
    transform: scale(1.1);
    cursor: pointer;
}

.demo-image .empty-image {
    left: 0;
    position: absolute;
    top: 0;
    height: 100%;
    width: 100%;
}

.empty-image>div {
    height: 100%;
    width: 100%;
    background-color: #f5f7fa;
}

.empty-image span {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #c0c0c0;
    font-size: 1.25rem;
}

.demo-image .block {
    padding: 30px 0;
    text-align: center;
    display: inline-block;
    width: 10%;
    box-sizing: border-box;
    vertical-align: top;
}

.demo-image .block:last-child {
    border-right: none;
}

.demo-image .demonstration {
    display: block;
    color: var(--el-text-color-secondary);
    font-size: 14px;
    margin-bottom: 20px;
}

.layout-container {
    display: grid;
    grid-template-columns:
        [left-space] 1fr [aside] 220px [gap] 20px [product-con] minmax(auto, 1330px) [right-space] 1fr;
    padding-left: 2rem;
    padding-right: 2rem;
}

.layout-container aside {
    grid-column: aside;
}

.product-container {
    grid-column: product-con;
}

.product-list-card-con {
    display: grid;
    grid-template-columns: repeat(4, minmax(20%, auto));
    gap: 15px;
}

.product-list-card {
    padding: 20px;
    border: 1px solid #e4e7ec;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-text-content {
    /* min-height: 40%; */
    flex: 4;
    display: flex;
    flex-direction: column;
    gap: 3px;
    /* flex: 4 1 0% */
}

.product-text-content h1 {
    font-weight: bold;
    font-size: 1.25rem;
    text-overflow: ellipsis;
    line-height: 1.6;
    overflow: hidden;
    white-space: nowrap;
}

.product-text-content h3 {
    color: #666666;
    margin-bottom: 7px;
}

.price-text{
    color: #666666;
    padding: 5px 0;
}
.price-text span {
    color: #282828;
    font-size: 1.25rem;
    font-weight: bold;
}

</style>