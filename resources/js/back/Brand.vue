<template>
    <div>
        <h2>Brand</h2>
        <input type="text" v-model="test">
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
let test = ref('');

// onMounted(()=>{
//         fetch('http://127.0.0.1:8000')
//         .then((res)=>{
//             return res.json();
//         })
//         .then((text)=>{
//             console.log(text);
//         })
//     })
</script>