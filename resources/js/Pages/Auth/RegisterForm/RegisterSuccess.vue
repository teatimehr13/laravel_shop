<template>
    <RegisterLayout>
        <template #form>
            <div class="min-h-80 flex flex-col justify-center gap-10">
                <h1 class="text-5xl text-center">
                    註冊成功
                </h1>
                <div class="text-center">
                     <Link :href="route('login')">
                        <button
                            class="text-white bg-indigo-400 inline-flex h-12 w-[60%] justify-center items-center rounded-md border border-indigo-300 text-xl font-bold hover:bg-indigo-500 hover:border-indigo-500">
                            返回頁面
                        </button>
                    </Link>
                </div>
            </div>

        </template>
    </RegisterLayout>
</template>

<script setup>
import { usePage, router, Link } from '@inertiajs/vue3';
import RegisterLayout from './RegisterLayout.vue';


</script>