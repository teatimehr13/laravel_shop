<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3';
import HelloAnimation from '@/Components/HelloAnimation.vue';


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
    submitted: false
});

const submit = () => {
    form.submitted = true;
    if (!form.email || !form.password) return;
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};


</script>

<template>
    <div class="bg-base h-full flex flex-col min-h-screen">
        <div class="flex-1">
            <nav class="mt-10 mb-6 flex items-center justify-center">
               <HelloAnimation />
            </nav>
            <main>
                <div class="mx-auto max-w-96 space-y-4 p-4 mb-4">
                    <div class="border-card border divide-y rounded-md">
                        <div class="px-6 py-3">
                            <h1 class="text-strong font-medium text-2xl font-bold">
                                登入
                            </h1>
                        </div>
                        <div class="px-6 py-4">
                            <form @submit.prevent="submit">
                                <div class="mb-4">
                                    <label class="block">
                                        <span class="block text-sm font-medium text-slate-700">帳號</span>
                                        <div class="relative">
                                            <input type="text" v-model="form.email" class="input-style"
                                                placeholder="yourEmail@example.com"
                                                :class="{ 'input-invalid': form.submitted && !form.email }" />
                                            <el-icon v-if="form.submitted && !form.email" class="input-invalid-mark"
                                                size="large">
                                                <WarnTriangleFilled />
                                            </el-icon>
                                        </div>
                                        <p v-if="form.submitted && !form.email" class="mt-2 text-pink-600 text-sm">
                                            請輸入正確的Email
                                        </p>
                                    </label>
                                </div>

                                <div class="mb-4">
                                    <label class="block">
                                        <span class="block text-sm font-medium text-slate-700">密碼</span>
                                        <div class="relative">
                                            <input type="password" v-model="form.password" class="input-style"
                                                :class="{ 'input-invalid': form.submitted && !form.password }" />
                                            <el-icon v-if="form.submitted && !form.password" class="input-invalid-mark"
                                                size="large">
                                                <WarnTriangleFilled />
                                            </el-icon>
                                        </div>
                                        <p v-if="form.submitted && !form.password" class="mt-2 text-pink-600 text-sm">
                                            請輸入正確的密碼
                                        </p>
                                    </label>
                                </div>

                                <div class="flex items-center justify-end mb-4">
                                    <Link :href="route('password.request')"
                                        class="underline text-sm text-blue-700 hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    忘記密碼?
                                    </Link>
                                </div>

                                <div>
                                    <button
                                        class="bg-sky-500 hover:bg-sky-600 inline-flex h-9 w-full justify-center items-center rounded-md border border-transparent text-sm text-slate-50 font-bold">
                                        提交
                                    </button>
                                </div>

                                <div class="flex items-center my-4">
                                    <hr class="flex-grow border-t border-gray-300" />
                                    <span class="mx-4 text-gray-500 text-sm">or</span>
                                    <hr class="flex-grow border-t border-gray-300" />
                                </div>

                                <div class="mb-2">
                                    <button
                                        class="bg-transparent inline-flex h-9 w-full justify-center items-center rounded-md border border-gray-300 text-sm  font-bold bg-form-hover hover:border-neutral-400">

                                        <span
                                            class="inline-flex items-center gap-1.5 focus-within:outline-none focus-visible:outline-none">
                                            <img alt="Google"
                                                class="object-contain object-center leading-none shrink-0 size-5"
                                                src="/storage/app/public/svg_icon/0df9a2ae114efbe63df9.svg">
                                            使用google帳號登入
                                        </span>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <button
                                        class="bg-transparent inline-flex h-9 w-full justify-center items-center rounded-md border border-gray-300 text-sm  font-bold bg-form-hover hover:border-neutral-400">
                                        <span
                                            class="inline-flex items-center gap-1.5 focus-within:outline-none focus-visible:outline-none">
                                            <img alt="Google"
                                                class="object-contain object-center leading-none shrink-0 size-5"
                                                src="/storage/app/public/svg_icon/facebook-1-svgrepo-com.svg">
                                            使用Facebook帳號登入
                                        </span>

                                    </button>
                                </div>

                            </form>
                        </div>

                        <div class="text-center py-4 px-6">
                            <Link :href="route('register.phone')">
                                <button
                                    class="text-slate-700 bg-transparent inline-flex h-9 w-full justify-center items-center rounded-md border border-gray-300 text-sm font-bold bg-form-hover hover:border-neutral-400">
                                    註冊會員
                                </button>
                            </Link>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.bg-base {
    background-color: hsla(216, 18%, 98%);
}

.border-card {
    border-color: hsl(222, 12%, 83.9%);
    background-color: white;
}

.bg-form-hover:hover {
    background-color: hsla(226 08% 46.5% / 0.05);
}

.input-style {
    @apply mt-2 block w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm shadow-sm placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none
}

.input-invalid {
    @apply border-pink-500 text-pink-600 focus:border-pink-500 focus:ring-pink-500
}

.input-invalid-mark {
    @apply text-pink-600;
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    margin-right: 10px;
    margin-left: 10px;
}


</style>