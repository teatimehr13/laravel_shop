<template>
    <el-container>
        <!-- Header 固定 -->
        <el-header>
            <Header />
        </el-header>

        <!-- 主內容 -->
        <el-container>
            <!-- Aside 固定 -->
            <el-aside width="200px">
                <Aside />
            </el-aside>

            <!-- Main 動態插槽 -->
            <el-main>
                <slot name="switch" />
                <!-- <slot /> -->
            </el-main>
        </el-container>
    </el-container>
</template>

<script setup>
import Header from '@/Components/Back/Header.vue';
import Aside from '@/Components/Back/Aside.vue';

</script>

<style>
/* .layout {
    display: flex;
    flex-direction: row;
}

header {
    width: 100%;
    height: 60px;
    background-color: #333;
    color: white;
}

aside {
    width: 250px;
    background-color: #f8f9fa;
}

main {
    flex-grow: 1;
    padding: 20px;
} */
.el-header {
    background-color: #B3C0D1;
    color: #333;
    text-align: center;
    line-height: 60px;
}

.el-aside {
    background-color: #D3DCE6;
    color: #333;
    text-align: center;
    line-height: 200px;
}

.el-main {
    background-color: #E9EEF3;
    color: #333;
    /* text-align: center;
    line-height: 160px; */
}

body>.el-container {
    margin-bottom: 40px;
}

.el-container:nth-child(5) .el-aside,
.el-container:nth-child(6) .el-aside {
    line-height: 260px;
}

.el-container:nth-child(7) .el-aside {
    line-height: 320px;
}
</style>