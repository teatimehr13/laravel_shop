<template>
    <BackendLayout>
        <template #switch>
            <div>
                <!-- 篩選器 -->
                <div class="filters">
                    <el-select v-model="storeType" placeholder="全部類型" style="width: 150px" @change="applyFilters">
                        <el-option v-for="item in storeTypeArr" :key="item.value" :label="item.name"
                            :value="item.value" />
                    </el-select>

                    <el-input v-model="addressFilter" placeholder="輸入地址篩選" style="width: 240px"
                        @keyup.enter="applyFilters" />
                </div>

                <el-button type="" @click="dialogToggle" style="margin: 10px 0;">新增門市</el-button>

                <!-- Table 與懶加載 -->
                <div>
                    <el-table :data="stores.data" style="width: 100%; height: calc(100vh - 250px)"
                        v-el-table-infinite-scroll="loadMore" v-loading="loading" :infinite-scroll-disabled="loading"
                        element-loading-text="加載中..." :row-class-name="getRowClass" border>

                        <!-- <el-table-column prop="store_name" label="門市名稱" width="200"/> -->

                        <el-table-column width="220" :fixed="isFixedStore ? 'left' : false" prop="store_name">
                            <template #header>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span>門市名稱</span>
                                    <el-tooltip content="固定/取消固定門市列">
                                        <el-checkbox v-model="isFixedStore"></el-checkbox>
                                    </el-tooltip>
                                </div>
                            </template>
                        </el-table-column>

                        <el-table-column label="圖片" width="120">
                            <template #default="scope">
                                <img v-if="scope.row.image" :src="scope.row.image" alt="店面圖片"
                                    style="max-width: 75px; border-radius: 4px" />
                            </template>
                        </el-table-column>
                        <el-table-column prop="store_type_name" label="類型" width="120" />
                        <el-table-column prop="address" label="地址" width="300" />
                        <el-table-column prop="contact_number" label="聯絡電話" width="150" />
                        <el-table-column prop="opening_hours" label="營業時間" width="200">
                            <template #default="scope">
                                <div v-html="formattedOpeningHours[scope.$index]"></div>
                            </template>
                        </el-table-column>

                        <el-table-column width="140" :fixed="isFixed ? 'right' : false">
                            <template #header>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span>操作</span>
                                    <el-tooltip content="固定/取消固定操作列">
                                        <el-checkbox v-model="isFixed"></el-checkbox>
                                    </el-tooltip>
                                </div>
                            </template>

                            <!-- <el-button size="small">編輯</el-button> -->
                            <template #default="scope">
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

                                    <!-- 子組件，編輯 -->
                                    <StoreForm v-model:file-list="fileList" v-model:form-data="popForm"
                                        @submit="onSubmitEdit" v-model:uploadList="uploadList" ref="formRef2"
                                        @uploadList="emitUploadList" />
                                    <el-form-item>
                                        <el-button type="primary" @click="onSubmitEdit">儲存</el-button>
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

                <!-- 加載提示 -->
                <!-- <div v-if="noMoreData" style="text-align: center; margin: 10px">沒有更多數據了</div> -->
            </div>

            <!-- 對話框 -->
            <el-dialog v-model="dialogFormToggle" title="新增" width="400px">
                <StoreForm v-model:form-data="popForm" v-model:file-list="fileList" v-model:upload-list="uploadList"
                    @uploadList="emitUploadList" ref="formRef" />
                <template #footer>
                    <el-button type="primary" @click="onSubmitAdd">提交</el-button>
                    <el-button @click="dialogToggle">取消</el-button>
                    <!-- <el-button @click="dd">測試</el-button> -->
                </template>
            </el-dialog>

        </template>
    </BackendLayout>
</template>


<script setup>
import { ref, reactive, onMounted, computed, nextTick, watch } from "vue";
import axios from "axios";
import BackendLayout from '@/Layouts/BackendLayout.vue';
import StoreForm from "./FormComponent/StoreForm.vue";
import debounce from "lodash.debounce";
import { genFileId } from 'element-plus'

// 初始化加載
onMounted(() => {
    loadMore();
    scrollStyle();

})

const emitUploadList = (data) => {
    uploadList.value = data;
    // console.log(uploadList.value);
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
    store_name: '',       // 名稱
    store_type: '',       // 類型
    address: '',
    contact_number: '',
    opening_hours: '',
    // is_enabeld: ''
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
    height: "550px",
    overflowX: "hidden",
    overflowY: "auto"
});


const closePopover = (id) => {
    popoverVisible[id] = false; // 關閉popover
    // activeRow.value = '';
    // resetForm();
};

const activeRow = ref(null);
const openEditPopover = (row) => {
    // console.log(fileList.value);
    // console.log(uploadList.value);
    // console.log(popForm);
    // activeRow.value = row.id;
    // console.log(activeRow);

    setTimeout(() => {
        fileList.value = row.image ? [
            {
                name: row.store_name,
                url: row.image,
                uid: genFileId(),
            },
        ] : [];

        uploadList.value = [
            {
                delete_image: false
            },
        ]

        popForm['is_enabled'] = parseInt(row['is_enabled']);
        popForm.id = row.id;
        popForm.store_name = row.store_name;
        popForm.store_type = parseInt(row.store_type);
        popForm.address = row.address;
        popForm.image = row.image;
        popForm.contact_number = row.contact_number;
        popForm.opening_hours = row.opening_hours ? row.opening_hours.split("\n") // 按 \n 分割
            .map((line) => line.trim()) // 去除多餘空白
            .filter((line) => line !== "") // 過濾空行
            .join("\n") // 拼接成 HTML 的換行;
            : '';
    }, 100)
};

