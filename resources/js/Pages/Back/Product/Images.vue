<template>
    <div v-for="(val, key) in sort_product_options" :key="val.id" class="images-outside">
        <h1>{{ val.color_name }}</h1>

        <div class="images scrollbar-flex-content">
            <div v-for="(img_val, img_key) in val.product_images" :key="img_val.uid || img_val.id"
                class="image-wrapper">
                <div class="image-container" :class="{ 'image-deleted': img_val._delete }">
                    <div class="demo-image__preview">
                        <el-image v-if="val.product_images.length" style="width: 100px; height: 100px"
                            :src="img_val.image" :preview-src-list="getPreviewList(val.product_images)" show-progress
                            fit="cover" :initial-index="img_key" />
                    </div>
                    <!-- 浮水印，只有當 `_delete: true` 才顯示 -->
                    <div v-if="img_val._delete" class="watermark">
                        <span>刪除</span>
                    </div>
                </div>

                <el-input v-model="img_val.alt_text" @change="img_val.alt_text = normalizeText(img_val.alt_text)"
                    style="width: 200px" placeholder="Alt Text" />

                <!-- 刪除 & 還原按鈕 -->
                <div class="toggle_btn">
                    <el-button v-if="img_val.id === null" @click="removeImage(val.id, null, img_val.uid)">刪除</el-button>
                    <el-button v-else-if="!img_val._delete" @click="removeImage(val.id, img_val.id)">標記刪除</el-button>
                    <el-button v-else @click="restoreImage(val.id, img_val.id)" type="danger" plain
                        class="del_btn">還原</el-button>
                </div>
            </div>

            <input type="file" multiple class="hidden-input" :ref="el => fileInputs[val.id] = el"
                @change="handleFileUpload($event, val.id)" />

            <div class="upload-placeholder" @click="triggerUpload(val.id)">
                <el-tooltip class="box-item" effect="dark" content="新增檔案" placement="top">
                    <el-button>
                        <el-icon>
                            <Plus />
                        </el-icon>
                    </el-button>
                </el-tooltip>
            </div>
        </div>
    </div>

    <div>
        <el-button type="danger" @click="submitData">更新</el-button>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    productImages: Array,
    productId: String
})

console.log(props.productImages);
const productImages = ref(props.productImages);

let original_productImages = JSON.parse(JSON.stringify(props.productImages));

const sort_product_options = computed(() => {
    return [...productImages.value].sort((a, b) => {
        return a.color_code === "combo" ? 1 : b.color_code === "combo" ? -1 : 0;
    });
})

const fileInputs = ref([]);
const triggerUpload = (optionId) => {
    fileInputs.value[optionId].click();
};

// 取得圖片預覽列表
const getPreviewList = (images) => {
    return images.map((img) => img.image);
};

const removeImage = (optionId, imageId, uid = null) => {
    const option = sort_product_options.value.find(opt => opt.id === optionId);
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
    const option = sort_product_options.value.find(opt => opt.id === optionId);
    if (!option) return;

    option.product_images.forEach(img => {
        if (img.id === imageId) {
            img._delete = false;  // 取消刪除
        }
    });
};

// 上傳圖片
const handleFileUpload = (event, optionId) => {
    const files = event.target.files;

    if (!files.length) return;
    const option = sort_product_options.value.find(opt => opt.id === optionId);
    console.log(option);

    if (!option) return;
    console.log(option);

    Array.from(files).forEach(file => {
        const newImage = {
            id: null,  //`null` 表示這是新上傳的圖片
            uid: Date.now() + Math.random(), //為每張圖片生成唯一 ID
            product_id: option.product_id,
            image: URL.createObjectURL(file), //預覽圖片
            alt_text: "",
            is_combination: option.color_name === "組合色" ? 1 : 0,
            file: file, //用於上傳,
        };
        option.product_images.push(newImage);
    });
    event.target.value = '';
};

