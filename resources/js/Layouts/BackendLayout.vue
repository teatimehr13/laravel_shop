<template>
    <div class="layout">
        <header>
            <Header />
        </header>

        <!-- <aside> -->
            <!-- <Sidebar /> -->
        <!-- </aside> -->

        <!-- <main>
            <slot />
        </main> -->
    </div>
</template>

<script setup>
import Header from '@/Components/Back/Header.vue';
// import Sidebar from '@/Components/Sidebar.vue';
</script>

<style scoped>
.layout {
    display: flex;
    flex-direction: row;
}
header {
    width: 100%;
    height: 60px;
    background-color: #333;
    color: white;
    /* 可以根據設計需求自定義樣式 */
}
aside {
    width: 250px;
    background-color: #f8f9fa;
    /* 可以根據設計需求自定義樣式 */
}
main {
    flex-grow: 1;
    padding: 20px;
}
</style>


<!-- 在子頁面引入以下 -->
<!-- import BackendLayout from '@/Layouts/BackendLayout.vue';

defineProps({
    layout: BackendLayout,
}); -->