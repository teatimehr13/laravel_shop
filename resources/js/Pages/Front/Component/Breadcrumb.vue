<template>
  <div class="layout-container">
    <div class="breadcrumb-con">
      <el-breadcrumb separator=">">
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

.breadcrumb-con {
  grid-column: aside / product-con-end;
  /* gap沒有取名，讓他跨過gap到product-con */
}
</style>