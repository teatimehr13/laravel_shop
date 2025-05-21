<template>
  <div class="mx-auto px-4 sm:py-8 max-w-[1680px] md:px-12 lg:px-16 xl:px-20 2xl:px-24">
    <div class="my-4 md:my-8 breadcrumb-con">
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
.breadcrumb-con span {
  font-size: 1rem;
}

.breadcrumb-con {
  grid-column: aside / product-con-end;
  /* gap沒有取名，讓他跨過gap到product-con */
}
</style>