<template>
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
  </template>
  
  <script setup>
  import { defineProps } from "vue";
  
  defineProps({
    category: Object,
    subcategory: Object,
    product: Object
  });
  </script>
  