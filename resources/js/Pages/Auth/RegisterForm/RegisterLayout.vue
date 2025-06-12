<script setup>
import HelloAnimation from '@/Components/HelloAnimation.vue';
import { computed, ref, watch } from 'vue';
import { usePage, useForm, Link, router } from '@inertiajs/vue3';

const register = usePage().props.register || {};
const expiredAt = register._expiredAt ? Number(register._expiredAt) : null;
const expired = ref(false);

console.log(register);

const routeName = usePage().component; // Inertia 預設會有 component 名，或用 window.location.pathname 判斷
console.log(routeName);

const stepActive = computed(() => {
  switch (routeName) {
    case 'Auth/RegisterForm/RegisterPhone':     
      return 0;
    case 'Auth/RegisterForm/RegisterPassword':
      return 1;
    case 'Auth/RegisterForm/RegisterInfo':
      return 2;
    case 'Auth/RegisterForm/RegisterSuccess':
      return 4;
    default:
      return 0;
  }
});


if (expiredAt) {
    const left = expiredAt - Math.floor(Date.now() / 1000); // 剩餘秒數
    if (left <= 0) {
        expired.value = true;
    } else {
        setTimeout(() => {
            expired.value = true;
            if (!window._register_alerted) {
                window._register_alerted = true;
                alert('表單已過期，請重新操作');
                 router.visit('/register/phone');
            }else{
                router.visit('/register/phone');
            }
        }, left * 1000);
    }
}
</script>

<template>
    <div class="bg-base h-full flex flex-col min-h-screen">
        <div class="flex-1">
            <nav class="mt-10 mb-6 flex items-center justify-center">
                <HelloAnimation />
            </nav>
            <main>
                <div class="mx-auto max-w-md space-y-4 p-4 mb-4">
                    <div>
                        <el-steps :active="stepActive" finish-status="success" align-center>
                            <el-step title="手機驗證" />
                            <el-step title="設定密碼" />
                            <el-step title="基本資料" />
                            <el-step title="註冊完成" />
                        </el-steps>
                    </div>
                </div>

                <div class="mx-auto max-w-96 space-y-4 p-4">
                    <slot name="form" />
                </div>

                <div class="mx-auto max-w-96 space-y-4 p-4 mb-4 text-center">
                    已經有帳號了? 
                    <Link :href="route('login')" class="text-blue-600">
                        登入
                    </Link>
                </div>
            </main>
        </div>
    </div>

</template>

<style>
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

.el-input__inner:focus {
    outline: none;
    box-shadow: none;
}

.el-form-item__error {
    margin-top: 2px;
}

.back-error {
    color: #f56c6c;
    font-size: 12px;
    left: 0;
    line-height: 1;
    padding-top: 2px;
    margin: 0 0 10px 0.125rem;
}
</style>