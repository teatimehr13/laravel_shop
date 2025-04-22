<template>
    <!-- <div class="aside">
    <ul>
      <li>test1 </li>
      <li>test2 </li>
    </ul>
  </div> -->

    <div class="aside">

        <el-menu :default-active="currentRoute" class="el-menu-vertical-demo">
            <el-menu-item index="/back/stores" @click="navigateTo('stores.index')">
                <el-icon></el-icon>
                <span>stores</span>
            </el-menu-item>

            <el-menu-item index="/back/products" @click="navigateTo('products.index')">
                <el-icon></el-icon>
                <span>products</span>
            </el-menu-item>

            <el-menu-item index="/back/news" @click="navigateTo('news.index')">
                <el-icon>
                    <!-- <document /> -->
                </el-icon>
                <span>news</span>
            </el-menu-item>
            <el-menu-item index="/back/categories" @click="navigateTo('categories.index')">
                <el-icon>
                    <!-- <setting /> -->
                </el-icon>
                <span>categories</span>
            </el-menu-item>

            <el-menu-item index="/back/backorder" @click="navigateTo('backorder.index')">
                <el-icon>
                    <!-- <setting /> -->
                </el-icon>
                <span>order</span>
            </el-menu-item>
        </el-menu>
    </div>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
const currentRoute = ref(window.location.pathname);


onMounted(() => {
    router.on("navigate", () => {
        currentRoute.value = window.location.pathname;
    });
});

const navigateTo = (routeName) => {
    router.visit(route(routeName));
};



// import {

// } from '@element-plus/icons-vue'
</script>