<template>
    <div v-show="!newCoRowVisible" class="new_co_row">
        <el-form :model="newCoRowData" ref="newCoFormRef">
            <el-row>
                <el-col>
                    <el-form-item prop="color_name">
                        <el-input v-model="newCoRowData.color_name" placeholder="輸入顏色" size="small" />
                    </el-form-item>
                </el-col>
                <el-col>
                    <el-form-item prop="color_code">
                        <el-input v-model="newCoRowData.color_code" placeholder="輸入顏色" size="small" />
                    </el-form-item>
                </el-col>

                <el-col>
                    <el-form-item prop="price">
                        <el-input v-model.number="newCoRowData.price" placeholder="輸入價格" size="small" />
                    </el-form-item>
                </el-col>

                <el-col>
                    <el-form-item prop="enable">
                        <el-radio-group v-model="newCoRowData.enable">
                            <el-radio :value="1">顯示</el-radio>
                            <el-radio :value="0">隱藏</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>

                <el-col>
                    <el-form-item prop="image">
                        <div class="icon_gp" v-if="!newCoRowVisible">
                            <input ref="fileInput" type="file" class="hidden-input" @change="handleFileChange" />

                            <div v-if="fileListAdd_co.length > 0" class="custom-file-wrapper">
                                <img :src="fileListAdd_co[0].url" class="upload-img" />
                                <div class="file-actions">
                                    <el-icon class="action-icon" @click="handlePreview(fileListAdd_co[0])">
                                        <ZoomIn />
                                    </el-icon>
                                    <el-icon class="action-icon" @click="handleRemove(fileListAdd_co[0])">
                                        <Delete />
                                    </el-icon>
                                    <el-icon class="action-icon plus-icon" @click="triggerUpload">
                                        <Plus />
                                    </el-icon>
                                </div>
                            </div>

                            <div v-else class="upload-placeholder" @click="triggerUpload">
                                <el-icon>
                                    <Plus />
                                </el-icon>
                            </div>
                        </div>
                    </el-form-item>
                </el-col>

                <el-col>
                    <el-form-item>
                        <el-button size="small" type="success" @click="">
                            新增
                        </el-button>
                        <el-button size="small" @click="">
                            取消
                        </el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
    </div>

    <el-col>
        <el-button style="margin: 10px 0;" @click="" v-show="!newCoRowVisible">添加顏色</el-button>
    </el-col>

    <el-dialog v-model="addCoVisible" style="width: max-content;">
        <img w-full :src="addCoImageUrl" alt="Preview Image" />
    </el-dialog>
</template>

<script setup>
import { reactive, watch, ref, toRef } from "vue";

const props = defineProps({
    newCoRowData: Object,
    newCoRowVisible: Number,
    fileListAdd_co: Object
})

const emit = defineEmits([
    "update:fileListAdd_co",
    // "update:tempRow",
    // "toggle-edit",
    // "cancel-edit"
]);

const addCoImageUrl = ref('');
const addCoVisible = ref(false);


const handleFileChange = (event) => {
    const file = event.target.files[0];
    console.log(file);

    if (file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            emit("update:fileListAdd_co", [{ name: file.name, url: reader.result, file: file }]);
        };
    }
};


const handleRemove = (file) => {
    emit("update:fileListAdd_co", []);
    // emit("update:tempRow", { ...props.tempRow, image: "" });
};

const handlePreview = (file) => {
    addCoVisible.value = true;
    addCoImageUrl.value = file.url;
};


const triggerUpload = () => {
    fileInput.value.click();
};

const fileInput = ref(null);



</script>

<style scoped>
::v-deep(.el-row) {
    display: grid;
    grid-template-columns: 130px 130px 130px 200px 178px 200px;
    align-items: center;
    padding: 8px 0;
    /* margin-top: 10px; */
}

::v-deep(.el-row > div) {
    padding: 0 12px !important;
}

::v-deep(.el-form-item) {
    margin: auto;
}

::v-deep(.demo-ruleForm > .el-form-item) {
    margin: 20px auto;
}


::v-deep(.el-form-item__label) {
    margin-bottom: 4px;
    color: #172b4d;
    width: auto !important;
}

.custom-file-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
}

.upload-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    max-width: 50px;
}

.file-actions {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s;
}

.custom-file-wrapper:hover .file-actions {
    opacity: 1;
}

.action-icon {
    font-size: 20px;
    color: white;
    cursor: pointer;
}

.plus-icon {
    font-size: 20px;
    color: white;
}

.hidden-input {
    display: none;
}

.icon_gp .el-icon:hover {
    transform: scale(1.3);
    transition: linear .15s;
}
</style>