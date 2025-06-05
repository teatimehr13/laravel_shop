<template>
    <div class="border-card border divide-y rounded-md">
        <div class="px-6 py-3">
            <h1 class="text-strong font-medium text-2xl font-bold">
                手機驗證
            </h1>
        </div>
        <div class="px-6 py-4">
            <!-- <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block">
                        <div class="relative">
                            <input type="text" v-model="form.name" class="input-style"
                                :class="{ 'input-invalid': form.submitted && !form.name }" />
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
                            <el-icon v-if="form.submitted && !form.email" class="input-invalid-mark" size="large">
                                <WarnTriangleFilled />
                            </el-icon>
                        </div>
                        <p v-if="form.submitted && !form.email" class="mt-2 text-pink-600 text-sm">
                            請輸入正確的Email
                        </p>
                    </label>
                </div>
            </form> -->

            <el-form :model="formData" class="demo-ruleForm" :rules="formRules" ref="internalFormRef">
                <div class="mb-4">
                    <label class="block mb-2">
                        <div class="flex gap-2">
                            <el-form-item label="" prop="phone" class="relative w-[70%]">
                                <el-input v-model="formData.phone" placeholder="請輸入手機號碼進行驗證" />
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
                                <el-input v-model="formData.verify_num" placeholder="請輸入驗證碼" />
                            </el-form-item>
                        </div>
                    </label>
                </div>
            </el-form>

            <button @click="nextStep"
                class="text-white bg-indigo-400 inline-flex h-8 w-full justify-center items-center rounded-md border border-indigo-300 text-sm font-bold  hover:bg-indigo-500 hover:border-indigo-500">
                下一步
            </button>
        </div>


    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';

const props = defineProps({
    formData: Object,
    step: Number
})

const step = props.step;

const emit = defineEmits(['nextStep']);

const nextStep = async () => {
    const valid = await formValidate();
    if (!valid) return;
    await emit('nextStep', 1)
}

console.log(props.formData);
console.log(props.step);




const internalFormRef = ref();
const formRules = reactive({
    phone: [
        { required: true, message: "手機號碼未填寫", trigger: "blur" },
    ],
    verify_num: [
        { required: true, message: "未輸入驗證碼", trigger: "blur" },
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