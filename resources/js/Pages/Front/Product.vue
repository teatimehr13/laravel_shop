<template>
    <div v-for="(category, idx) in categories" :key="category.id">
        <h1>
            {{ category.name }}
        </h1>

        <!-- <div v-for="subcategory in category.subcategories" :key="subcategory.id">
            <img :src="subcategory.image" alt="">
        </div> -->

        <div class="demo-image">
            <div v-for="subcategory in category.subcategories" :key="subcategory.id" class="block">
                <span class="demonstration">{{ subcategory.name }}</span>
                <el-image style="width: 100px; height: 100px" :src="subcategory.image" fit="fill" />
            </div>
        </div>
    </div>
</template>


<script setup>

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