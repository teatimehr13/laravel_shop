<template>
    <BackendLayout>
        <template #switch>
            <div>
                <div>
                    <el-button type="" @click="dialogToggle" style="margin: 10px 0;">新增產品</el-button>
                </div>

                <el-table :data="products" style="width: 100%; height: calc(100vh - 250px)"
                    :row-class-name="getRowClass" border>
                    <el-table-column width="220" :fixed="isFixedStore ? 'left' : false" prop="name">
                        <template #header>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span>產品名稱</span>
                                <el-tooltip content="固定/取消固定門市列">
                                    <el-checkbox v-model="isFixedStore"></el-checkbox>
                                </el-tooltip>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="title" label="產品title" width="120" />

                    <el-table-column label="圖片" width="120">
                        <template #default="scope">
                            <img v-if="scope.row.image" :src="scope.row.image" alt="店面圖片"
                                style="max-width: 75px; border-radius: 4px" />
                        </template>
                    </el-table-column>
                    <el-table-column prop="subcategory.name" label="類別" width="120" />
                    <el-table-column prop="published_status" label="狀態" width="100">
                        <template #default="scope">
                            <span>
                                {{ scope.row.published_status == 1 ? '上架' : '下架' }}
                            </span>
                        </template>
                    </el-table-column>

                    <el-table-column prop="color_codes" label="顏色" width="200">
                        <template #default="scope">
                            <span v-for="(color_name, index) in scope.row.color_codes" :key="index">
                                {{ color_name }}<span v-if="index < scope.row.color_codes.length - 1">、 </span>
                            </span>
                        </template>
                    </el-table-column>

                    <el-table-column width="300" :fixed="isFixed ? 'right' : false">
                        <template #header>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span>操作</span>
                                <el-tooltip content="固定/取消固定操作列">
                                    <el-checkbox v-model="isFixed"></el-checkbox>
                                </el-tooltip>
                            </div>
                        </template>

                        <template #default="scope">
                            <el-button @click="dialogColorToggle(scope.row)" size="small" type="info">
                                顏色管理
                            </el-button>

                            <el-popover :placement="popoverPlacement" trigger="click" :width="350"
                                v-model:visible="popoverVisible[scope.row.id]" popper-class="custom-scrollbar"
                                :hide-after="0" :show-after="100" @show="handlePopoverShow(scope.row.id)"
                                :popper-style="popoverStyle" @before-leave="enableScroll" :offset="offSet"
                                ref="popoverRef">
                                <template #reference>
                                    <el-button size="small" @click="openEditPopover(scope.row)"
                                        :ref="el => (triggerRefs[scope.row.id] = el)"
                                        :class="{ activeButton: activeRow === scope.row.id }">編輯</el-button>
                                </template>

                                <ProductForm v-model:file-list="fileList" v-model:form-data="popForm"
                                    v-model:uploadList="uploadList" @submit="onSubmitEdit" @uploadList="emitUploadList"
                                    ref="formRef2" />

                                <el-form-item>
                                    <el-button type="primary" @click="onSubmitEdit(scope.row.id)">儲存</el-button>
                                    <el-button @click="closePopover(scope.row.id)">關閉</el-button>
                                </el-form-item>
                            </el-popover>

                            <el-popconfirm title="確定移除此筆資料?" @confirm="onSubmitDel(scope.row.id)" :width="170"
                                :hide-after="100" v-model:visible="popconfirmVisible[scope.row.id]">
                                <template #reference>
                                    <el-button size="small" type="danger">移除</el-button>
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
            </div>

            <el-dialog v-model="dialogFormToggle" title="新增" width="400px">
                <ProductForm v-model:form-data="popForm" v-model:file-list="fileList" v-model:upload-list="uploadList"
                    @uploadList="emitUploadList" ref="formRef" mode="add" />
                <template #footer>
                    <el-button type="primary" @click="onSubmitAdd">提交</el-button>
                    <el-button @click="dialogToggle">取消</el-button>
                </template>
            </el-dialog>

            <!-- 增刪改查product color -->
            <el-dialog v-model="dialogColorVisible" :title="product_dialog_title" width="1000">
                <ProductCoEditForm v-model:color_options_data="color_options_data" v-model:fileList_co="fileList_co"
                    v-model:tempRow="tempRow" :editingRow="editingRow" @toggle-edit="toggleEdit"
                    @cancel-edit="cancelEdit" @del-co="chkCoImgs" ref="colorFormRef" />

                <ProductCoAddForm ref="newCoFormRef" v-model:newCoRowData="newCoRowData"
                    v-model:newCoRowVisible="newCoRowVisible" v-model:fileListAdd_co="fileListAdd_co"
                    @toggle-add="toggleAdd" @toggle-add-btn="toggleAddBtn" />

                <ProductCoAttPicForm :colorOptions="color_options_data" :productId="productId"
                    v-model:dialogColorVisible="dialogColorVisible" />
            </el-dialog>
        </template>
    </BackendLayout>
