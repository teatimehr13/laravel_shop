<script setup>
import { route } from 'ziggy-js'; // 引入 ziggy-js 中的 route 函數
import HelloAnimation from '@/Components/HelloAnimation.vue';
import { ref, watch } from 'vue';
import { usePage, useForm, Link } from '@inertiajs/vue3'
import StepPhone from './RegisterForm/StepPhone.vue';
import StepPassword from './RegisterForm/StepPassword.vue';
import StepInfo from './RegisterForm/StepInfo.vue';


const page = usePage();

// defineProps({
//     status: {
//         type: String,
//     },
// });

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    submitted: false,
    phone: '',
    verify_num: ''
});

const stepActive = ref(0);
const handleNextStep = (next) => {
  stepActive.value = next;
  console.log(stepActive.value);
  
}

const submit = () => {
    form.submitted = true;

    form.post(route('register'), {
        onBefore: () => {
            console.log(form);

            if (!form.password || !form.password_confirmation || !form.email || !form.name) {
                console.log(123);

                return false;
            }
        },
        onSuccess: () => {
            console.log(status);

        },
    });
};

watch(() => form.password, (newVal) => {
    if (form.errors.password) {
        form.errors.password = ''
    }
})
</script>

<template>
    <div class="bg-base h-full flex flex-col min-h-screen">
        <div class="flex-1">
            <nav class="mt-10 mb-6 flex items-center justify-center">
                <HelloAnimation />
            </nav>
            <main>
                <div class="mx-auto max-w-3xl space-y-4 p-4 mb-4">
                    <div>
                        <el-steps :active="stepActive" finish-status="success" simple>
                            <el-step title="手機驗證" />
                            <el-step title="設定密碼" />
                            <el-step title="基本資料" />
                            <el-step title="註冊完成" />
                        </el-steps>
                    </div>
                </div>

                <div class="mx-auto max-w-96 space-y-4 p-4 mb-4">


                    <!-- <StepPhone v-model:form-data="form" :step="stepActive" @nextStep="handleNextStep"/> -->
                     <!-- <StepPassword v-model:form-data="form" :step="stepActive" @nextStep="handleNextStep"/> -->
                       <!-- <StepInfo v-model:form-data="form" :step="stepActive" @nextStep="handleNextStep"/> -->

                       <div class="min-h-80 flex flex-col justify-center gap-10">
                           <h1 class="text-5xl text-center">
                                註冊成功
                           </h1>
                           <div class="text-center">
                             <button
                                class="text-white bg-indigo-400 inline-flex h-12 w-[60%] justify-center items-center rounded-md border border-indigo-300 text-xl font-bold hover:bg-indigo-500 hover:border-indigo-500">
                                返回頁面
                            </button>
                           </div>
                       </div>
                    
                    <!-- <div class="border-card border divide-y rounded-md">
                        <div class="px-6 py-3">
                            <h1 class="text-strong font-medium text-2xl font-bold">
                                註冊
                            </h1>
                        </div>
                        <div class="px-6 py-4">
                            <form @submit.prevent="submit">
                                <div class="mb-4">
                                    <label class="block">
                                        <span class="block text-sm font-medium text-slate-700">用戶名稱</span>
                                        <div class="relative">
                                            <input type="text" v-model="form.name" class="input-style"
                                                :class="{ 'input-invalid': form.submitted && !form.name }" />
                                            <el-icon v-if="form.submitted && !form.name" class="input-invalid-mark"
                                                size="large">
                                                <WarnTriangleFilled />
                                            </el-icon>
                                        </div>

                                        <p v-if="form.submitted && !form.name" class="mt-2 text-red-600 text-sm">
                                            {{ !form.name ? '請輸入用戶稱名' : form.errors.name }}
                                        </p>
                                    </label>
                                </div>
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
                                                :class="{ 'input-invalid': form.submitted && (!form.password || form.errors.password) }" />
                                            <el-icon v-if="form.submitted && (!form.password || form.errors.password)"
                                                class="input-invalid-mark" size="large">
                                                <WarnTriangleFilled />
                                            </el-icon>
                                        </div>
                                        <p v-if="form.submitted && (!form.password || form.errors.password)"
                                            class="mt-2 text-red-600 text-sm">
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
                                            class="mt-2 text-red-600 text-sm">
                                            {{ !form.password_confirmation ? '請輸入正確的密碼' : form.errors.password }}
                                        </p>
                                    </label>
                                </div>

                                <div v-if="page.props.status" class="mb-4 font-medium text-sm text-green-600 mt-2">
                                    {{ page.props.status }}
                                </div>


                                <div>
                                    <button
                                        class="bg-sky-500 hover:bg-sky-600 inline-flex h-9 w-full justify-center items-center rounded-md border border-transparent text-sm text-slate-50 font-bold">
                                        註冊
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
                                            使用google帳號註冊
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
                                            使用Facebook帳號註冊
                                        </span>

                                    </button>
                                </div>

                            </form>
                        </div>

                        <div class="text-center py-4 px-6">
                            <Link :href="route('login')">
                            <button
                                class="text-slate-700 bg-transparent inline-flex h-9 w-full justify-center items-center rounded-md border border-gray-300 text-sm font-bold bg-form-hover hover:border-neutral-400">
                                去登入
                            </button>
                            </Link>
                        </div>
                    </div> -->
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
    @apply border-red-500 text-red-600 focus:border-red-500 focus:ring-red-500
}

.input-invalid-mark {
    @apply text-red-600;
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    margin-right: 10px;
    margin-left: 10px;
}

::v-deep(.el-input__inner:focus) {
    outline: none;
    box-shadow: none;
}

::v-deep(.el-form-item__error){
    margin-top: 2px;
}
</style>