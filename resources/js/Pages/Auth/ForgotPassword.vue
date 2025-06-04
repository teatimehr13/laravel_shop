<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import HelloAnimation from '@/Components/HelloAnimation.vue';

defineProps({
    status: {
        type: String,
    },
});

const countdown = ref(0)
const error = ref(null)
const hit_status = ref(true);
const invalid_style = ref(false);

const form = useForm({
    email: '',
    submitted: false
});

//email 格式驗證
const isEmailFormat = computed(() => {
  const email = form.email
  const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/  
  return pattern.test(email)
})

const submit = () => {
    error.value = null
    invalid_style.value = false

    form.post(route('password.email'), {
        onError: (errors) => {
            console.log(123);
            const messages = errors.email || []
            if (typeof messages === 'string' && messages.includes('寄送上限')) {
                hit_status.value = false
            }
            // 429 節流錯誤會被包含在 response error 裡（用 fetch intercept 的話）
        },
        onFinish: () => {
            // Laravel 429 不會進錯誤欄位，而是 HTTP 例外
        },
        onBefore: () => {
            form.submitted = true;
            //前端驗證不要雙向綁定
            if (!form.email || !isEmailFormat.value) {
                invalid_style.value = true;
                return false
            }

            if (!hit_status.value) {
                // error.value = `你今天已達寄送上限，請明天再試。`
                return false
            }

            if (countdown.value > 0) {
                error.value = `請稍後再試，還有 ${countdown.value} 秒才能再次寄送。`
                return false
            }
        },
        onSuccess: () => {
            hit_status.value = true
            countdown.value = 60
            const interval = setInterval(() => {
                countdown.value--
                if (countdown.value <= 0) clearInterval(interval)
            }, 1000)
        }
    })
}

watch(() => form.email, (newVal) => {
  if (form.submitted && isEmailFormat.value) {
    invalid_style.value = false
  }
})

</script>

<template>
    <div class="bg-base h-full flex flex-col min-h-screen">
        <div class="flex-1">
            <nav class="mt-10 mb-6 flex items-center justify-center">
                <!-- <h1 class="text-2xl text-slate-700">
                    hello
                </h1> -->
                <HelloAnimation />
            </nav>
            <main>
                <div class="mx-auto max-w-96 space-y-4 p-4 mb-4">
                    <div class="border-card border divide-y rounded-md">
                        <div class="px-6 py-3">
                            <h1 class="text-strong font-medium text-2xl font-bold">
                                忘記密碼
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
                                                :class="{ 'input-invalid': invalid_style || form.errors.email }" />
                                            <el-icon v-if="invalid_style || form.errors.email" class="input-invalid-mark"
                                                size="large">
                                                <WarnTriangleFilled />
                                            </el-icon>
                                        </div>
                                        <p v-if="invalid_style " class="mt-2 text-red-600 text-sm">
                                            請輸入正確的Email
                                        </p>

                                        <div v-else-if="form.errors.email" class="mt-2 text-red-600 text-sm">
                                            {{ form.errors.email }}
                                        </div>
                                    </label>
                                </div>
                                <div>
                                    <button
                                        class="bg-sky-500 hover:bg-sky-600 disabled:bg-sky-200 inline-flex h-9 w-full justify-center items-center rounded-md border border-transparent text-sm text-slate-50 font-bold"
                                        :disabled="countdown > 0 || !hit_status">
                                        {{ form.submitted && countdown > 0 ? `重新發送 ${countdown} 秒` : '送出' }}
                                    </button>


                                    <div v-if="error" class="text-orange-600 text-sm mt-2">
                                        {{ error }}
                                    </div>

                                    <div v-if="status" class="mb-4 font-medium text-sm text-green-600 mt-2">
                                        {{ status }}
                                    </div>
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
    @apply border-red-500  focus:border-red-500 focus:ring-red-500
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
</style>