</template>

<script setup>

import { ref, reactive, onMounted, computed, nextTick, watch } from "vue";
import axios from "axios";
import BackendLayout from '@/Layouts/BackendLayout.vue';
import ProductForm from "./FormComponent/ProductForm.vue";
import ProductCoEditForm from "./FormComponent/ProductCoEditForm.vue";
import ProductCoAddForm from "./FormComponent/ProductCoAddForm.vue";
import ProductCoAttPicForm from "./FormComponent/ProductCoAttPicForm.vue";
import debounce from "lodash.debounce";
import { genFileId } from 'element-plus'



const props = defineProps({
    products: {
        type: Object,
        required: true
    },
    categories: {
        type: Object,
        required: true
    }
});

// console.log(props);

const products = ref([...props.products]);
const categories = ref([...props.categories]);
console.log(categories.value);

console.log(products.value);

const emitUploadList = (data) => {
    uploadList.value = data;
}

// 篩選select
const storeTypeArr = [
    { name: "全部類型", value: "" },
    { name: "直營", value: "0" },
    { name: "特約展售", value: "1" },
    { name: "授權經銷商", value: "2" },
];

// 表單數據
const popForm = reactive({
    name: '',
    title: '',
    subcategory_id: '',
    category_id: '',
    published_status: '',
    color_codes: [],
    subcategories: [],
    categories: categories.value,
})

// 初始數據
const stores = reactive({
    data: [],
    current_page: 1,
    last_page: null,
});

const loading = ref(false);
const noMoreData = ref(false);
const storeType = ref("");
const addressFilter = ref("");
const isFixed = ref(false); // 默認不固定
const isFixedStore = ref(false); // 默認不固定

// 加載更多數據
const loadMore = debounce(async () => {
    if (loading.value || noMoreData.value) return;
    loading.value = true;

    try {
        const response = await axios.get("/back/stores", {
            params: {
                page: stores.current_page,
                store_type: storeType.value || null,
                address: addressFilter.value || null,
            },
        });

        // console.log(response.data);

        const newData = response.data.data; // 新數據 
        const lastPage = response.data.last_page; // 總頁數 ex:11

        // 如果有數據，追加到 stores.data
        if (newData.length > 0) {
            stores.data.push(...newData);
            stores.current_page += 1; // 頁碼 +1
            stores.last_page = lastPage;

            // 如果當前頁碼超過最後一頁，標記沒有更多數據
            if (stores.current_page > stores.last_page) {
                noMoreData.value = true;
            }
        } else {
            noMoreData.value = true;
        }

    } catch (error) {
        console.error("Error loading more data:", error);
    } finally {
        loading.value = false;
    }
}, 300);

// 篩選條件發生變化時重新加載
const applyFilters = debounce(() => {
    stores.data = [];
    stores.current_page = 1;
    noMoreData.value = false;
    loadMore();
}, 500);

//格式化營業時間顯示
const formattedOpeningHours = computed(() => {
    if (stores.data) {
        return stores.data.map((store) =>
            store.opening_hours
                ? store.opening_hours
                    .split("\n") // 按 \n 分割
                    .map((line) => line.trim()) // 去除多餘空白
                    .filter((line) => line !== "") // 過濾空行
                    .join("<br>") // 拼接成 HTML 的換行
                : ''
        );
    }
    return '';
});


const getRowClass = ({ row }) => {
    return activeRow.value === row.id ? "activeRowFocus" : "";
}

