<template>
    <div class="border-card border divide-y rounded-md">
        <div class="px-6 py-3">
            <h1 class="text-strong font-medium text-2xl font-bold">
                基本資料
            </h1>
        </div>
        <div class="px-6 py-4">
            <el-form :model="formData" class="demo-ruleForm" :rules="formRules" ref="internalFormRef">
                <div class="mb-8">
                    <label class="block mb-2">
                        <el-form-item label="" prop="name" class="relative w-full">
                            <el-input v-model="formData.name" placeholder="請輸入姓名" type="text" />
                        </el-form-item>
                    </label>

                    <label class="block">
                        <el-form-item label="" prop="email" class="relative w-full">
                            <el-input v-model="formData.email" placeholder="請輸入email" type="email" />
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

<script setup>
import { reactive, ref } from 'vue';
import { View, Hide } from '@element-plus/icons-vue'

const props = defineProps({
    formData: Object,
    step: Number
})

const step = props.step;
const formData = reactive({ ...props.formData });

const emit = defineEmits(['nextStep']);

const nextStep = async () => {
    const valid = await formValidate();
    if (!valid) return;
    await emit('nextStep', 3)
}

console.log(props.formData);
console.log(props.step);



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


</script>

<style scoped></style>