<template>
    <aside style="width: 220px;">
        <a href="#">test</a>
        <div>
            <div class="prodcut-list-nav-title">
                <h1>
                    產品分類
                </h1>
            </div>

            <div class="product-list-nav-content">
                <ul class="product-list-link-cont" v-for="(categoryList, idx) in categoryLists">
                    <li class="product-list-item">
                        <Link :href="route('product.front.index', categoryList.search_key)">
                        {{ categoryList.name }}
                        </Link>

                        <noscript>
                            <a :href="`/product/list/${categoryList.search_key}`">{{ categoryList.name }}</a>
                        </noscript>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    categoryLists: Array
});

console.log(props.categoryLists);


</script>

<style scoped>
.prodcut-list-nav-title>h1 {
    font-weight: bold;
    font-size: 1.25rem;
}

li.product-list-item a:hover {
    color: rgb(83, 133, 225);
}

li.product-list-item {
    padding-top: .3125rem;
    padding-bottom: .3125rem;
}
</style>