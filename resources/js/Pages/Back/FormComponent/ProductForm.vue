<template>
    <el-form :model="formData" label-width="300px" class="demo-ruleForm" :rules="formRules" ref="internalFormRef">
        <el-form-item label="產品名稱" :label-position="labelPosition" prop="name">
            <el-input v-model="formData.name" />
        </el-form-item>
        <!-- <el-form-item label="類型" :label-position="labelPosition" prop="store_type">
            <el-select v-model="formData.store_type" :teleported="false" @change="handleSelectChange">
                <el-option label="直營" :value="0" />
                <el-option label="特約展售" :value="1" />
                <el-option label="授權經銷商" :value="2" />
            </el-select>
        </el-form-item> -->

        <!-- <el-form-item label="類別" :label-position="labelPosition" prop="address">
            <el-input v-model="formData.address" />
        </el-form-item> -->

         <el-form-item label="操作" :label-position="labelPosition" prop="published_status">
            <el-radio-group v-model="formData['published_status']">
                <el-radio :value="1">顯示</el-radio>
                <el-radio :value="0">隱藏</el-radio>
            </el-radio-group>
        </el-form-item>


        <!-- <el-form-item label="顏色" :label-position="labelPosition" prop="color_codes">
            <el-input v-model="formData.color_codes" type="textarea" :autosize="{ minRows: 2, maxRows: 6 }" />
        </el-form-item> -->

        <el-form-item label="圖片" :label-position="labelPosition" prop="image">
            <el-upload :file-list="fileList" class="upload-demo" action="" :on-preview="handlePreview"
                :on-remove="handleRemove" list-type="picture" :auto-upload="false" :limit="1" :on-exceed="handleExceed"
                ref="upload" popper-class="no-transition" :on-change="handleOnChange">
                <el-button type="" style="width: 100% !important;">選擇檔案</el-button>
            </el-upload>

            <el-dialog v-model="dialogVisible">
                <img w-full :src="dialogImageUrl" alt="Preview Image" style="margin: auto;" />
            </el-dialog>
        </el-form-item>

       
    </el-form>
</template>

<script setup>

import { reactive, watch, ref, toRef } from "vue";
import { genFileId } from 'element-plus';

//拿到父組件的資料
const props = defineProps({
    formData: Object,
    fileList: Array,
    uploadList: Array,
});

//傳給父組件資料
const emits = defineEmits(["update:file-list", "update:form-data", "submit", "uploadList"]);

//不可以直接修改
const uploadList = toRef(props, 'uploadList');
const localUploadList = ref();
const upload = ref([]);
const labelPosition = ref('top');
const toggleUpload = ref(false);
const dialogImageUrl = ref('');
const dialogVisible = ref(false);

// console.log(toRef(props,'formData'));



const internalFormRef = ref(null); // 引用 el-form

const formValidate = () => {
    return new Promise((resolve, reject) => {
        if (!internalFormRef.value) {
            reject(new Error("internalFormRef 未綁定"));
        } else {
            internalFormRef.value.validate((isValid) => {
                if (isValid) {
                    console.log('true');
                    resolve(true);
                } else {
                    console.log('false');
                    reject(false);
                }
            });
        }
    });
};

const handleSelectChange = () => {
    internalFormRef.value.clearValidate(['store_type']); // 清除 store_type 的驗證錯誤
};

const formRules = reactive({
    store_name: [
        { required: true, message: "門市名稱為必填項", trigger: "blur" },
    ],
    store_type: [
        { required: true, message: "類型為必選項", trigger: "submit" },
    ],
    address: [
        { max: 255, message: "地址不能超過 255 個字", trigger: "blur" },
    ],
    'is_enabled': [
        { required: true, message: "請選擇顯示或隱藏", trigger: "submit" }
    ],
    image: [
        {
            validator: (rule, value, callback) => {
                if (localUploadList.value.length === 0) {
                    console.log(localUploadList.value);
                    callback();
                } else {
                    const file = localUploadList.value[0].raw;
                    console.log(file);
                    const validTypes = ["image/jpeg", "image/png", "image/gif"];
                    if (!validTypes.includes(file.type)) {
                        callback(new Error("圖片格式僅限 jpg/png/gif"));
                    } else {
                        callback();
                    }
                }
            },
            trigger: "change",
        },
    ],
});

defineExpose({
    formValidate,
    internalFormRef,
});

//刪除待上傳的圖片
const handleRemove = (uploadFile, uploadFiles) => {
    // console.log(uploadFile, uploadFiles)
    localUploadList.value = [];
    emits("uploadList", localUploadList.value);
}

//點擊待上傳的圖片
const handlePreview = (uploadFile) => {
    console.log(uploadFile)
    dialogImageUrl.value = uploadFile.url
    dialogVisible.value = true
}

//upload變更時
const handleOnChange = (file) => {
    // console.log(file);

    localUploadList.value = [{
        name: file.name,
        url: file.url,
        uid: file.uid,
        raw: file.raw,
    }];
    // console.log(localUploadList.value[0].raw);
    emits("uploadList", localUploadList.value);
}

//限制僅上傳一筆
const handleExceed = (files) => {
    // console.log(files);
    upload.value.clearFiles();
    const file = files[0];
    file.uid = genFileId(); // 生成唯一的 UID
    upload.value.handleStart(file)
};


</script>