//popover 
const popoverVisible = reactive({});
const popoverPlacement = ref('left-start');
const popoverRef = ref(null);
const triggerRefs = ref({});

const size = ref('default');

let offSet = ref(8);

const popoverStyle = ref({
    // height: "550px",
    overflowX: "hidden",
    overflowY: "auto"
});


const closePopover = (id) => {
    popoverVisible[id] = false; // 關閉popover
};

const activeRow = ref(null);
const openEditPopover = (row) => {
    console.log(row);

    // formRef2.value.resetFields();
    setTimeout(() => {
        formRef2.value.internalFormRef.resetFields()

        fileList.value = row.image ? [
            {
                name: row.name,
                url: row.image,
                uid: genFileId(),
            },
        ] : [];

        uploadList.value = [
            {
                delete_image: false
            },
        ]

        popForm.id = row.id;
        popForm.name = row.name;
        popForm.subcategories = row.subcategories;
        popForm.title = row.title;
        popForm.subcategory_id = parseInt(row.subcategory_id);
        popForm.published_status = parseInt(row.published_status);
        popForm.color_codes = row.color_codes;

    }, 100)
};

//顯示popover(更新)
const handlePopoverShow = (id) => {
    disableScroll();
};

const disableScroll = () => {
    const scrollbarWrap = document.querySelector('.el-scrollbar__wrap');
    if (scrollbarWrap) {
        scrollbarWrap.style.overflowY = 'hidden';
    }
};

const enableScroll = () => {
    const scrollbarWrap = document.querySelector('.el-scrollbar__wrap');
    if (scrollbarWrap) {
        scrollbarWrap.style.overflowY = 'auto';
    }
};

const scrollStyle = () => {
    const style = document.createElement("style");
    style.type = "text/css";
    style.innerHTML = `
    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #d3d3d3;
      border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #b3b3b3; /* 滾動條懸停顏色 */
    }
  `;
    document.head.appendChild(style);
}

const popoverClass = ref("");

//對話框
const color_options_data = ref([]);
const product_dialog_title = ref('');
const dialogFormToggle = ref(false);
const dialogToggle = () => {
    dialogFormToggle.value = !dialogFormToggle.value;

    console.log(fileList.value);
    console.log(uploadList.value);
    console.log(popForm);
    resetForm();
}

//上傳upload
const fileList = ref([]);
let uploadList = ref([]);

// const NewSubRow = ref(false);

// // 儲存新增行的數據
// const NewSubRowData = reactive({
//     name: "",
//     search_key: "",
//     show_in_list: "",
//     order_index: "",
// });


//通知
const showMessage = (type, title) => {
    ElNotification({
        type, // "success" 或 "error"
        title,
        position: 'bottom-left',
    });
};


//表單
const formRef = ref(null);
const formRef2 = ref(null);
const formValidated = (mode) => {
    switch (mode) {
        case "edit":
            return formRef2.value.formValidate();
        case "add":
            return formRef.value.formValidate();
    }
};

// 重置表單
const resetForm = () => {
    nextTick(() => {
        formRef.value.internalFormRef.resetFields();

        popForm.id = '';
        popForm.name = '';
        popForm.title = '';
        popForm.subcategory_id = '';
        popForm.published_status = '';
        popForm.color_codes = [];
        popForm.subcategories = [];

        fileList.value = [];
        uploadList.value = [];
    })
};

//提交
let formData = new FormData();

