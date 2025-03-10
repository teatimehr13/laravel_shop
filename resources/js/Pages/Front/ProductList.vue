<template>
    <FrontendLayout>
        <template #switch>
            <Breadcrumb :category="category" :subcategory="subcategory" />

            <section>
                <div style="display: flex; max-width: 1680px; padding-left: 8.125rem; padding-right: 8.125rem;">
                    <Sidebar :categoryLists="categoryLists" />
    
                    <div style="margin: auto;">
    
                        <div>
                            <h1>{{ subcategory_name }}</h1>
                        </div>
    
    
                        <el-row :gutter="20">
                            <el-col v-for="(productList, idx) in productLists" :key="productList.id" :xs="12" :sm="6" :md="3" :lg="3"
                                style="border-bottom: 3px solid #e5e7eb; margin-bottom: 1.875rem; padding-bottom: 1rem;">
                                <h1>
                                    {{ productList.name }}
                                </h1>
                                <h3>
                                    {{ productList.title }}
                                </h3>
    
                                <div
                                    style="display: flex; flex-direction: column; align-items: center; margin-bottom: 10px; ">
                                    <div class="demo-image"
                                        style="border: 1px solid #e8eaef; padding: 5px; margin-bottom: 10px;">
                                        <el-image style="width: 100px; height: 100px;" :src="productList.image"
                                            fit="fill" />
                                    </div>
                                    <a :href="`/product/show/${productList.slug}`">
                                        <span class="demonstration">{{ productList.name }}</span>
                                    </a>
                                </div>
    
                            </el-col>
                        </el-row>
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
    category:{
        type: Object
    },
    subcategory:{
        type: Object
    },
    categoryLists:{
        type: Array
    }
})


const productLists = props.productLists;
const subcategory_name = props.subcategory_name;
// console.log(props.categories);
// console.log(productLists);
// console.log(subcategory_name);
// console.log(props.category);
// console.log(props.subcategory);
// console.log(props.categoryLists);





</script>

<style scoped>
.demo-image {
    margin: auto;
}

.demo-image .block {
    padding: 30px 0;
    text-align: center;
    /* border-right: solid 1px var(--el-border-color); */
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
</style>