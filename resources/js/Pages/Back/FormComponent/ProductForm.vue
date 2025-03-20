<template>
    <div class="form-container">
        <el-form :model="formData" label-width="300px" class="demo-ruleForm" :rules="formRules" ref="internalFormRef">
            <el-form-item label="產品名稱" :label-position="labelPosition" prop="name">
                <el-input v-model="formData.name" />
            </el-form-item>

            <el-form-item label="產品title" :label-position="labelPosition" prop="title">
                <el-input v-model="formData.title" />
            </el-form-item>

            <el-form-item v-if="mode == 'add'" label="類別" :label-position="labelPosition" prop="category_id">
                <el-select v-model="formData.category_id" :teleported="false"
                    @change="categoryChange(formData.category_id)">
                    <el-option v-for="category in formData.categories" :key="category.id" :label="category.name"
                        :value="category.id">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="子類別" :label-position="labelPosition" prop="subcategory_id">
                <el-select v-model="formData.subcategory_id" :teleported="false" @change="handleSelectChange">
                    <el-option v-for="subcategory in formData.subcategories" :key="subcategory.id"
                        :label="subcategory.name" :value="subcategory.id">
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
                    :on-remove="handleRemove" list-type="picture" :auto-upload="false" :limit="1"
                    :on-exceed="handleExceed" ref="upload" popper-class="no-transition" :on-change="handleOnChange">
                    <el-button type="" style="width: 40% !important;">選擇檔案</el-button>
                </el-upload>

                <el-dialog v-model="dialogVisible">
                    <img w-full :src="dialogImageUrl" alt="Preview Image" style="margin: auto;" />
                </el-dialog>
            </el-form-item>
            <el-form-item label="產品描述" :label-position="labelPosition" prop="description">
                <QuillEditor v-model:content="formData.description" content-type="html" theme="snow" ref="quillRef"
                    style="min-height: 200px;" />
            </el-form-item>

            <hr style="margin: 10px auto;">

            <div style="margin: 10px auto;">
                <span>特惠欄位</span>
                <el-tooltip effect="dark" :content="showSpecial ? '點擊隱藏欄位' : '點擊顯示欄位'" placement="top">
                    <el-link @click="showSpecial = !showSpecial" type="primary" :underline="false">
                        {{ showSpecial ? '[顯示]' : '[隱藏]' }}
                    </el-link>
                </el-tooltip>
            </div>

            <transition name="expand-fade">
                <el-form-item v-show="showSpecial" label="特惠訊息" :label-position="labelPosition" prop="special_message">
                    <QuillEditor v-model:content="formData.special_message" content-type="html" theme="snow"
                        ref="specialQuillRef" style="min-height: 200px;" />
                </el-form-item>
            </transition>

            <transition name="expand-fade">
                <el-form-item v-show="showSpecial" label="特惠時間" :label-position="labelPosition" prop="special_start_at">
                    <el-date-picker v-model="formData.special_start_at" type="datetime" placeholder="開始時間"
                        :teleported="false" style="margin-right:10px" />
                </el-form-item>
            </transition>

            <transition name="expand-fade">
                <el-form-item v-show="showSpecial" label="" :label-position="labelPosition" prop="special_end_at">
                    <el-date-picker v-model="formData.special_end_at" type="datetime" placeholder="結束時間"
                        :teleported="false" />
                </el-form-item>
            </transition>
        </el-form>
    </div>
</template>

<script setup>

import { reactive, watch, ref, toRef, onMounted } from "vue";
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
// console.log(props.formData);

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
    const response = await axios.post(`/back/categories/${category_id}/subsel`);
    console.log(response.data);

    props.formData.subcategories = response.data;
    props.formData.subcategory_id = response.data.length ? response.data[0].id : '';
}

const validateEndDate = (rule, value, callback) => {
    if (!value || !props.formData.special_start_at) {
        return callback(); // 若空，交由後端驗證
    }

    const start = new Date(props.formData.special_start_at);
    const end = new Date(value);

    if (end < start) {
        callback(new Error('結束時間需晚於開始時間'));
    } else {
        callback();
    }
};

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
    category_id: [
        { required: true, message: "此選項為必填", trigger: "blur" }
    ],
    special_start_at: [
        { type: 'date', required: false, message: '請選擇開始時間', trigger: 'change' },
    ],
    special_end_at: [
        { type: 'date', required: false, message: '請選擇結束時間', trigger: 'change' },
        { validator: validateEndDate, trigger: 'change' }
    ]
});
const quillRef = ref(null);
const specialQuillRef = ref(null);
const showSpecial = props.mode == 'edit' ? ref(true) : ref(false);


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

const resetForm = () => {
    internalFormRef.value.resetFields();
    quillRef.value.setText('');
    specialQuillRef.value.setText('');
}

const resetFormData = () => {
    props.formData.id = '';
    props.formData.name = '';
    props.formData.title = '';
    props.formData.subcategory_id = '';
    props.formData.published_status = '';
    props.formData.color_codes = [];
    props.formData.subcategories = [];
    props.formData.description = '';
    props.formData.special_message = '';
    props.formData.special_start_at = '';
    props.formData.special_end_at = '';
}

const setFormData = (row) => {
    props.formData.id = row.id;
    props.formData.name = row.name;
    props.formData.subcategories = row.subcategories;
    props.formData.title = row.title;
    props.formData.subcategory_id = parseInt(row.subcategory_id);
    props.formData.published_status = parseInt(row.published_status);
    props.formData.color_codes = row.color_codes;
    props.formData.description = row.description;
    props.formData.special_message = row.special_message;
    props.formData.special_start_at = row.special_start_at;
    props.formData.special_end_at = row.special_end_at;
}


defineExpose({
    formValidate,
    internalFormRef,
    quillRef,
    specialQuillRef,
    resetForm,
    resetFormData,
    setFormData
});

</script>

<style scoped>
::v-deep(.el-input),
::v-deep(.el-select) {
    width: 40%;
}

::v-deep(.el-upload) {
    justify-content: left;
}

::v-deep(.demo-ruleForm > .el-form-item) {
    margin-bottom: 13px;
}

.form-container {
    max-height: 500px;
    overflow-y: auto;
    padding: 10px;
}

.expand-fade-enter-active,
.expand-fade-leave-active {
    transition: opacity 1s ease, max-height 1s ease, transform 1s ease;
    overflow: hidden;
}

.expand-fade-enter-from,
.expand-fade-leave-to {
    opacity: 0;
    max-height: 0;
    transform: translateY(-10px);
}

.expand-fade-enter-to,
.expand-fade-leave-from {
    opacity: 1;
    max-height: 1000px;
    /* 預估最大高度 */
    transform: translateY(0)scale(1);
}
</style>