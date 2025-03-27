<template>
    <div class="checkout-page">
      <h2>訂單商品</h2>
  
      <div v-for="item in cartItems" :key="item.productOption.id" class="checkout-item">
        <img :src="item.productOption.image" alt="商品圖片" width="100" />
        <div>{{ item.productOption.name }} - {{ item.productOption.color_name }}</div>
        <div>單價：{{ formatCurrency(item.productOption.price) }}</div>
        <div>數量：{{ item.quantity }}</div>
        <div>小計：{{ formatCurrency(item.productOption.price * item.quantity) }}</div>
      </div>
  
      <hr />
  
      <h3>寄送資訊</h3>
      <label>
        姓名：<input v-model="form.name" type="text" />
      </label>
      <label>
        電話：<input v-model="form.phone" type="text" />
      </label>
      <label>
        地址：<input v-model="form.address" type="text" />
      </label>
  
      <label>
        備註：<input v-model="form.remark" type="text" />
      </label>
  
      <div class="total">
        運費：{{ formatCurrency(shippingFee) }}<br />
        總付款金額：<strong>{{ formatCurrency(totalPrice + shippingFee) }}</strong>
      </div>
  
      <button @click="submitOrder">下訂單</button>
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue'
  import axios from 'axios'
  
  // 模擬購物車資料（你可從 props 傳入）
  const cartItems = ref([
    {
      productOption: {
        id: 1,
        name: 'α1 II',
        color_name: '銀色',
        price: 28980,
        image: '/storage/product_options/123.jpg'
      },
      quantity: 2
    },
    {
      productOption: {
        id: 2,
        name: 'α1 II',
        color_name: '粉色',
        price: 11110,
        image: '/storage/product_options/456.jpg'
      },
      quantity: 1
    }
  ])
  
  const form = ref({
    name: '',
    phone: '',
    address: '',
    remark: ''
  })
  
  const shippingFee = 45
  
  const totalPrice = computed(() => {
    return cartItems.value.reduce((sum, item) => {
      return sum + item.productOption.price * item.quantity
    }, 0)
  })
  
  const formatCurrency = (num) => {
    return 'NT$' + num.toLocaleString()
  }
  
  const submitOrder = async () => {
    const payload = {
      cart_items: cartItems.value.map(item => ({
        product_option_id: item.productOption.id,
        quantity: item.quantity
      })),
      shipping: {
        name: form.value.name,
        phone: form.value.phone,
        address: form.value.address
      },
      remark: form.value.remark
    }
  
    try {
      const res = await axios.post('/checkout', payload)
      alert('訂單送出成功！')
    } catch (error) {
      alert('訂單送出失敗')
      console.error(error)
    }
  }
  </script>
  
  <style scoped>
  .checkout-page {
    max-width: 600px;
    margin: 0 auto;
  }
  
  .checkout-item {
    border-bottom: 1px solid #ddd;
    padding: 10px 0;
  }
  </style>
  