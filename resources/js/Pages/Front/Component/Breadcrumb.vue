<template>
  <div class="layout-container">
    <div class="breadcrumb-con">
      <el-breadcrumb separator=">">
        <el-breadcrumb-item>
          <a href="/categories">
            <strong>
              <el-icon style="vertical-align: text-top;">
                <HomeFilled />
              </el-icon>
              所有產品類別
            </strong>
          </a>
        </el-breadcrumb-item>

        <el-breadcrumb-item v-if="category && subcategory && !product">
          <strong>{{ category.name }}</strong>
        </el-breadcrumb-item>
        <el-breadcrumb-item v-else-if="category && subcategory && product">
          <a :href="`/product/list/${subcategory.search_key}`">{{ category.name }}</a>
        </el-breadcrumb-item>

        <el-breadcrumb-item v-if="subcategory && !product">
          {{ subcategory.name }}>
        </el-breadcrumb-item>

        <el-breadcrumb-item v-else-if="subcategory">
          <a :href="`/product/list/${subcategory.search_key}`">{{ subcategory.name }}</a>
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

const props = defineProps({
  category: Object,
  subcategory: Object,
  product: Object
});

console.log(props.category);


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