//整理成要上傳的資料
const getUpdatedProductOptions = () => {
    return productImages.value.map(option => {
        const originalProductImages = original_productImages.find(o => o.id === option.id);

        return {
            po_id: option.id,
            product_images: option.product_images
                .filter(img => {
                    // 找出原始的圖片
                    const originalImg = originalProductImages?.product_images?.find(oImg => oImg.id === img.id);

                    // 檢查 alt_text 是否變更
                    const altTextChanged = originalImg && originalImg.alt_text?.trim() !== img.alt_text?.trim();

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


//上傳試測
const submitData = () => {
    const formData = new FormData();
    // console.log(product_options);
    // console.log(props.productId);
    // console.log(original_product_options);

    const updatedOptions = getUpdatedProductOptions();
    console.log(updatedOptions);

    let product_id = props.productId;
    // console.log(product_id);

    // return
    // return

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

    for (const [key, value] of formData.entries()) {
        console.log(`${key}:`, value);
    }

    // return

    //檢查formData有沒有東西，沒有的話不上傳
    if (formData.entries().next().done) {
        return;
    }

    formData.append("product_id", product_id);
    // loading_status.value = true;

    // ajax 的方法
    axios.post("/back/products/updateProductImages", formData, {
        headers: { "Content-Type": "multipart/form-data" }
    }).then(response => {
        console.log(response.data);
        // Object.assign(productImages.value, response.data.updated_product_options || []);
        productImages.value = response.data.updated_product_options || [];
        original_productImages = JSON.parse(JSON.stringify(response.data.updated_product_options) || []);
        if (response.data) {
            showMessage('success', '更新成功');
        }
        // loading_status.value = false;
    });


    // router.post(`/back/products/updateProductImages`, formData, {
    //     preserveScroll: true, // 保持捲動位置
    //     preserveState: true,  // 不重新 reload 畫面，保留當前頁面資料
    //     replace: true,        // 不新增一條新的歷史紀錄
    //     onSuccess: () => {
    //         router.visit(`/back/products/${product_id}/images`, {
    //             preserveScroll: true
    //         });
    //         showMessage('success', '更新成功')
    //     },
    //     onError: (errors) => {
    //         console.log('新失敗', errors)
    //     }
    // });

}

function normalizeText(value) {
    const trimmed = (value ?? '').trim();
    return trimmed === '' ? null : trimmed;
}

const showMessage = (type, title) => {
    ElNotification({
        type, // "success" 或 "error"
        title,
        position: 'bottom-left',
    });
};
</script>

<style scoped>
.images-outside {
    border-bottom: 1px solid #ededed;
    padding-bottom: 5px;
}

.scrollbar-flex-content {
    display: flex;
}

.images {
    padding-bottom: 5px;
}

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
::v-deep(.image-deleted img) {
    filter: grayscale(100%) opacity(50%);
}


.image-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    margin: 10px;
    padding: 5px;
}

/* 浮水印 */
::v-deep(.watermark) {
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

::v-deep(.watermark > .el-icon) {
    vertical-align: middle;
}

.upload-placeholder {
    /* height: 50px; */
    width: 200px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    margin: 10px;
    padding: 5px;
}

.upload-placeholder .el-button {
    width: 100px;
    height: 100px;
    margin: auto;
}

.upload-placeholder .el-button:hover {
    cursor: pointer;
}

.hidden-input {
    display: none;
}


.demo-image__error .image-slot {
    font-size: 30px;
}

.demo-image__error .image-slot .el-icon {
    font-size: 30px;
}

.demo-image__error .el-image {
    width: 100%;
    height: 200px;
}

::v-deep(.el-input__inner:focus) {
    outline: none;
    box-shadow: none;
}

h1 {
    font-size: 1.5rem;
    margin: 10px;
    /* color:#172B4D; */
}

::v-deep(.toggle_btn > button) {
    width: 200px;
}

::v-deep(.toggle_btn > button:not(.del_btn)) {
    color: #172B4D;
    background: #091E420F;
}
</style>