const formBeforSubmit = () => {
    formData = new FormData(); // 清空
    console.log(popForm);

    formData.append('name', popForm.name);
    formData.append('title', popForm.title);
    formData.append('subcategory_id', popForm.subcategory_id); //待修正
    formData.append('published_status', popForm.published_status);
    formData.append('color_codes', popForm.color_codes);

    console.log(uploadList.value);


    if (uploadList.value.length > 0) {
        const file = uploadList.value[0];
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

    return formData;
}

// Popconfirm 的顯示狀態
const popconfirmVisible = ref({});

//編輯
const onSubmitEdit = async (id) => {
    try {
        await formValidated("edit");
        await formBeforSubmit();

        // console.log(formData);
        // for (const [key, value] of formData.entries()) {
        //     console.log(`${key}:`, value);
        // }

        // 模擬 PATCH 方法
        formData.append('_method', 'PATCH');

        // const response = await axios.patch(`/back/products/${id}`, formData, {
        //     headers: {
        //         'Content-Type': 'multipart/form-data',
        //     },
        // });

        const response = await axios.post(`/back/products/${id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // console.log('stores.data', stores.data);
        console.log('提交成功:', response.data);

        // 從後端獲取更新後的數據
        const updatedProduct = response.data;
        // console.log(updatedStore);
        // console.log(products.value);

        if (updatedProduct) {
            // 更新目標行
            const index = products.value.findIndex(product => product.id === updatedProduct.id);
            if (index !== -1) {
                // products.value[index] = updatedProduct; // 更新數據
                Object.assign(products.value[index], updatedProduct); // 更新原始數據
            }

            closePopover(popForm.id);
            showMessage("success", "保存成功");
            return
        }

    } catch (error) {
        console.error('提交失败:', error);
    }
};

//新增
const onSubmitAdd = async () => {
    try {
        await formValidated("add");
        await formBeforSubmit();
        const response = await axios.post('/back/products', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // console.log('stores.data', stores.data);
        console.log('提交成功:', response.data);

        // 從後端獲取更新後的數據
        const updatedProduct = response.data;
        if (updatedProduct) {
            dialogFormToggle.value = !dialogFormToggle.value;
            products.value.push(updatedProduct);
            showMessage("success", "新增成功");
            return
        }

        //上傳失敗提示
        showMessage("error", "新增失敗");

    } catch (error) {
        console.error('提交失败:', error);
        showMessage("error", "新增失敗");
    }
}

//刪除
const onSubmitDel = async (id) => {
    // console.log(id);
    try {
        let delForm = {
            id: id
        }

        const response = await axios.delete(`/back/products/${id}`, delForm, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // console.log('stores.data', stores.data);
        // console.log('提交成功:', response.data);

        // 從後端獲取更新後的數據
        const feedback = response.data;
        if (feedback.message = 'success') {
            products.value = products.value.filter(item => item.id !== id);
            showMessage("success", "移除成功");
            return
        }

        //上傳失敗提示
        showMessage("error", "移除失敗");

    } catch (error) {
        console.error('提交失败:', error);
    }
}

const productId = ref(null);

//顏色管理 - 編輯
const dialogColorVisible = ref(false);
const dialogColorToggle = async (product) => {
    productId.value = product.id;
    product_dialog_title.value = product.name + " 顏色管理";

    try {
        const response = await axios.get(`/back/products/${product.id}/prod_options`);

        color_options_data.value = response.data;
        // 確保資料載入後才打開 dialog
        dialogColorVisible.value = true;
        console.log(color_options_data.value);
    } catch (error) {
        console.error("載入產品顏色資料失敗：", error);
    }
};



let formDataForCo = new FormData();

const toggleEdit = async (row) => {
    // const isEditing = type === TypeEnum.CATEGORY ? editingCgRow.value === row.id : editingRow.value === row.id;
    const isEditing = editingRow.value === row.id ? editingRow.value : null;

    if (isEditing) {
        console.log(row);
        console.log(tempRow.value);
        const hasChange = Object.keys(tempRow.value).some((key) =>
            tempRow.value[key] !== row[key]
        )

        if (!hasChange && !fileList_co.value.length) {
            editingRow.value = null;
            return
        }
        console.log(fileList_co.value);

        // for (const [key, value] of formDataForCo.entries()) {
        //     console.log(`${key}:`, value);
        // }

        await colorFormRef.value.colorFormValidate();
        await colorFormBeforSubmit();
        await updateCo(tempRow.value.id);

    } else {
        editingRow.value = row.id;
        tempRow.value = { ...row };
        console.log(fileList_co.value);
    }
};

const colorFormRef = ref();
const tempRow = ref({});
const editingRow = ref(null);
const colorFormBeforSubmit = () => {
    formDataForCo = new FormData();
    formDataForCo.append('id', tempRow.value.id);
    formDataForCo.append('product_id', tempRow.value.product_id);
    formDataForCo.append('color_name', tempRow.value.color_name);
    formDataForCo.append('color_code', tempRow.value.color_code);
    formDataForCo.append('price', tempRow.value.price);
    formDataForCo.append('enable', tempRow.value.enable);
    formDataForCo.append('_method', 'PATCH');

    console.log(fileList_co.value);
    if (fileList_co.value.length > 0) {
        const file = fileList_co.value[0].file;
        //有傳新圖片時
        if (file) {
            formDataForCo.append('image', file);
        }

    } else {
        //刪掉現有的圖片
        if (!tempRow.value.image) {
            formDataForCo.append('delete_image', true);
            console.log('delete image');
        } else {
            //沒傳圖片，但也沒刪
            console.log('do not delete_image');
        }
    }
    return formDataForCo;
}

// 取消子類別編輯模式
const cancelEdit = () => {
    editingRow.value = null;
    fileList_co.value = [];
};

const fileList_co = ref([]);

const updateCo = async (product_option_id) => {
    try {
        const response = await axios.post(`/back/product_options/${product_option_id}`, formDataForCo, {
            headers: {
                'Content-Type': 'multipart/form-data',
                // '_method': 'patch'
            },
        });

        // console.log(response.data);
        // console.log(tempRow.value);

        if (response.data) {
            const index = color_options_data.value.findIndex(option => option.id === response.data.id);
            if (index !== -1) {
                console.log(color_options_data.value[index]);
                color_options_data.value[index] = {
                    ...response.data, // 展開所有其他資料
                    enable: Number(response.data.enable),
                    price: Number(response.data.price),
                }; // 更新數據
                editingRow.value = null;
            }
            showMessage("success", "保存成功");
        }

    } catch (error) {
        console.error('提交失败:', error);
        showMessage("error", "保存失敗");
    }
};

//顏色管理 - 新增
const newCoFormRef = ref();
const newCoRowData = ref({
    color_name: '',
    color_code: '',
    price: '',
    enable: '',
    image: '',
    product_id: productId,
    published_status: 1
});

const newCoRowVisible = ref(false);
const fileListAdd_co = ref([]);

let formDataForCoAdd = new FormData();

const toggleAddBtn = () => {
    newCoRowVisible.value = !newCoRowVisible.value;
    newCoFormRef.value.resetFields();
    fileListAdd_co.value = [];
}

const toggleAdd = async () => {
    // console.log(newCoRowData.value);
    // console.log(fileListAdd_co.value);
    // console.log(productId.value);

    await newCoFormRef.value.colorAddFormValidate();
    await colorAddFormBeforSubmit();
    await addCo();

}

const colorAddFormBeforSubmit = () => {
    formDataForCoAdd = new FormData();
    formDataForCoAdd.append('product_id', newCoRowData.value.product_id);
    formDataForCoAdd.append('color_name', newCoRowData.value.color_name);
    formDataForCoAdd.append('color_code', newCoRowData.value.color_code);
    formDataForCoAdd.append('price', newCoRowData.value.price);
    formDataForCoAdd.append('enable', newCoRowData.value.enable);
    formDataForCoAdd.append('published_status', newCoRowData.value.published_status);

    console.log(fileListAdd_co.value);
    if (fileListAdd_co.value.length > 0) {
        const file = fileListAdd_co.value[0].file;
        //有傳新圖片時
        if (file) {
            formDataForCoAdd.append('image', file);
        }

    } else {
        //刪掉現有的圖片
        if (!newCoRowData.value.image) {
            formDataForCoAdd.append('delete_image', true);
            console.log('delete image');
        } else {
            //沒傳圖片，但也沒刪
            console.log('do not delete_image');
        }
    }

    // for (const [key, value] of formDataForCoAdd.entries()) {
    //     console.log(`${key}:`, value);
    // }

    return formDataForCoAdd;
}

const addCo = async () => {
    try {
        const response = await axios.post(`/back/product_options`, formDataForCoAdd, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        console.log(response.data);

        if (response.data) {
            color_options_data.value.push({
                ...response.data, // 展開所有其他資料
                enable: Number(response.data.enable),
                price: Number(response.data.price),
                published_status: 1
            });

            newCoRowVisible.value = !newCoRowVisible.value;
            newCoFormRef.value.resetFields();
            fileListAdd_co.value = [];
            showMessage("success", "新增成功");
        }

    } catch (error) {
        console.error('提交失败:', error);
        showMessage("error", "新增失敗");
    }
};

//顏色管理 - 移除
const delCo = async (id) => {
    try {
        // console.log(id);
        const response = await axios.delete(`/back/product_options/${id}`);
        // console.log(response.data);

        if (response.data.message == 'success') {
            color_options_data.value = color_options_data.value.filter(e => e.id != id);
            showMessage('success', '刪除成功');
            return
        }

    } catch {
        showMessage('error', '刪除失敗');
    }
}

//顏色管理 - 移除前確認附圖
const chkCoImgs = async (id) => {
    try {
        const response = await axios.get(`/back/product_options/${id}/checkImgs`);
        const imageCount = response.data.image_count;
        console.log(imageCount);

        if (imageCount > 0) {
            // const confirmed = window.confirm(`該產品顏色尚有${imageCount}張附圖，是否刪除?`);
            // if(!confirmed) return;
            let msg = `該產品顏色尚有 ${imageCount} 張產品附圖，是否確認刪除?`
            chkCoImgsMsg(msg,id);
        } else{
            delCo(id);
        }

        // delCo(id);
    } catch (error) {
        console.error(error);
    }
}

const chkCoImgsMsg = (msg,id) => {
    ElMessageBox.alert(msg, '刪除確認', {
        // if you want to disable its autofocus
        // autofocus: false,
        confirmButtonText: 'OK',
        callback: () => {
            //   console.log(123);
            delCo(id);
        },
    })
}


//監聽編輯高亮狀態
watch(
    popoverVisible,
    (newVal) => {
        // 遍歷所有的 ID，檢查是否有顯示中的 Popover
        for (const id in newVal) {
            if (newVal[id]) {
                activeRow.value = Number(id); // 將行 ID 設置為高亮
                return;
            }
        }
        activeRow.value = ""; // 如果沒有任何 Popover 顯示，清除高亮
    },
    { deep: true } // 深度監聽，確保監聽對象的嵌套值變化
);

//監聽移除高亮狀態
watch(
    popconfirmVisible,
    (newVal) => {
        // 遍歷所有的 ID，檢查是否有顯示中的 Popover
        for (const id in newVal) {
            if (newVal[id]) {
                activeRow.value = Number(id); // 將行 ID 設置為高亮
                return;
            }
        }
        activeRow.value = ""; // 如果沒有任何 popconfirm 顯示，清除高亮
    },
    { deep: true } // 深度監聽，確保監聽對象的嵌套值變化
);

</script>

<style scoped>
.el-checkbox__label {
    padding-left: 3px;
}

.filters {
    margin: 10px 0 15px;
}

.el-select {
    margin-right: 10px;
}

::v-deep(.el-popover .el-popper) {
    height: 400px !important;
    overflow-y: scroll !important;
    background-color: #409eff;
}


::v-deep(.el-form-item__label) {
    margin-bottom: 4px;
    color: #172b4d;
    width: auto !important;
}

::v-deep(.el-input__inner:focus) {
    outline: none;
    box-shadow: none;
}

::v-deep(.el-upload) {
    width: 100%;
}

::v-deep(.el-form-item__content) {
    display: block;
}

::v-deep(.el-upload-list__item) {
    transition: none;
}

::v-deep(.el-upload-list__item-info):hover {
    cursor: pointer;
}

::v-deep(.el-upload-list__item-info):hover span {
    color: #409eff;
}

::v-deep(.el-dialog__wrapper) {
    background-color: rgba(0, 0, 0, 0.5);
    /* 半透明背景 */
}

.activeButton {
    background-color: var(--el-button-hover-bg-color);
    border-color: var(--el-button-hover-border-color);
    color: var(--el-button-hover-text-color);
    outline: none;
}

::v-deep(.activeRowFocus) {
    --el-table-tr-bg-color: var(--el-table-row-hover-bg-color);
}

::v-deep(.el-form-item__content .el-dialog) {
    width: max-content;
}

::v-deep(.el-form-item) {
    margin: auto;
}
</style>