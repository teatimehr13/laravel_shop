<template>
    <form @submit.prevent="login">
        <input v-model="email" type="email" placeholder="Email" required />
        <input v-model="password" type="password" placeholder="Password" required />
        <button type="submit">Login</button>
    </form>
</template>


<script setup>

import { onMounted, ref } from 'vue';
// import web from '@/web/web';
import api from '@/api/api';

const email = ref('');
const password = ref('');

async function login(params) {
    try {
        const response = await api.post('/login_access', {
            email: email.value,
            password: password.value
        })
        console.log(response.data);
        window.location.href = '/cart';
    } catch (error) {
        console.error('Login failed:', error);
    }
}

</script>