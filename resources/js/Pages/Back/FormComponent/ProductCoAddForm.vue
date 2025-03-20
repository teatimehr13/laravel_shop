<template>
    <div v-show="newCoRowVisible" class="new_co_row">
        <el-form :model="newCoRowData" ref="newCoFormRef" :rules="rules">
            <el-row>
                <el-col class="custom-color_name">
                    <el-form-item prop="color_name">
                        <el-input v-model="newCoRowData.color_name" placeholder="輸入顏色名稱" size="small"
                            :disabled="isCombination"
                            @input="(val) => emit('update:newCoRowData', { ...props.newCoRowData, color_name: val })" />
                    </el-form-item>
                    <el-tooltip v-if="hasCombination" content="組合色已存在，無法再新增" placement="top">
                        <span>
                            <el-checkbox v-model="isCombination" :disabled="hasCombination">
                                組合色
                            </el-checkbox>
                        </span>
                    </el-tooltip>

                    <el-checkbox v-else v-model="isCombination" :disabled="hasCombination">
                        組合色
                    </el-checkbox>

                </el-col>
                <el-col>
                    <el-form-item prop="color_code">
                        <!-- <el-input v-model="newCoRowData.color_code" placeholder="輸入顏色" size="small"
                            :disabled="isCombination"
                            @input="(val) => emit('update:newCoRowData', { ...props.newCoRowData, color_code: val })" /> -->

                        <div class="demo-color-block">
                            <el-tooltip class="box-item" effect="dark" content="選取產品顏色" placement="top">
                                <span class="demonstration">
                                    <el-color-picker v-model="newCoRowData.color_code" :disabled="isCombination"
                                        @change="(val) => emit('update:newCoRowData', { ...props.newCoRowData, color_code: val })" />
                                </span>
                            </el-tooltip>
                        </div>
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
                                <el-tooltip class="box-item" effect="dark" content="上傳檔案" placement="top-start">
                                    <el-button>
                                        <el-icon>
                                            <Plus />
                                        </el-icon>
                                    </el-button>
                                </el-tooltip>
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
                            <el-button size="small" @click="toggleAddbtn">
                                取消
                            </el-button>
                        </div>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
    </div>

    <el-col style="margin-right: 10px; display: inline-block;">
        <div style="margin: 10px 0;">
            <el-button @click="toggleAddbtn" v-show="!newCoRowVisible">添加顏色</el-button>
        </div>
    </el-col>

    <el-dialog v-model="addCoVisible" style="width: max-content;" align-center>
        <img w-full :src="addCoImageUrl" alt="Preview Image" />
    </el-dialog>
</template>

<script setup>
import { computed, reactive, watch, ref, toRef } from "vue";

const props = defineProps({
    newCoRowData: Object,
    newCoRowVisible: Boolean,
    fileListAdd_co: Array,
    color_options_data: Array
})

// 使用 `computed` 來代理 `fileListAdd_co`
const fileListAdd_co = computed(() => props.fileListAdd_co);

const emit = defineEmits([
    //v-model綁定的話要加"update:"
    "update:fileListAdd_co",
    "update:newCoRowData",
    "toggle-add",
    "toggle-add-btn",
    "remove-verify"
    // "update:tempRow",
    // "toggle-edit",
    // "cancel-edit"
]);

const hasCombination = computed(() => {
    return props.color_options_data.some(option => option.color_name === "組合色");
});

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
    // isCombination.value = false;
}

const toggleAddbtn = () => {
    emit('toggle-add-btn');
    isCombination.value = false;
}

const newCoFormRef = ref();


const resetFields = () => {
    newCoFormRef.value.resetFields();
}

const isCombination = ref(false); // 是否為組合色

// const handleCombinationChange = (newVal) => {
//     emit("update:newCoRowData", {
//         ...props.newCoRowData,
//         color_name: newVal ? "組合色" : "",
//     });
//     emit('remove-verify');
// };

defineExpose({
    newCoFormRef,
    colorAddFormValidate,
    resetFields
})

watch(isCombination, (newVal) => {
    emit("update:newCoRowData", {
        ...props.newCoRowData,
        color_name: newVal ? "組合色" : "",
        color_code: newVal ? "combo" : "",
    });
    emit('remove-verify');
    console.log(props.newCoRowData);

});
</script>

<style scoped>
::v-deep(.el-row) {
    display: grid;
    grid-template-columns: 130px 130px 130px 200px 130px 200px;
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

.upload-placeholder {
    height: 50px;
    width: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.upload-placeholder .el-button:hover {
    cursor: pointer;
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


::v-deep(.custom-color_name) {
    display: flex;
    flex-direction: column;
    justify-content: center;
    transform: translateY(23px);
    margin-bottom: 15px;
}

.demo-color-block {
    display: flex;
    align-items: center;
    /* margin-bottom: 16px; */
}

.demo-color-block .demonstration {
    margin-right: 16px;
}
</style>