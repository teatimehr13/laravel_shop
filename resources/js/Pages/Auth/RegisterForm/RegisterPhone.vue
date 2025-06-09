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
                            <label class="block mb-2">
                                <div class="flex gap-2">
                                    <el-form-item label="" prop="phone" class="relative w-[70%]">
                                        <el-input v-model="form.phone" placeholder="請輸入手機號碼進行驗證" />
                                    </el-form-item>

                                    <button
                                        class="text-slate-700 bg-transparent inline-flex h-8 w-[30%] justify-center items-center rounded-md border border-gray-300 text-sm font-bold bg-form-hover hover:border-neutral-400">
                                        取得驗證碼
                                    </button>
                                </div>
                            </label>

                            <label class="block">
                                <div class="flex gap-2">
                                    <el-form-item label="" prop="verify_num" class="relative w-full">
                                        <el-input v-model="form.verify_num" placeholder="請輸入驗證碼" />
                                    </el-form-item>
                                </div>
                            </label>

                            <p v-if="form.errors.phone" class="back-error">
                                {{ form.errors.phone }}
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
// console.log(usePage().props.errors.step_message);

const register = usePage().props.register || {};
const step_error = usePage().props.errors.step_message || '';

onMounted(() => {
  if (step_error) {
    ElMessage.error(step_error);
  }
});

const form = useForm({
    verify_num: register.verify_num || '',
    phone: register.phone || '',
});


const nextStep = async () => {
    const valid = await formValidate();
    if (!valid) return;
    await submit();
}


const submit = () => {
    return form.post(route('register.post.phone'), {
        onSuccess: () => router.visit(route('register.password')),
        onError: (errors) => {
            // console.log(form);
            console.log(errors.phone);
        }
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
    ]
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



</script>

<style scoped></style>