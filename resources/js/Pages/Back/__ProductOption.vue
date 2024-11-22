<template>
    <table border="1">
      <thead>
        <tr>
          <th>顏色名稱</th>
          <th>價格</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(option, index) in productOptions" :key="option.id">
          <td>{{ option.color_name }}</td>
          <td>{{ option.price }}</td>
          <td>
            <button @click="toggleRow(index)">
              {{ option.isExpanded ? '折疊' : '展開' }}
            </button>
          </td>
        </tr>
        <!-- 確保 option 存在，並使用安全的判斷 -->
        <tr v-if="productOptions[index] && productOptions[index].isExpanded" :key="`expanded-${productOptions[index].id}`">
          <td colspan="3">
            <div class="expanded-content">
              <p>更多信息：{{ productOptions[index].details }}</p>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </template>
  
  <script setup>
  import { reactive } from 'vue';
  
  const productOptions = reactive([
    {
      id: 1,
      color_name: '黑色',
      price: '1500',
      details: '黑色產品的更多信息...',
      isExpanded: false,
    },
    {
      id: 2,
      color_name: '白色',
      price: '1800',
      details: '白色產品的更多信息...',
      isExpanded: false,
    },
    // 更多顏色選項...
  ]);
  
  const toggleRow = (index) => {
    productOptions[index].isExpanded = !productOptions[index].isExpanded;
  };
  </script>
  
  <style scoped>
  .expanded-content {
    padding: 10px;
    background-color: #f9f9f9;
    border-top: 1px solid #ddd;
  }
  
  button {
    cursor: pointer;
  }
  </style>
  