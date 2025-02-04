<template>
    <el-form :model="formData" label-width="300px" class="demo-ruleForm" :rules="formRules" ref="internalFormRef">
        <el-form-item label="產品名稱" :label-position="labelPosition" prop="name">
            <el-input v-model="formData.name" />
        </el-form-item>

        <el-form-item label="產品title" :label-position="labelPosition" prop="title">
            <el-input v-model="formData.title" />
        </el-form-item>

        <el-form-item v-if="mode == 'add'" label="類別" :label-position="labelPosition" prop="category_id">
            <el-select v-model="formData.category_id" :teleported="false" @change="categoryChange(formData.category_id)">
                <el-option v-for="category in formData.categories" :key="category.id" :label="category.name"
                    :value="category.id">
                </el-option>
            </el-select>
        </el-form-item>

        <el-form-item label="子類別" :label-position="labelPosition" prop="subcategory_id">
            <el-select v-model="formData.subcategory_id" :teleported="false" @change="handleSelectChange">
                <el-option v-for="subcategory in formData.subcategories" :key="subcategory.id" :label="subcategory.name"
                    :value="subcategory.id">
                </el-option>
            </el-select>
        </el-form-item>

        <el-form-item label="操作" :label-position="labelPosition" prop="published_status">
            <el-radio-group v-model="formData['published_status']">
                <el-radio :value="1">上架</el-radio>
                <el-radio :value="0">下架</el-radio>
            </el-radio-group>
        </el-form-item>

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
import axios from "axios";

//拿到父組件的資料
const props = defineProps({
    formData: Object,
    fileList: Array,
    uploadList: Array,
    mode: {
        type: String,
        default: "edit", // 預設為編輯模式
    },
});
console.log(props.formData);

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
    // internalFormRef.value.clearValidate(['store_type']); // 清除 store_type 的驗證錯誤
};

const categoryChange = async (category_id) => {
    const response =  await axios.post(`/back/categories/${category_id}/subsel`);
    console.log(response.data);   
    
    props.formData.subcategories = response.data;
    props.formData.subcategory_id = response.data.length ? response.data[0].id : '';
}

const formRules = reactive({
    name: [
        { required: true, message: "產品名稱必填項", trigger: "blur" },
    ],
    title: [
        { required: true, message: "產品title為必選項", trigger: "submit" },
    ],
    subcategory_id: [
        { required: true, message: "子類別為必選項", trigger: "blur" },
    ],
    'published_status': [
        { required: true, message: "請選擇顯示或隱藏", trigger: "submit" }
    ],
    image: [
        {
            validator: (rule, value, callback) => {
                console.log(localUploadList.value);

                if (!localUploadList.value || localUploadList.value.length === 0) {
                    return callback();
                }

                const file = localUploadList.value[0]?.raw;
                if (!file) {
                    return callback();
                }

                const validTypes = ["image/jpeg", "image/png", "image/gif"];
                if (!validTypes.includes(file.type)) {
                    return callback(new Error("圖片格式僅限 jpg/png/gif"));
                }

                callback();
            },
            trigger: "change",
        },
    ],

    category_id: [{ required: true, message: "此選項為必填", trigger: "blur" }]
});

// watch(
//     () => props.mode,
//     (newMode) => {
//         if (newMode === "add") {
//             formRules.category_id = [{ required: true, message: "此選項為必填", trigger: "blur" }];
//         } else {
//             delete formRules.category_id;
//         }
//     },
//     { immediate: true }
// );

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
    // console.log(localUploadList.value[0]);
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


