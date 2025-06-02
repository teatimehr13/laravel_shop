<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
    submitted: false
});

const submit = () => {
    form.submitted = true;
    console.log(form);

    if (!form.password || !form.password_confirmation || !form.email) {
        return;
    }

    form.post(route('password.store'), {
        onSuccess: () => form.reset('password', 'password_confirmation'),
        // onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="bg-base h-full flex flex-col min-h-screen">
        <div class="flex-1">
            <nav class="my-8 flex items-center justify-center">
                <h1 class="text-2xl text-slate-700">
                    hello
                </h1>
            </nav>
            <main>
                <div class="mx-auto max-w-96 space-y-4 p-4 mb-4">
                    <div class="border-card border divide-y rounded-md">
                        <div class="px-6 py-3">
                            <h1 class="text-strong font-medium text-2xl font-bold">
                                密碼重設
                            </h1>
                        </div>
                        <div class="px-6 py-4">
                            <form @submit.prevent="submit">
                                <div class="mb-4">
                                    <label class="block">
                                        <span class="block text-sm font-medium text-slate-700">Email</span>
                                        <div class="relative">
                                            <input type="text" v-model="form.email" class="input-style"
                                                placeholder="yourEmail@example.com" autofocus
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
                                                :class="{ 'input-invalid': form.submitted && (!form.password || form.errors.password) }" />
                                            <el-icon v-if="form.submitted && (!form.password || form.errors.password)"
                                                class="input-invalid-mark" size="large">
                                                <WarnTriangleFilled />
                                            </el-icon>
                                        </div>
                                        <p v-if="form.submitted && (!form.password || form.errors.password)"
                                            class="mt-2 text-pink-600 text-sm">
                                            {{ !form.password ? '請輸入正確的密碼' : form.errors.password }}
                                        </p>
                                    </label>
                                </div>

                                <div class="mb-4">
                                    <label class="block">
                                        <span class="block text-sm font-medium text-slate-700">再次確認密碼</span>
                                        <div class="relative">
                                            <input type="password" v-model="form.password_confirmation"
                                                class="input-style"
                                                :class="{ 'input-invalid': form.submitted && !form.password_confirmation }" />
                                            <el-icon v-if="form.submitted && !form.password_confirmation"
                                                class="input-invalid-mark" size="large">
                                                <WarnTriangleFilled />
                                            </el-icon>
                                        </div>
                                        <p v-if="form.submitted && !form.password_confirmation"
                                            class="mt-2 text-pink-600 text-sm">
                                            請輸入正確的密碼
                                        </p>

                                        <p v-if="form.errors.password_confirmation" class="mt-2 text-pink-600 text-sm">
                                            {{ form.errors.password_confirmation }}
                                        </p>
                                    </label>
                                </div>

                                <div>
                                    <button
                                        class="bg-sky-500 hover:bg-sky-600 inline-flex h-9 w-full justify-center items-center rounded-md border border-transparent text-sm text-slate-50 font-bold">
                                        送出
                                    </button>
                                </div>

                            </form>
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