//顯示popover(更新)
const handlePopoverShow = (id) => {
    disableScroll();
    // adjustPlacement(id); 
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

const adjustPlacement = (id) => {
    const triggerElement = triggerRefs.value[id]; // 获取对应的按钮 DOM

    if (!triggerElement) {
        console.error(`未找到 trigger-${id} 的元素`);
        return;
    }

    // 如果是组件引用，取 $el
    const domElement = triggerElement.$el || triggerElement;

    // 按鈕位置
    const triggerRect = domElement.getBoundingClientRect();
    // popover高度    
    const popoverHeight = 455;

    //畫面的高度
    const viewportHeight = window.innerHeight;

    console.log(triggerRect.bottom);
    console.log(triggerRect.bottom + popoverHeight);
    console.log(viewportHeight);

    if (triggerRect.bottom + popoverHeight > viewportHeight) {
        popoverPlacement.value = 'left-start';
        // 动态调整 placement 或其他逻辑
    } else {
        popoverPlacement.value = 'bottom-start';
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
const dialogFormToggle = ref(false);
const dialogToggle = () => {

    dialogFormToggle.value = !dialogFormToggle.value;

    console.log(fileList.value);
    console.log(uploadList.value);
    console.log(popForm);
    resetForm();

}

//通知
const open3 = () => {
    ElNotification({
        title: '操作成功',
        // message:'上傳成功',
        type: 'success',
        position: 'bottom-left',
    })
}

const open4 = () => {
    ElNotification({
        title: '操作失敗',
        type: 'error',
        position: 'bottom-left',
    })
}

//上傳upload
const fileList = ref([]);
let uploadList = ref([]);

// const upload = ref();


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
        popForm.store_name = '';
        popForm.store_type = '';
        popForm.address = '';
        popForm.contact_number = '';
        popForm.opening_hours = '';
        popForm['is_enabled'] = '';
        fileList.value = [];
        uploadList.value = [];
    })


};
//提交
let formData = new FormData();

const formBeforSubmit = () => {
    formData = new FormData(); // 清空
    console.log(popForm);
    formData.append('store_name', popForm.store_name);
    formData.append('store_type', popForm.store_type);
    formData.append('address', popForm.address);
    formData.append('contact_number', popForm.contact_number);
    formData.append('opening_hours', popForm.opening_hours);
    formData.append('is_enabled', popForm['is_enabled'])
    formData.append('id', popForm.id);

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

    // console.log(formData);
    // console.log(uploadList.value);

    // for (const [key, value] of formData.entries()) {
    //     console.log(`${key}:`, value);
    // }
}

// Popconfirm 的顯示狀態
const popconfirmVisible = ref({});

//編輯
const onSubmitEdit = async () => {
    try {
        await formValidated("edit");
        await formBeforSubmit();
        const response = await axios.post('/back/stores/update_stores', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // console.log('stores.data', stores.data);
        // console.log('提交成功:', response.data);

        // 從後端獲取更新後的數據
        const updatedStore = response.data;
        console.log(updatedStore);

        if (updatedStore) {
            // 更新目標行
            const index = stores.data.findIndex(store => store.id === updatedStore.id);
            if (index !== -1) {
                console.log(stores.data[index]);
                stores.data[index] = updatedStore; // 更新數據
            }
            console.log(stores.data[index]);


            closePopover(popForm.id);
            open3();
            return
        }

        //上傳失敗提示
        open4();

    } catch (error) {
        console.error('提交失败:', error);
    }
};

//新增
const onSubmitAdd = async () => {
    try {
        await formValidated("add");
        await formBeforSubmit();
        const response = await axios.post('/back/stores', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // console.log('stores.data', stores.data);
        console.log('提交成功:', response.data);

        // 從後端獲取更新後的數據
        const updatedStore = response.data;
        if (updatedStore) {
            dialogFormToggle.value = !dialogFormToggle.value;
            open3();
            return
        }

        //上傳失敗提示
        open4();

    } catch (error) {
        console.error('提交失败:', error);
    }
}

//刪除
const onSubmitDel = async (id) => {
    // console.log(id);

    try {
        let delForm = {
            id: id
        }

        const response = await axios.post('/back/stores/delete_stores', delForm, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // console.log('stores.data', stores.data);
        console.log('提交成功:', response.data);

        // 從後端獲取更新後的數據
        const feedback = response.data;
        if (feedback.success) {
            stores.data = stores.data.filter(item => item.id !== id);
            open3();
            return
        }

        //上傳失敗提示
        open4();

    } catch (error) {
        console.error('提交失败:', error);
    }
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

::v-deep(.el-dialog){
    width: max-content;
}
</style>