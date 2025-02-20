<template>
    <el-button type="info" @click="toggleCoImg()">
        附圖管理
    </el-button>

    <el-dialog v-model="uploadCoAttVisible" title="上傳項目">
        <el-upload v-model:file-list="coAttList" :auto-upload="false" list-type="picture-card"
            :on-preview="coAttPreview" :on-remove="coAttRemove" multiple>
            <el-icon>
                <Plus />
            </el-icon>
        </el-upload>

        <el-dialog v-model="coAttVisible">
            <img w-full :src="coAttImageUrl" alt="Preview Image" />
        </el-dialog>
    </el-dialog>

    <el-dialog v-model="coImgVisible" title="產品附圖管理" style="min-width: 600px; width: 1100px; max-width: 1500px;">
        <div>
            <div v-for="(product_option, idx) in product_options" :key="product_option.id">
                <h1>{{ product_option.color_code }}</h1>

                <div class="images" style="display: flex; flex-wrap: wrap;">

                    <div v-for="image in product_option.product_images" :key="image.uid || image.id"
                        class="image-wrapper">
                        <!-- 圖片 -->
                        <div class="image-container" :class="{ 'image-deleted': image._delete }">
                            <img :src="image.image" class="image-item" />
                            <!-- 浮水印，只有當 `_delete: true` 才顯示 -->
                            <div v-if="image._delete" class="watermark">刪除</div>
                        </div>

                        <!-- Alt Text -->
                        <input type="text" v-model="image.alt_text" placeholder="Alt Text" />

                        <!-- 刪除 & 還原按鈕 -->
                        <button v-if="image.id === null"
                            @click="removeImage(product_option.id, null, image.uid)">刪除</button>
                        <button v-else-if="!image._delete"
                            @click="removeImage(product_option.id, image.id)">標記刪除</button>
                        <button v-else @click="restoreImage(product_option.id, image.id)">還原</button>
                    </div>

                    <input type="file" multiple class="hidden-input" :ref="el => fileInputs[product_option.id] = el"
                        @change="handleFileUpload($event, product_option.id)" />

                    <div class="upload-placeholder" @click="triggerUpload(product_option.id)">
                        <el-tooltip class="box-item" effect="dark" content="新增檔案" placement="top-start">
                            <el-button>
                                <el-icon>
                                    <Plus />
                                </el-icon>
                            </el-button>
                        </el-tooltip>
                    </div>
                </div>
            </div>
        </div>

        <button @click="submitData">更新</button>

    </el-dialog>
</template>

<script setup>
import { ref, reactive, computed, watch } from "vue";
import axios from "axios";


const props = defineProps({
    colorOptions: Array,
    productId: Number
})


const uploadCoAttVisible = ref(false);
// const color_name_title = ref('');
const toggleCoAtt = (row) => {
    uploadCoAttVisible.value = !uploadCoAttVisible.value;
    // color_name_title.value = row.color_name;
}


const coAttList = ref([]);
const coAttPreview = (file) => {
    coAttVisible.value = true;
    coAttImageUrl.value = file.url;
};
const coAttRemove = (uploadFile, uploadFiles) => {
    console.log(uploadFile, uploadFiles);
    //這裡的fileList會有多個
}

const coAttVisible = ref(false);
const coAttImageUrl = ref('');

const coImgVisible = ref(false);
const toggleCoImg = async () => {
    await productImages();
    coImgVisible.value = !coImgVisible.value;
}

const product_options = reactive([]);

//原始數據，用來比對更改過的數據
let original_product_options = [];



const productImages = async () => {
    let obj = {
        pr_id: props.productId
    }

    const response = await axios.post('/back/products/product_images', obj, {
    });

    product_options.length = 0;
    Object.assign(product_options, response.data || []);
    original_product_options = JSON.parse(JSON.stringify(response.data));

    console.log(product_options);
}

// 上傳圖片
const handleFileUpload = (event, optionId) => {
    const files = event.target.files;

    if (!files.length) return;
    const option = product_options.find(opt => opt.id === optionId);
    console.log(option);

    if (!option) return;

    // 加入新的圖片
    // option.product_images.push({
    //     id: null,  // 新增的圖片沒有 ID
    //     product_id: option.product_id,
    //     image: URL.createObjectURL(file), // 預覽用
    //     alt_text: "",
    //     is_combination: 0,
    //     file: file, // 用於上傳
    //     uid: Date.now() + Math.random()
    // });

    // console.log(Array.from(files));

    Array.from(files).forEach(file => {
        const newImage = {
            id: null,  //`null` 表示這是新上傳的圖片
            uid: Date.now() + Math.random(), //為每張圖片生成唯一 ID
            product_id: option.product_id,
            image: URL.createObjectURL(file), //預覽圖片
            alt_text: "",
            is_combination: 0,
            file: file, //用於上傳
        };
        option.product_images.push(newImage);
    });
    event.target.value = '';
};

const removeImage = (optionId, imageId, uid = null) => {
    const option = product_options.find(opt => opt.id === optionId);
    if (!option) return;

    if (imageId === null && uid) {
        // 如果是新上傳的圖片（id: null），根據 `uid` 找到對應的圖片來刪除
        const index = option.product_images.findIndex(img => img.id === null && img.uid === uid);
        if (index !== -1) {
            option.product_images.splice(index, 1);
        }
    } else {
        // 如果是已存在的圖片，標記 `_delete: true`
        option.product_images.forEach(img => {
            if (img.id === imageId) {
                img._delete = true;
            }
        });
    }
};

