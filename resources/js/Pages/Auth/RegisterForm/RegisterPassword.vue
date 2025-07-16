<template>
    <RegisterLayout>
        <template #form>
            <div class="border-card border divide-y rounded-md">
                <div class="px-6 py-3">
                    <h1 class="text-strong font-medium text-2xl font-bold">
                        密碼設定
                    </h1>
                </div>
                <div class="px-6 py-4">
                    <el-form :model="form" class="demo-ruleForm" :rules="formRules" ref="internalFormRef">
                        <div class="mb-8">
                            <label class="block mb-2">
                                <el-form-item label="" prop="password" class="relative w-full" :error="form.errors.password">
                                    <el-input v-model="form.password" placeholder="請輸入密碼"
                                        :type="showPassword ? 'text' : 'password'" />
                                    <el-icon @click="toggleShowPassword" class="cursor-pointer"
                                        style="position: absolute; top: 0; bottom: 0; margin: auto; right: 10px;">
                                        <component :is="showPassword ? View : Hide" />
                                    </el-icon>
                                </el-form-item>
                            </label>

                            <label class="block">
                                <el-form-item label="" prop="password_confirmation" class="relative w-full" :error="form.errors.password">
                                    <el-input v-model="form.password_confirmation" placeholder="請再次輸入密碼"
                                        :type="showPasswordConfirm ? 'text' : 'password'" />
                                    <el-icon @click="toggleShowPasswordConfirm" class="cursor-pointer"
                                        style="position: absolute; top: 0; bottom: 0; margin: auto; right: 10px;">
                                        <component :is="showPasswordConfirm ? View : Hide" />
                                    </el-icon>
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
import { View, Hide } from '@element-plus/icons-vue'
import { usePage, useForm, Link, router } from '@inertiajs/vue3'
import RegisterLayout from './RegisterLayout.vue';

const register = usePage().props.register || {};

const form = useForm({
    password: register.password || '',
    password_confirmation: register.password || '',
});


const nextStep = async () => {
    const valid = await formValidate();
    if (!valid) return;
    await submit();
}


const submit = () => {
    form.post(route('register.post.password'), {
        onSuccess: () => router.visit(route('register.info')),
        onError: (errors) => {
            console.log(errors.password);
        }
    });
}


const validatePasswordDif = (rule, value, callback) => {
    if (form.password !== form.password_confirmation) {
        console.log(form);
        return callback(new Error('密碼與再次輸入密碼不同')); // 若空，交由後端驗證
    }
    return true;
};

const showPassword = ref(false)
const showPasswordConfirm = ref(false)
const toggleShowPassword = () => (showPassword.value = !showPassword.value)
const toggleShowPasswordConfirm = () => (showPasswordConfirm.value = !showPasswordConfirm.value)

const internalFormRef = ref();
const formRules = reactive({
    password: [
        { required: true, message: "密碼未填寫", trigger: "submit" },
        { min: 8, message: '密碼至少8位', trigger: 'submit' },
        { pattern: /[a-z]/, message: '需含小寫字母', trigger: 'submit' },
        { pattern: /[0-9]/, message: '需含數字', trigger: 'submit' },
        { validator: validatePasswordDif, trigger: 'submit' }
    ],
    password_confirmation: [
        { required: true, message: "密碼未填寫", trigger: "submit" },
        { validator: validatePasswordDif, trigger: 'submit' }
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


</script>

<style scoped></style>