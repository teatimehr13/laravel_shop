<template>
    <aside style="width: 220px;">
        <div>
            <div class="prodcut-list-nav-title">
                <h1>
                    產品分類
                </h1>
            </div>

            <div class="product-list-nav-content">
                <ul class="product-list-link-cont" v-for="(categoryList, idx) in categoryLists">
                    <li class="product-list-item"
                        :class="{ is_active: currentRoute === `/product/list/${categoryList.search_key}` }">
                        <Link :href="route('product.front.index', categoryList.search_key)">
                        {{ categoryList.name }}
                        <el-icon v-if="currentRoute === `/product/list/${categoryList.search_key}`" class="arrow-right">
                            <ArrowRightBold />
                        </el-icon>
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
import { ref, onMounted, watchEffect } from 'vue';
const props = defineProps({
    categoryLists: Array
});

const currentRoute = ref(window.location.pathname);

watchEffect(() => {
    currentRoute.value = window.location.pathname;
});




</script>

<style scoped>
.prodcut-list-nav-title>h1 {
    font-weight: bold;
    font-size: 1.25rem;
}

.prodcut-list-nav-title {
    border-bottom: 3px solid #e7e9ec;
    padding: .5rem 0;
    margin-bottom: .5rem;
}

li.product-list-item a:hover {
    color: rgb(83, 133, 225);
}

li.product-list-item {
    padding-top: .3125rem;
    padding-bottom: .3125rem;
    position: relative;
}

.product-list-item a {
    vertical-align: middle;
}

.is_active {
    color: #000000;
    font-weight: bold;
}

.product-list-link-cont {
    position: relative;
}


.arrow-right {
    position: absolute;
    right: 20px;
    top: 50%;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    color: #626aef;
}
</style>