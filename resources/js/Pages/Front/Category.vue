<template>
    <FrontendLayout>
        <template #switch>
            <section>
                <div style="max-width: 1200px; margin: auto;">

                    <div>
                        <h1>所有產品類別</h1>
                    </div>

                    <el-row :gutter="10" v-for="(category, idx) in categories" :key="category.id">
                        <el-col :span="24" style="border-bottom: 3px solid #e5e7eb; margin-bottom: 1.875rem; padding-bottom: 1rem;">
                            <h1>
                                {{ category.name }}
                            </h1>
                        </el-col>

                        <el-col v-for="subcategory in category.subcategories" :key="subcategory.id" class="block"
                            :xs="12" :sm="6" :md="3" :lg="3">
                            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 10px; ">
                                <div class="demo-image" style="border: 1px solid #e8eaef; padding: 5px; margin-bottom: 10px;">
                                    <el-image style="width: 100px; height: 100px;" :src="subcategory.image"
                                        fit="fill" />
                                </div>
                                <a :href="`/product/list/${subcategory.search_key}`">
                                    <span class="demonstration">{{ subcategory.name }}</span>
                                </a>
                            </div>
                        </el-col>
                    </el-row>

                </div>
            </section>
        </template>
    </FrontendLayout>
</template>


<script setup>
import FrontendLayout from '@/Layouts/FrontendLayout.vue';

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