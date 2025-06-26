<template>
    <Header />
    <main>
        <div class="container">
            <p style="margin-bottom: 32px;">
                <el-button link @click="goBack">
                    <el-icon>
                        <ArrowLeft />
                    </el-icon>
                    回上一頁
                </el-button>
                <hr style="margin-top: 8px;">
            </p>

            <el-form :model="product" label-width="300px" class="demo-ruleForm" :rules="formRules"
                ref="internalFormRef">
                <el-form-item label="產品名稱" :label-position="labelPosition" prop="name">
                    <el-input v-model="product.name" style="max-width: 300px;" />
                </el-form-item>

                <el-form-item label="產品title" :label-position="labelPosition" prop="title">
                    <el-input v-model="product.title" style="max-width: 300px;" />
                </el-form-item>

                <el-form-item label="價格" :label-position="labelPosition" prop="title">
                    <el-input v-model="product.price" style="max-width: 300px;" />
                </el-form-item>


                <el-form-item label="子類別" :label-position="labelPosition" prop="subcategory_id">
                    <el-select v-model.number="product.subcategory_id" :teleported="false" style="max-width: 300px;">
                        <el-option v-for="subcategory in props.subcategories" :key="subcategory.id"
                            :label="subcategory.name" :value="subcategory.id">
                        </el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="操作" :label-position="labelPosition" prop="published_status">
                    <el-radio-group v-model="product['published_status']">
                        <el-radio :value="1">上架</el-radio>
                        <el-radio :value="0">下架</el-radio>
                    </el-radio-group>
                </el-form-item>

                <el-form-item label="圖片" :label-position="labelPosition" prop="image">
                    <el-upload :file-list="fileList" class="upload-demo" action="" :on-preview="handlePreview"
                        :on-remove="handleRemove" list-type="picture" :auto-upload="false" :limit="1"
                        :on-exceed="handleExceed" ref="upload" popper-class="no-transition" :on-change="handleOnChange">
                        <el-button type="">選擇檔案</el-button>
                    </el-upload>

                    <el-dialog v-model="dialogVisible">
                        <img w-full :src="dialogImageUrl" alt="Preview Image" style="margin: auto;" />
                    </el-dialog>
                </el-form-item>
                <el-form-item label="產品描述" :label-position="labelPosition" prop="description" class="quill-item">
                    <QuillEditor v-model:content="product.description" content-type="html" theme="snow" ref="quillRef"
                        style="min-height: 200px;" />
                </el-form-item>

                <hr style="margin: 10px auto;">

                <!-- <div style="margin: 10px auto;">
                    <span>特惠欄位</span>
                    <el-tooltip effect="dark" :content="showSpecial ? '點擊隱藏欄位' : '點擊顯示欄位'" placement="top">
                        <el-link @click="showSpecial = !showSpecial" type="primary" :underline="false">
                            {{ showSpecial ? '[顯示]' : '[隱藏]' }}
                        </el-link>
                    </el-tooltip>
                </div> -->

                <el-form-item label="特惠訊息" :label-position="labelPosition" prop="special_message" class="quill-item">
                    <QuillEditor v-model:content="product.special_message" content-type="html" theme="snow"
                        ref="specialQuillRef" style="min-height: 200px;" />
                </el-form-item>

                <el-form-item label="特惠時間" :label-position="labelPosition" prop="special_start_at">
                    <el-date-picker v-model="product.special_start_at" type="datetime" placeholder="開始時間"
                        :teleported="false" style="margin-right:10px" />
                </el-form-item>
                <el-form-item label="" :label-position="labelPosition" prop="special_end_at">
                    <el-date-picker v-model="product.special_end_at" type="datetime" placeholder="結束時間"
                        :teleported="false" />
                </el-form-item>

            </el-form>


            <el-button type="primary" @click="onSubmitEdit">
                提交
            </el-button>
        </div>
    </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import Header from '@/Components/Back/Header.vue';
import { genFileId } from 'element-plus';
import DOMPurify from 'dompurify';

const props = defineProps({
    product: Object,
    subcategories: Array
})

console.log(props);
const product = reactive({ ...props.product });

const labelPosition = ref('top');

const fileList = ref([]);
fileList.value = product.image ? [
    {
        name: product.name,
        url: product.image,
        uid: genFileId(),
    },
] : [];

const dialogImageUrl = ref('');
const dialogVisible = ref(false);

const upload = ref([]);

//點擊待上傳的圖片
const handlePreview = (uploadFile) => {
    console.log(uploadFile)
    dialogImageUrl.value = uploadFile.url
    dialogVisible.value = true
}

const handleOnChange = (file) => {
    // console.log(file);
    fileList.value = [{
        name: file.name,
        url: file.url,
        uid: file.uid,
        raw: file.raw,
    }];
}

const handleRemove = (uploadFile, uploadFiles) => {
    fileList.value = [];
}

//限制僅上傳一筆
const handleExceed = (files) => {
    upload.value.clearFiles();
    const file = files[0];
    file.uid = genFileId(); // 生成唯一的 UID
    upload.value.handleStart(file)
};


