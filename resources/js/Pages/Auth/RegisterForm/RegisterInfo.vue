<template>
    <RegisterLayout>
        <template #form>
            <div class="border-card border divide-y rounded-md">
                <div class="px-6 py-3">
                    <h1 class="text-strong font-medium text-2xl font-bold">
                        基本資料
                    </h1>
                </div>
                <div class="px-6 py-4">
                    <el-form :model="form" class="demo-ruleForm" :rules="formRules" ref="internalFormRef">
                        <div class="mb-8">
                            <label class="block mb-2">
                                <el-form-item label="" prop="name" class="relative w-full">
                                    <el-input v-model="form.name" placeholder="請輸入姓名" type="text" />
                                </el-form-item>
                            </label>

                            <label class="block">
                                <el-form-item label="" prop="email" class="relative w-full">
                                    <el-input v-model="form.email" placeholder="請輸入email" type="email" />
                                </el-form-item>
                            </label>
                        </div>
                    </el-form>

                    <div>
                        <button @click="nextStep"
                            class="text-white bg-indigo-400 inline-flex h-8 w-full justify-center items-center rounded-md border border-indigo-300 text-sm font-bold  hover:bg-indigo-500 hover:border-indigo-500">
                            下一步
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </RegisterLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { usePage, useForm, Link, router } from '@inertiajs/vue3'
import RegisterLayout from './RegisterLayout.vue';
const register = usePage().props.register || {};


const form = useForm({
    name: register.name || '',
    email: register.email || '',
    // password: '',
    // password_confirmation: '',
    // submitted: false,
    // phone: '',
    // verify_num: ''
});


const nextStep = async () => {
    const valid = await formValidate();
    if (!valid) return;
    await submit();
}

const submit = () => {
    // form.post(route('register.post.info'), {
    //     onSuccess: () => router.visit(route('register.success')),

    //     // 422 驗證錯誤，errors 物件就是後端回來的錯誤欄位
    //     onError: (errors) => {
    //         console.log(errors.email);
            
    //         if (errors.email) {
    //             showMessage('error', errors.email);
    //         } else {
    //             showMessage('error', '資料有誤，請再檢查一次');
    //         }
    //     },

    //     // 無論成功或失敗都會呼叫
    //     onFinish: (visit) => {
    //         // Inertia v1 會把 visit 物件帶進來（router.post 才有）
    //         const status = visit?.response?.status;
    //         if (status === 403) {
    //             const msg = visit.response?.data?.message ?? '此帳號無法操作';
    //             showMessage('warning', msg);
    //         }
    //     },
    // })

    showMessage('warning', '尚無開放帳號註冊');
}


const internalFormRef = ref();
const formRules = reactive({
    name: [
        { required: true, message: "姓名未填寫", trigger: "blur" },
        {
            pattern: /^[\u4e00-\u9fa5A-Za-z\s]+$/,
            message: '姓名只能輸入中英文，不可含特殊符號',
            trigger: 'blur'
        },
        { min: 2, max: 30, message: '請輸入有效的姓名，不可少於2字或多餘30字', trigger: 'blur' },
    ],
    email: [
        { required: true, message: "email未填寫", trigger: "blur" },
        { type: 'email', message: 'Email格式不正確', trigger: 'blur' }
    ],
});


const formValidate = async () => {
    if (!internalFormRef.value) return;
    try {
        await internalFormRef.value.validate();
        return true;
    } catch {
        return false;
    }
}

//通知
const showMessage = (type, title) => {
    ElNotification({
        type, // "success" 或 "error"
        title,
        position: 'bottom-left',
    });
};

</script>

<style scoped></style>