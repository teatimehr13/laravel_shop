<template>
    <el-form :model="tempRow" :rules="rules" ref="colorFormRef" class="colorForm">
        <el-table :data="sort_color_options">
            <el-table-column label="顏色名稱" width="130">
                <template #default="scope">
                    <div v-if="editingRow !== scope.row.id">
                        {{ scope.row.color_name }}
                    </div>
                    <el-form-item v-else prop="color_name">
                        <el-input v-model="tempRow.color_name" placeholder="輸入顏色" size="small" />
                    </el-form-item>
                </template>
            </el-table-column>

            <el-table-column label="顏色" width="130">
                <template #default="scope">
                    <div v-if="editingRow !== scope.row.id">
                        {{ scope.row.color_code }}
                    </div>
                    <el-form-item v-else prop="color_code">
                        <el-input v-model="tempRow.color_code" placeholder="輸入顏色" size="small" />
                    </el-form-item>
                </template>
            </el-table-column>

            <el-table-column label="價格" width="130">
                <template #default="scope">
                    <div v-if="editingRow !== scope.row.id">
                        {{ scope.row.price }}
                    </div>
                    <el-form-item v-else prop="price">
                        <el-input v-model.number="tempRow.price" placeholder="輸入價格" size="small" />
                    </el-form-item>
                </template>
            </el-table-column>

            <el-table-column label="顯示選項" width="200">
                <template #default="scope">
                    <div v-if="editingRow !== scope.row.id">
                        {{ scope.row.enable == '1' ? "顯示" : '隱藏' }}
                    </div>
                    <el-form-item v-else prop="enable">
                        <el-radio-group v-model="tempRow.enable">
                            <el-radio :value="1">顯示</el-radio>
                            <el-radio :value="0">隱藏</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </template>
            </el-table-column>

            <el-table-column label="圖片">
                <template #default="scope">
                    <div v-if="editingRow !== scope.row.id">
                        <img :src="scope.row.image" width="50px" @click="previewImage(scope.row.image)">
                    </div>
                    <div class="icon_gp" v-else>
                        <input ref="fileInput" type="file" class="hidden-input" @change="handleFileChange" />

                        <div v-if="fileList_co.length > 0" class="custom-file-wrapper">
                            <img :src="fileList_co[0].url" class="upload-img" />
                            <div class="file-actions">
                                <el-icon class="action-icon" @click="handlePreview(fileList_co[0])">
                                    <ZoomIn />
                                </el-icon>
                                <el-icon class="action-icon" @click="handleRemove(fileList_co[0])">
                                    <Delete />
                                </el-icon>
                                <el-icon class="action-icon plus-icon" @click="triggerUpload">
                                    <Plus />
                                </el-icon>
                            </div>
                        </div>

                        <div v-else-if="tempRow.image" class="custom-file-wrapper">
                            <img :src="tempRow.image" class="upload-img" />
                            <div class="file-actions">
                                <el-icon class="action-icon" @click="handlePreview({ url: tempRow.image })">
                                    <ZoomIn />
                                </el-icon>
                                <el-icon class="action-icon" @click="handleRemove">
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
                </template>
            </el-table-column>

            <el-table-column label="操作" width="250px">
                <template #default="scope">
                    <el-button :type="editingRow === scope.row.id ? 'primary' : ''" size="small"
                        @click="$emit('toggle-edit', scope.row)">
                        {{ editingRow === scope.row.id ? "儲存" : "編輯" }}
                    </el-button>
                    <el-button v-show="editingRow === scope.row.id" size="small" @click="$emit('cancel-edit')">
                        取消
                    </el-button>
                    <el-popconfirm title="確定移除此筆資料?" @confirm="$emit('del-co', scope.row.id)" :width="170"
                        :hide-after="100" v-model:visible="popconColfirmVisible[scope.row.id]">
                        <template #reference>
                            <el-button size="small" type="danger" v-show="editingRow !== scope.row.id">移除</el-button>
                        </template>
                        <template #actions="{ confirm, cancel }">
                            <el-button size="small" @click="cancel">沒有</el-button>
                            <el-button type="danger" size="small" @click="confirm">
                                是
                            </el-button>
                        </template>
                    </el-popconfirm>
                </template>
            </el-table-column>
        </el-table>
    </el-form>

    <el-dialog v-model="uploadCoVisible" style="width: max-content;">
        <img w-full :src="uploadCoImageUrl" alt="Preview Image" />
    </el-dialog>

    <!-- <el-dialog v-model="uploadCoAttVisible" title="上傳項目">
        <el-upload v-model:file-list="coAttList" :auto-upload="false"
            list-type="picture-card" :on-preview="coAttPreview" :on-remove="coAttRemove" multiple>
            <el-icon>
                <Plus />
            </el-icon>
        </el-upload>

        <el-dialog v-model="coAttVisible">
            <img w-full :src="coAttImageUrl" alt="Preview Image" />
        </el-dialog>
    </el-dialog>

    <el-dialog v-model="coImgVisible" title="附圖管理">
        <el-dialog v-model="coAttVisible">
            <img w-full :src="coAttImageUrl" alt="Preview Image" />
        </el-dialog>
    </el-dialog> -->