const validateEndDate = (rule, value, callback) => {
    if (!value || !product.special_start_at) {
        return callback(); // 若空，交由後端驗證
    }

    const start = new Date(product.special_start_at);
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
        { required: true, message: "產品title為必填項", trigger: "submit" },
    ],
    price: [
        { required: true, message: "售價必填項", trigger: "submit" },
    ],
    subcategory_id: [
        { required: true, message: "子類別為必填項", trigger: "blur" },
    ],
    'published_status': [
        { required: true, message: "請選擇顯示或隱藏", trigger: "submit" }
    ],
    image: [
        {
            validator: (rule, value, callback) => {
                console.log(fileList.value);

                if (!fileList.value || fileList.value.length === 0) {
                    return callback();
                }

                const file = fileList.value[0]?.raw;
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
        // {
        //     required: false,
        //     message: '請選擇開始時間',
        //     validator: (_rule, value, callback) => {
        //         if (value === null || value instanceof Date) {
        //             callback();
        //         } else {
        //             callback(new Error('請選擇有效的日期'));
        //         }
        //     },
        //     trigger: 'change',
        // },
        { type: 'date', required: false, message: '請選擇開始時間', trigger: 'change' },
    ],
    special_end_at: [
        { type: 'date', required: false, message: '請選擇結束時間', trigger: 'change' },
        { validator: validateEndDate, trigger: 'change' }
    ]
});

const internalFormRef = ref(null); // 引用 el-form

const formValidate = async () => {
    if (!internalFormRef.value) {
        throw new Error("internalFormRef 未綁定");
    }

    return await internalFormRef.value.validate();
};

let formData = new FormData();

//提交
const formBeforSubmit = () => {
    console.log(product);
    formData = new FormData();
    const safeDescription = DOMPurify.sanitize(product.description);
    const safeSpecialMsg = DOMPurify.sanitize(product.special_message);

    console.log(safeDescription);


    formData.append('name', product.name);
    formData.append('title', product.title);
    formData.append('price', product.price);
    formData.append('subcategory_id', product.subcategory_id); //待修正
    formData.append('published_status', product.published_status);
    // formData.append('color_codes', product.color_codes);
    formData.append('description', safeDescription);

    formData.append('special_message', safeSpecialMsg);

    // if (product.special_start_at)
    formData.append('special_start_at', formatDate(product.special_start_at) || '');
    formData.append('special_end_at', formatDate(product.special_end_at) || '');

    for (const [key, value] of formData.entries()) {
        console.log(`${key}:`, value);
    }

    // return
    // console.log(fileList.value);


    if (fileList.value.length > 0) {
        const file = fileList.value[0];
        //有傳新圖片時
        if (file.raw) {
            formData.append('image', file.raw);
        }

        //沒傳圖片，但也沒刪
        console.log('do not delete_image');

    } else {
        //刪掉現有的圖片
        formData.append('delete_image', true);
        console.log('delete image');
    }

    // console.log(fileList.value);

    return formData;
}

//編輯
const onSubmitEdit = async () => {
    try {
        await formValidate();
        await formBeforSubmit();
        // return

        // // console.log(formData);
        // // for (const [key, value] of formData.entries()) {
        // // console.log(`${key}:`, value);
        // // }

        // 模擬 PATCH 方法
        formData.append('_method', 'PATCH');

        const response = await axios.post(`/back/products/${product.id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // console.log('stores.data', stores.data);
        console.log('提交成功:', response.data);

        // 從後端獲取更新後的數據
        const updatedProduct = response.data;
        // console.log(updatedStore);
        // console.log(product.value);

        if (updatedProduct) {
            // 更新目標行
            // const index = product.findIndex(product => product.id === updatedProduct.id);
            // if (index !== -1) {
            //     // product.value[index] = updatedProduct; // 更新數據
            //     Object.assign(product[index], updatedProduct); // 更新原始數據
            // }

            Object.assign(product, updatedProduct);
            // closePopover(popForm.id);
            showMessage("success", "保存成功");
            return
        }

    } catch (error) {
        console.error('提交失败:', error);
        showMessage("error", "保存失敗");
    }
};

function formatDate(input) {
    if (!input) return null;

    let dateObj;

    // 檢查是否已是 Date 物件
    if (input instanceof Date) {
        dateObj = input;
    } else if (typeof input === 'string') {
        // 嘗試將字串轉為 Date
        dateObj = new Date(input);
        if (isNaN(dateObj)) {
            console.warn('formatDate 錯誤：無效的日期字串', input);
            return null;
        }
    } else {
        console.warn('formatDate 錯誤：不支援的型別', input);
        return null;
    }

    const yyyy = dateObj.getFullYear();
    const mm = String(dateObj.getMonth() + 1).padStart(2, '0'); // 月份從 0 開始
    const dd = String(dateObj.getDate()).padStart(2, '0');
    const hh = String(dateObj.getHours()).padStart(2, '0');
    const min = String(dateObj.getMinutes()).padStart(2, '0');
    const ss = String(dateObj.getSeconds()).padStart(2, '0');

    return `${yyyy}-${mm}-${dd} ${hh}:${min}:${ss}`;
}

//通知
const showMessage = (type, title) => {
    ElNotification({
        type, // "success" 或 "error"
        title,
        position: 'bottom-left',
    });
};

function goBack() {
    window.history.back();
}

</script>

<style scoped>
.container {
    padding: 2rem 1.5rem;
    max-width: 1000px;
    margin: auto;
}

::v-deep(.quill-item .el-form-item__content) {
    display: block;
}

::v-deep(.el-input__inner:focus) {
    outline: none;
    box-shadow: none;
}
</style>