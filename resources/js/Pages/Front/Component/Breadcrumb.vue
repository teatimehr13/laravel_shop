<template>
  <div class="layout-container">
    <div class="breadcrumb-con">
      <el-breadcrumb separator=">" >
        <el-breadcrumb-item v-if="category">
          <a href="/categories">{{ category.name }}</a>
        </el-breadcrumb-item>
    
        <el-breadcrumb-item v-if="subcategory">
          <a :href="`/product/list/${subcategory.search_key}`">
            {{ subcategory.name }}
          </a>
        </el-breadcrumb-item>
    
        <el-breadcrumb-item v-if="product">
          {{ product.name }}
        </el-breadcrumb-item>
      </el-breadcrumb>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

defineProps({
  category: Object,
  subcategory: Object,
  product: Object
});
</script>

<style scoped>

.breadcrumb-con {
  margin: 2rem 0;
}

.breadcrumb-con span {
  font-size: 1rem;
  line-height: 2;
}

.layout-container {
  display: grid;
  grid-template-columns:
    [left-space] 1fr 
    [aside] 220px 
    [gap] 20px 
    [product-con] clamp(900px, 70vw, 1330px) 
    [right-space] 1fr;

  grid-template-rows: auto 1fr; /* 第一行放 breadcrumb，第二行放 main 內容 */
}

.breadcrumb-con {
  grid-column: aside / gap-end; /* gap沒有取名，讓他跨過gap到product-con */
}
</style>