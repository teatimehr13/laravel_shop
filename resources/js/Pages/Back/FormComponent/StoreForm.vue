<template>
    <el-form :model="formData" label-width="auto">
        <el-form-item label="門市名稱" :label-position="labelPosition">
            <el-input v-model="formData.store_name" />
        </el-form-item>
        <el-form-item label="類型" :label-position="labelPosition">
            <el-select v-model="formData.store_type" :teleported="false">
                <el-option label="直營" :value="0" />
                <el-option label="特約展售" :value="1" />
                <el-option label="授權經銷商" :value="2" />
            </el-select>
        </el-form-item>

        <el-form-item label="地址" :label-position="labelPosition">
            <el-input v-model="formData.address" />
        </el-form-item>

        <el-form-item label="聯絡電話" :label-position="labelPosition">
            <el-input v-model="formData.contact_number" />
        </el-form-item>

        <el-form-item label="營業時間" :label-position="labelPosition">
            <el-input v-model="formData.opening_hours" type="textarea" :autosize="{ minRows: 2, maxRows: 6 }" />
        </el-form-item>

        <el-form-item label="圖片" :label-position="labelPosition">
            <el-upload :file-list="fileList" class="upload-demo" action="" :on-preview="handlePreview"
                :on-remove="handleRemove" list-type="picture" :auto-upload="false" :limit="1" :on-exceed="handleExceed"
                ref="upload" popper-class="no-transition" :on-change="handleOnChange">
                <el-button type="" style="width: 100% !important;">選擇檔案</el-button>
            </el-upload>

        </el-form-item>
        <!-- <el-form-item>
            <el-button type="primary" @click="$emit('submit', formData)">提交</el-button>
            <el-button @click="closePopover(scope.row.id)">關閉</el-button>
        </el-form-item> -->
    </el-form>
</template>

<script setup>

import { reactive, watch, ref, toRef } from "vue";
import { genFileId } from 'element-plus'

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

const test = () => {
    console.log(uploadList.value);
}

//刪除待上傳的圖片
const handleRemove = (uploadFile, uploadFiles) => {
    console.log(uploadFile, uploadFiles)
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
    console.log(file);

    localUploadList.value = [{
        name: file.name,
        url: file.url,
        uid: file.uid,
        raw: file.raw,
    }];

    emits("uploadList", localUploadList.value);
}

//限制僅上傳一筆
const handleExceed = (files) => {
    console.log(files);

    upload.value.clearFiles();
    const file = files[0];
    file.uid = genFileId(); // 生成唯一的 UID
    upload.value.handleStart(file)
};
</script>
