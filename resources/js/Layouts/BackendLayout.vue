<template>
    <el-container>
        <!-- Header 固定 -->
        <el-header>
            <Header />
        </el-header>

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
    background-color: #ffffff;
    color: #333;
    text-align: center;
    line-height: 60px;
}

/* .el-aside {
    background-color: #ffffff;
    color: #333;
    text-align: center;
    line-height: 200px;
    border-top: 1px solid #f3f3f3;
} */

.el-main {
    background-color: rgb(238, 241, 248);
    color: #333;
    border-top: 1px solid rgb(237, 240, 247);
    padding: 20px;
}

.el-main>div:first-of-type {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 4px 0 rgba(74, 91, 109, .1);
    padding: 16px 24px 24px;
}

body>.el-container {
    margin-bottom: 40px;
}

/* .el-container:nth-child(5) .el-aside,
.el-container:nth-child(6) .el-aside {
    line-height: 260px;
}

.el-container:nth-child(7) .el-aside {
    line-height: 320px;
} */
</style>