<template>
    <FrontendLayout>
        <template #switch>
            <Breadcrumb :category="category" />
            <section>
                <div class="category-container max-w-screen-xl mx-auto px-4">
                    <div v-for="(category, idx) in categories" :key="category.id" class="mb-8">
                        <!-- 分類名稱 -->
                        <div class="border-b border-gray-200 mb-6 pb-4">
                            <h1 class="text-xl font-semibold">
                                {{ category.name }}
                            </h1>
                        </div>

                        <!-- 子分類卡片區塊 -->
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            <div v-for="subcategory in category.subcategories" :key="subcategory.id"
                                class="flex flex-col items-center mb-2">
                                <!-- 圖片區塊 -->
                                <div class="border border-gray-200 p-2 mb-2 aspect-square w-full overflow-hidden">
                                    <a :href="`/product/list/${subcategory.search_key}`">
                                        <el-image :src="subcategory.image" fit="contain" class="w-full h-full transition duration-300 ease-in-out hover:scale-110" />
                                    </a>
                                </div>

                                <!-- 文字連結 -->
                                <a :href="`/product/list/${subcategory.search_key}`">
                                    <span class="text-sm text-gray-600 hover:text-black">
                                        {{ subcategory.name }}
                                    </span>
                                </a>
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
import Breadcrumb from './Component/Breadcrumb.vue';

const props = defineProps({
    products: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    }
})

const categories = props.categories;
// console.log(props.categories);
console.log(categories);

const category = {
    name: '所有產品類別'
}

</script>

<style scoped>
.demo-image {
    margin: auto;
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

/* .category-container {
    max-width: 1200px;
    margin: auto;
    padding-left: 2rem;
    padding-right: 2rem;
} */
</style>