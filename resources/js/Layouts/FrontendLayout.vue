<template>
    <el-container>
        <!-- Header 固定 -->
        <el-header>
            <Header />

        </el-header>
        <!-- Main 動態插槽 -->
        <el-main>
            <slot name="switch" />
            <!-- <slot /> -->
        </el-main>
    </el-container>
</template>

<script setup>
import Header from '@/Components/Front/Header.vue';


</script>

<style>
.el-header {
    background-color: #ffffff;
    color: #333;
    text-align: center;
    height: 60px;
    border-bottom: 1px solid #f3f3f3;
}

.el-main {
  --el-main-padding: 0px;
  padding: 0 !important;
}

.el-container,
.el-header,
.el-footer {
  padding: 0 !important;
  margin: 0 !important;
}

:root {
    --left-space: 1fr;
    --aside-width: 220px;
    --gap-width: 20px;
    --product-width: clamp(900px, 70vw, 1330px);
    --right-space: 1fr;
}

.layout-container {
    display: grid;
    grid-template-columns:
        [left-space] var(--left-space)
        [aside] var(--aside-width)
        [gap] var(--gap-width)
        [product-con] var(--product-width)
        [right-space] var(--right-space);

    padding-left: 2rem;
    padding-right: 2rem;
}

</style>