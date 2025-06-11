<template>
    <RegisterLayout>
        <template #form>
            <div class="border-card border divide-y rounded-md">
                <div class="px-6 py-3">
                    <h1 class="text-strong font-medium text-2xl font-bold">
                        手機驗證
                    </h1>
                </div>
                <div class="px-6 py-4">
                    <el-form :model="form" class="demo-ruleForm" :rules="formRules" ref="internalFormRef">
                        <div>
                            <label class="block mb-2 relative">
                                <div class="flex gap-2">
                                    <el-form-item label="" prop="phone" class="relative w-[70%]">
                                        <el-input v-model="form.phone" placeholder="請輸入手機號碼進行驗證" />
                                    </el-form-item>

                                    <button type="button" @click="getSmsCode" :disabled="countdown > 0"
                                        class="text-slate-700 bg-transparent inline-flex h-8 w-[30%] justify-center items-center rounded-md border border-gray-300 text-sm font-bold bg-form-hover hover:border-neutral-400 disabled:text-gray-300">
                                        取得驗證碼
                                    </button>
                                </div>
                                <p v-if="send_sms_status" class="text-xs absolute bottom-0 m-auto translate-x-0.5">
                                    <span class="text-red-500">
                                        {{ countdown }}
                                    </span>
                                    {{ send_sms_msg }}
                                </p>

                                <p v-if="!send_sms_status && send_sms_msg"
                                    class="text-red-500 text-xs absolute bottom-0 m-auto translate-x-0.5">
                                    {{ send_sms_msg }}
                                </p>
                            </label>

                            <label class="block">
                                <div class="flex gap-2">
                                    <el-form-item label="" prop="verify_num" class="relative w-full">
                                        <el-input v-model="form.verify_num" placeholder="請輸入驗證碼" />
                                    </el-form-item>
                                </div>
                            </label>

                            <p v-if="form.errors.phone || form.errors.verify_num" class="back-error">
                                {{ form.errors.phone || form.errors.verify_num }}
                            </p>

                        </div>
                    </el-form>

                    <button type="button" @click="nextStep"
                        class="text-white bg-indigo-400 inline-flex h-8 w-full justify-center items-center rounded-md border border-indigo-300 text-sm font-bold  hover:bg-indigo-500 hover:border-indigo-500">
                        下一步
                    </button>
                </div>
            </div>
        </template>
    </RegisterLayout>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { usePage, useForm, Link, router } from '@inertiajs/vue3';
import RegisterLayout from './RegisterLayout.vue';
import axios from 'axios';

// console.log(usePage().props.errors.step_message);
console.log(usePage().props);

const register = usePage().props.register || {};
const step_error = usePage().props.errors.step_message || '';
const send_sms_status = ref(false);
const send_sms_msg = ref('');
// const sms = usePage().props.sms.sms_verify_code;

let timer = null;
const countdown = ref(0);

onMounted(() => {
    if (step_error) {
        ElMessage.error(step_error);
    }
});

const form = useForm({
    verify_num: register.verify_num || '',
    phone: register.phone || '',
});

const startCountdown = () => {
    countdown.value = 60;
    if (timer) clearInterval(timer);

    timer = setInterval(() => {
        if (countdown.value > 0) {
            countdown.value--;
        } else {
            send_sms_status.value = false;
            send_sms_msg.value = '';
            clearInterval(timer);
        }
    }, 1000)
}

const nextStep = async () => {
    if (!form.verify_num || !form.phone) return
    const valid = await formValidate();
    if (!valid) return;
    await submit();
}


const submit = () => {
    return form.post(route('register.post.phone'), {
        onSuccess: () => router.visit(route('register.password')),
        onError: (errors) => {
            // console.log(form);
            console.log(errors);
        }
    })
}

const getSmsCode = async () => {
    // if (!form.phone) {
    //     send_sms_status.value = false;
    //     send_sms_msg.value = '未填寫手機號碼';
    //     return
    // }

     const valid = await formValidate();
    if (!valid) return;
    return await axios.post(route('register.sendSmsCode'), { phone: form.phone })
        .then(res => {
            if (res.data.success) {
                send_sms_status.value = true;
                send_sms_msg.value = `秒後可再次獲取驗證碼`;
                // msg('驗證碼寄送成功', 'success');
                let sms = res.data.sms;
                msg(`您獲取得驗證碼為: ${sms}`, 'success');
                startCountdown();
                // alert(`您獲取得驗證碼為: ${sms}`)
            } else if (res.data.error) {
                console.log(res.data.error);
                send_sms_status.value = false;
                send_sms_msg.value = res.data.error; // 後端錯誤訊息直接顯示
            }
        })
        .catch(err => {
            console.log(err.response?.data?.error);
            send_sms_status.value = false;
            send_sms_msg.value = err.response?.data?.error || '驗證碼發送失敗';
        })
}


const internalFormRef = ref();
const formRules = computed(() => ({
    phone: [
        {
            required: true,
            message: "手機號碼未填寫",
            trigger: 'submit'
        },
        {
            pattern: /^09\d{8}$/,
            message: "手機號碼格式錯誤",
            trigger: 'submit'
        }
    ],
}));

const formValidate = async () => {
    if (!internalFormRef.value) return;
    try {
        await internalFormRef.value.validate();
        return true;
    } catch {
        return false;
    }
}


const msg = (msg, type) => {
    ElMessage({
        message: msg,
        type: type,
    })
}

</script>

<style scoped>
.back-error {
    margin: 0 0 10px 0.125rem;
}
</style>