</template>

<script setup>
import { reactive, watch, ref, toRef, computed } from "vue";


//拿到父組件的資料
const props = defineProps({
    color_options_data: Object,
    fileList_co: Array,
    tempRow: Object,
    editingRow: Number,
});

const sort_color_options = computed(() => {
    return [...props.color_options_data].sort((a, b) => {
        return a.color_code === "combo" ? 1 : b.color_code === "combo" ? -1 : 0;
      });
})

const uploadCoImageUrl = ref('');
const uploadCoVisible = ref(false);

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
                if (fileList_co.value.length === 0) {
                    console.log(fileList_co.value);
                    callback();
                } else {
                    const file = fileList_co.value[0];
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

const colorFormRef = ref();

const previewImage = (image) => {
    uploadCoImageUrl.value = image;
    uploadCoVisible.value = true;
}

const handlePreview = (file) => {
    uploadCoVisible.value = true;
    uploadCoImageUrl.value = file.url;
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    console.log(file);

    if (file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            // fileList_co.value = [{ name: file.name, url: reader.result, file: file }];
            // console.log(fileList_co.value);
            emit("update:fileList_co", [{ name: file.name, url: reader.result, file: file }]);
        };
    }
};

const handleRemove = (file) => {
    // fileList_co.value = [];
    // tempRow.value.image = '';
    emit("update:fileList_co", []);
    emit("update:tempRow", { ...props.tempRow, image: "" });
};

const triggerUpload = () => {
    fileInput.value.click();
};

const fileInput = ref(null);


const colorFormValidate = () => {
    return new Promise((resolve, reject) => {
        if (!colorFormRef.value) {
            reject(new Error("colorFormRef 未綁定"));
        } else {
            colorFormRef.value.validate((isValid) => {
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

defineExpose({
    colorFormRef,
    colorFormValidate
});

const emit = defineEmits([
    "update:fileList_co",
    "update:tempRow",
    "toggle-edit",
    "cancel-edit",
    "del-co"
]);

const popconColfirmVisible = ref({});

// const uploadCoAttVisible = ref(false);
// // const color_name_title = ref('');
// const toggleCoAtt = (row) => {
//     uploadCoAttVisible.value = !uploadCoAttVisible.value;
//     // color_name_title.value = row.color_name;
// }


// const coAttList = ref([]);
// const coAttPreview = (file) => {
//     coAttVisible.value = true;
//     coAttImageUrl.value = file.url;
// };
// const coAttRemove = (uploadFile, uploadFiles) => {
//     console.log(uploadFile, uploadFiles);
//     //這裡的fileList會有多個
// }

// const coAttVisible = ref(false);
// const coAttImageUrl = ref('');

// const coImgVisible = ref(false);
// const toggleCoImg = (row) => {
//     coImgVisible.value = !coImgVisible.value;
//     // color_name_title.value = row.color_name;
//     //這裡在點下時，應要去後端撈相關圖片回來擺上
    
// }

</script>

<style scoped>
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

::v-deep(.colorForm .el-form-item__content) {
    height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
}

::v-deep(.colorForm .el-form-item__error) {
    top: 70%;
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
</style>