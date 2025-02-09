<template>
    <div v-show="newCoRowVisible" class="new_co_row">
        <el-form :model="newCoRowData" ref="newCoFormRef" :rules="rules">
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
                        <div class="icon_gp" v-if="newCoRowVisible">
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
                        <div>
                            <el-button size="small" type="success" @click="toggleAdd">
                                新增
                            </el-button>
                            <el-button size="small" @click="$emit('toggle-add-btn')">
                                取消
                            </el-button>
                        </div>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
    </div>

    <el-col>
        <div>
            <el-button style="margin: 10px 0;" @click="toggleAddbtn" v-show="!newCoRowVisible">添加顏色</el-button>
        </div>
    </el-col>

    <el-dialog v-model="addCoVisible" style="width: max-content;">
        <img w-full :src="addCoImageUrl" alt="Preview Image" />
    </el-dialog>
</template>

<script setup>
import {  computed, reactive, watch, ref, toRef } from "vue";

const props = defineProps({
    newCoRowData: Object,
    newCoRowVisible: Boolean,
    fileListAdd_co: Array
})

// 使用 `computed` 來代理 `fileListAdd_co`
const fileListAdd_co = computed(() => props.fileListAdd_co);

const emit = defineEmits([
    //v-model綁定的話要加"update:"
    "update:fileListAdd_co",
    "toggle-add",
    "toggle-add-btn"
    // "update:tempRow",
    // "toggle-edit",
    // "cancel-edit"
]);

const rules = reactive({
    color_name: [
        { required: true, message: "顏色名稱為必填項", trigger: "blur" },
    ],
    color_code: [
        { required: true, message: "顏色為必填項", trigger: "blur" },
    ],
    price: [
        { required: true, message: "價格為必填項", trigger: "blur" },
        { type: 'number', message: "需為數字類型", trigger: "blur" },
    ],
    enable: [
        { required: true, message: "請選擇顯示或隱藏", trigger: "submit" }
    ],
    image: [
        {
            validator: (rule, value, callback) => {
                // console.log(fileListAdd_co.value);
                if (fileListAdd_co.value.length === 0) {
                    console.log(fileListAdd_co.value);
                    callback();
                } else {
                    const file = fileListAdd_co.value[0].file;
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

const colorAddFormValidate = () => {
    return new Promise((resolve, reject) => {
        if (!newCoFormRef.value) {
            reject(new Error("newCoFormRef 未綁定"));
        } else {
            newCoFormRef.value.validate((isValid) => {
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

const toggleAdd = () => {
    // console.log(123);
    emit("toggle-add");
}

const toggleAddbtn = () => {
    emit('toggle-add-btn');
}

const newCoFormRef = ref();

defineExpose({
    newCoFormRef,
    colorAddFormValidate
})

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