<template>
    <BackendLayout />

    <div>

    </div>
</template>


<script setup>
import { onMounted, ref, defineProps } from 'vue';
import api from '@/api/api';
import BackendLayout from '@/Layouts/BackendLayout.vue';


const props = defineProps({
    data: {
        type: Object,
        required: true
    },

});

console.log(props);

</script>