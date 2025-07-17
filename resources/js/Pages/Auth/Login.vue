<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3';
import HelloAnimation from '@/Components/HelloAnimation.vue';


const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    redirect: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
    submitted: false,
    errors: { email: '' },
    redirect: props.redirect || null
});


const internalFormRef = ref();
const formRules = computed(() => ({
    email: [
        {
            required: true,
            message: "手機號碼或Email未填寫",
            trigger: 'submit'
        },
        {
            validator: validateAccount,
            trigger: 'submit'
        }
    ],
    password: [
        {
            required: true,
            message: "密碼未填寫",
            trigger: 'submit'
        }
    ]
}));

const clearErrors = () => (form.errors.email = '');

const validateAccount = (rule, value, callback) => {
    if (
        /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value) ||
        /^09\d{8}$/.test(value) ||
        /^\+8869\d{8}$/.test(value)
    ) {
        callback();
    } else {
        callback(new Error('請輸入正確的 Email 或手機號碼'));
    }

    // const isEmail = /^[\w.%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(value);
    // const isPhoneLocal = /^09\d{8}$/.test(value);
    // const isPhoneIntl = /^\+8869\d{8}$/.test(value);

    // // ➜ 只要三種格式「全部都不符合」才報錯
    // if (!isEmail && !isPhoneLocal && !isPhoneIntl) {
    //     callback(new Error('請輸入正確的 Email 或手機號碼'));
    // } else {
    //     callback();
    // }
};


const formValidate = async () => {
    // if (!internalFormRef.value) return;
    clearErrors();
    try {
        await internalFormRef.value.validate();
        return true;
    } catch {
        return false;
    }
}

const submit = async () => {
    const validate = await formValidate();
    if (!validate) return;

    await form.post(route('login'), {
        onError: (errors) => {
            console.log(errors);
        },
        onSuccess: () => {
        }
        // onFinish: () => form.reset('password'),
    });
};

const loginWith3Party = ($path) => {
    window.location.href = route($path)
}



</script>

<template>
    <div class="bg-base h-full flex flex-col min-h-screen">
        <div class="flex-1">
            <nav class="mt-10 mb-6 flex items-center justify-center">
                <HelloAnimation />
            </nav>
            <main>
                <div class="mx-auto max-w-96 space-y-4 p-4">
                    <div class="border-card border divide-y rounded-md">
                        <div class="px-6 py-3">
                            <h1 class="text-strong font-medium text-2xl font-bold">
                                登入
                            </h1>
                        </div>
                        <div class="px-6 py-4">
                            <el-form :model="form" class="demo-ruleForm" :rules="formRules" ref="internalFormRef">
                                <div>
                                    <label class="block mb-2 relative">
                                        <span class="block text-sm font-medium text-slate-700">帳號</span>
                                        <div class="relative">
                                            <el-form-item label="" prop="email" :error="form.errors.email"
                                                class="relative w-full">
                                                <el-input v-model="form.email" placeholder="輸入手機號碼或Email" class="pt-2"
                                                    type="text" />
                                            </el-form-item>
                                        </div>
                                    </label>

                                    <label class="block">
                                        <span class="block text-sm font-medium text-slate-700">密碼</span>
                                        <div class="relative">
                                            <el-form-item label="" prop="password" class="relative w-full">
                                                <el-input v-model="form.password" class="pt-2" type="password" />
                                            </el-form-item>
                                        </div>
                                    </label>

                                </div>
                            </el-form>

                            <div class="flex items-center justify-end mb-4">
                                <Link :href="route('password.request')"
                                    class="underline text-sm text-blue-700 hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                忘記密碼?
                                </Link>
                            </div>

                            <div>
                                <button type="button" @click="submit"
                                    class="bg-sky-500 hover:bg-sky-600 inline-flex h-9 w-full justify-center items-center rounded-md border border-transparent text-sm text-slate-50 font-bold">
                                    登入
                                </button>
                            </div>

                            <div class="flex items-center my-4">
                                <hr class="flex-grow border-t border-gray-300" />
                                <span class="mx-4 text-gray-500 text-sm">or</span>
                                <hr class="flex-grow border-t border-gray-300" />
                            </div>

                            <div class="mb-2">
                                <button
                                    class="bg-transparent inline-flex h-9 w-full justify-center items-center rounded-md border border-gray-300 text-sm  font-bold bg-form-hover hover:border-neutral-400" @click="loginWith3Party('auth.google')">
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
                                    class="bg-transparent inline-flex h-9 w-full justify-center items-center rounded-md border border-gray-300 text-sm  font-bold bg-form-hover hover:border-neutral-400" @click="loginWith3Party('auth.facebook')">
                                    <span
                                        class="inline-flex items-center gap-1.5 focus-within:outline-none focus-visible:outline-none">
                                        <img alt="Google"
                                            class="object-contain object-center leading-none shrink-0 size-5"
                                            src="/storage/app/public/svg_icon/facebook-1-svgrepo-com.svg">
                                        使用Facebook帳號登入
                                    </span>

                                </button>
                            </div>
                        </div>

                        <!-- <div class="text-center py-4 px-6">
                            <Link :href="route('register.phone')">
                            <button
                                class="text-slate-700 bg-transparent inline-flex h-9 w-full justify-center items-center rounded-md border border-gray-300 text-sm font-bold bg-form-hover hover:border-neutral-400">
                                註冊會員
                            </button>
                            </Link>
                        </div> -->
                    </div>
                </div>
                <div class="mx-auto max-w-96 space-y-4 p-4 mb-4 text-center">
                    還沒有入會員?
                    <Link :href="route('register.phone')" class="text-blue-600">
                    註冊
                    </Link>
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

.back-error {
    color: #f56c6c;
    font-size: 12px;
    left: 0;
    line-height: 1;
    padding-top: 2px;
    margin: 5px 0 10px 0.125rem;
}

::v-deep(.el-form-item__error) {
    margin-top: 2px;
}

::v-deep(.el-input__inner:focus) {
    outline: none;
    box-shadow: none;
}
</style>