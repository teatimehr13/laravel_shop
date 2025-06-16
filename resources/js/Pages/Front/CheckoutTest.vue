<template>
    <form action="/ecpay/checkout" method="post" ref="form">
        <input type="hidden" name="_token" :value="csrf">
        <input type="hidden" name="amount" :value="amount" />
        <button type="submit">立即付款</button>
    </form>

</template>

<script setup>
import { ref } from 'vue';
const amount = 9999;
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
console.log(csrf);

</script>