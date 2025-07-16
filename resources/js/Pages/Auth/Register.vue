<script setup>
import { route } from 'ziggy-js'; // 引入 ziggy-js 中的 route 函數
import HelloAnimation from '@/Components/HelloAnimation.vue';
import { ref, watch } from 'vue';
import { usePage, useForm, Link } from '@inertiajs/vue3'
import StepPhone from './RegisterForm/RegisterPhone.vue';
import StepPassword from './RegisterForm/RegisterPassword.vue';
import StepInfo from './RegisterForm/RegisterInfo.vue';


const page = usePage();
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