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
    line-height: 60px;
}

.el-main {
    /* background-color: rgb(238, 241, 248);
    color: #333;
    border-top: 1px solid rgb(237, 240, 247);
    padding: 20px; */
}

/* .el-main>div:first-of-type {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 4px 0 rgba(74, 91, 109, .1);
    padding: 16px 24px 24px;
}

body>.el-container {
    margin-bottom: 40px;
} */
</style>