const restoreImage = (optionId, imageId) => {
    const option = product_options.find(opt => opt.id === optionId);
    if (!option) return;

    option.product_images.forEach(img => {
        if (img.id === imageId) {
            img._delete = false;  // 取消刪除
        }
    });
};

const fileInputs = ref([]);

const triggerUpload = (optionId) => {
    fileInputs.value[optionId].click();
};

const test_form = {
    "product_options": [
        {
            "po_id": '5',
            "product_images": [
                {
                    "alt_text": "cup2",
                    "is_combination": 0,
                    "id": 22,
                    "image": "cmMtdXBsb2FkLTE3Mzk1MTQ2NzgxMDktMTA0/wands12.jpg",
                    "_delete": 0
                },
                {
                    "alt_text": "devil",
                    "is_combination": 0,
                    "id": null,
                    "image": "cmMtdXBsb2FkLTE3Mzk1MTQ2NzgxMDktMTAy/rws_tarot_15_devil.jpg"
                }
            ]
        },
        {
            "po_id": 9,
            "product_images": [
                {
                    "alt_text": "swords1",
                    "is_combination": 1,
                    "image": "cmMtdXBsb2FkLTE3Mzk1MTQ2NzgxMDktMTAw/wands13.jpg",
                    "id": 25
                }
            ]
        }
    ]
};


//上傳試測
const submitData = () => {
    const formData = new FormData();
    // console.log(product_options);
    // console.log(props.productId);
    // console.log(original_product_options);

    const updatedOptions = getUpdatedProductOptions();
    console.log(updatedOptions);
    let product_id = props.productId;

    updatedOptions.forEach((option, optionIndex) => {
        formData.append(`product_options[${optionIndex}][po_id]`, option.po_id);

        option.product_images.forEach((image, imageIndex) => {
            formData.append(`product_options[${optionIndex}][product_images][${imageIndex}][id]`, image.id ?? '');
            formData.append(`product_options[${optionIndex}][product_images][${imageIndex}][alt_text]`, image.alt_text);
            formData.append(`product_options[${optionIndex}][product_images][${imageIndex}][is_combination]`, image.is_combination);
            formData.append(`product_options[${optionIndex}][product_images][${imageIndex}][_delete]`, image._delete ? 1 : 0);

            if (image.id === null && image.image instanceof File) {
                formData.append(`product_options[${optionIndex}][product_images][${imageIndex}][image]`, image.image);
            }
        });
    });

    // for (const [key, value] of formData.entries()) {
    //     console.log(`${key}:`, value);
    // }

    //檢查formData有沒有東西，沒有的話不上傳
    if (formData.entries().next().done) {
        return;
    }

    formData.append("product_id", product_id);

    axios.post("/back/products/updateProductImages", formData, {
        headers: { "Content-Type": "multipart/form-data" }
    }).then(response => {
        console.log(response.data);
        Object.assign(product_options, response.data.updated_product_options || []);
        original_product_options = JSON.parse(JSON.stringify(response.data.updated_product_options) || []);
        if(response.data){
            showMessage('success', '更新成功');
        }
    });
}

//整理成要上傳的資料
const getUpdatedProductOptions = () => {
    return product_options.map(option => {
        const originalOption = original_product_options.find(o => o.id === option.id);

        return {
            po_id: option.id,
            product_images: option.product_images
                .filter(img => {
                    // 找出原始的圖片
                    const originalImg = originalOption?.product_images?.find(oImg => oImg.id === img.id);

                    // 檢查 alt_text 是否變更
                    const altTextChanged = originalImg && originalImg.alt_text !== img.alt_text;

                    // 檢查是否標記刪除
                    const isDeleted = img._delete;

                    // 檢查是否是新上傳的圖片
                    const isNew = img.id === null;

                    return altTextChanged || isDeleted || isNew;
                })
                .map(img => ({
                    //重新整理數據結構
                    id: img.id ?? null,
                    alt_text: img.alt_text,
                    is_combination: img.is_combination,
                    _delete: img._delete ?? false,
                    image: img.id ? img.image : img.file, // 新圖片才帶 `file`
                }))
        };
    }).filter(option => option.product_images.length > 0); // 過濾掉沒有改變的 product_options
}

const showMessage = (type, title) => {
    ElNotification({
        type, // "success" 或 "error"
        title,
        position: 'bottom-left',
    });
};

</script>

<style>
.image-item {
    max-width: 100px;
}

.image-container {
    position: relative;
    display: inline-block;
}

.image-item {
    width: 100px;
    height: auto;
    transition: filter 0.3s ease, opacity 0.3s ease;
}

/* 當 _delete: true 時，讓圖片變成灰階 & 透明度變低 */
.image-deleted img {
    filter: grayscale(100%) opacity(50%);
}

/* 浮水印 */
.watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(108, 108, 108, 0.7);
    color: white;
    padding: 5px 10px;
    font-size: 14px;
    font-weight: bold;
    border-radius: 5px;
    width: max-content;
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

.hidden-input {
    display: none;
}
</style>