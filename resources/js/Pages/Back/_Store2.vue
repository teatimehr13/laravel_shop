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

                <!-- Table 與懶加載 -->
                <div>
                    <el-table :data="stores.data" style="width: 100%; height: calc(100vh - 200px)"
                        v-el-table-infinite-scroll="loadMore" v-loading="loading" :infinite-scroll-disabled="loading"
                        element-loading-text="加載中..." border>

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
                                <el-popover :placement="popoverPlacement" :width="350" trigger="click"
                                    v-model:visible="popoverVisible[scope.row.id]" popper-class="" :hide-after="0"
                                    :show-after="100" @show="handlePopoverShow(scope.row.id)"
                                    @before-leave="enableScroll" :offset="offSet" ref="popoverRef">
                                    <template #reference>
                                        <el-button size="small" @click="openEditPopover(scope.row)"
                                            :ref="el => (triggerRefs[scope.row.id] = el)">編輯</el-button>
                                    </template>

                                    <el-form style="max-width: 600px" :model="popForm" label-width="auto"
                                        :label-position="labelPosition" :size="size">
                                        <el-form-item label="門市名稱">
                                            <el-input v-model="popForm.store_name" />
                                        </el-form-item>
                                        <el-form-item label="類型">
                                            <el-select placeholder="please select your zone" el-select
                                                :teleported="false" v-model="popForm.store_type">
                                                <el-option label="直營" :value="0" />
                                                <el-option label="特約展售" :value="1" />
                                                <el-option label="授權經銷商" :value="2" />
                                            </el-select>
                                        </el-form-item>
                                        <el-form-item label="地址">
                                            <el-input v-model="popForm.address" />
                                        </el-form-item>

                                        <el-form-item label="聯絡電話">
                                            <el-input v-model="popForm.contact_number" />
                                        </el-form-item>

                                        <el-form-item label="營業時間">
                                            <el-input v-model="popForm.opening_hours" type="textarea"
                                                :autosize="{ minRows: 2, maxRows: 6 }" />
                                        </el-form-item>

                                        <el-form-item label="圖片">
                                            <div>
                                                <el-upload :file-list="fileList" class="upload-demo" action=""
                                                    :on-preview="handlePreview" :on-remove="handleRemove"
                                                    list-type="picture" :auto-upload="false" :limit="1"
                                                    :on-exceed="handleExceed" ref="upload" popper-class="no-transition"
                                                    :on-change="handleOnChange">
                                                    <el-button type="" style="width: 100% !important;">選擇檔案</el-button>
                                                </el-upload>
                                                <el-dialog v-model="dialogVisible">
                                                    <img w-full :src="dialogImageUrl" style="margin: auto"
                                                        alt="Preview Image" />
                                                </el-dialog>
                                            </div>
                                        </el-form-item>

                                        <el-form-item>
                                            <el-button type="primary" @click="onSubmit">儲存</el-button>
                                            <el-button @click="closePopover(scope.row.id)">關閉</el-button>
                                        </el-form-item>
                                    </el-form>
                                </el-popover>
                                <el-button size="small" type="danger">移除</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>

                <!-- 加載提示 -->
                <div v-if="noMoreData" style="text-align: center; margin: 10px">沒有更多數據了</div>
            </div>
        </template>
    </BackendLayout>
</template>


<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import axios from "axios";
import BackendLayout from '@/Layouts/BackendLayout.vue';
import debounce from "lodash.debounce";
import { genFileId } from 'element-plus'

// 初始化加載
onMounted(() => {
    loadMore();
})

const renderForm = () => {
  return `
    <el-form style="max-width: 600px" :model="popForm" label-width="auto" label-position="top" size="default">
      <el-form-item label="門市名稱">
        <el-input v-model="popForm.store_name" />
      </el-form-item>
      <el-form-item label="類型">
        <el-select placeholder="please select your zone" teleported="false" v-model="popForm.store_type">
          <el-option label="直營" :value="0" />
          <el-option label="特約展售" :value="1" />
          <el-option label="授權經銷商" :value="2" />
        </el-select>
      </el-form-item>
      <el-form-item label="地址">
        <el-input v-model="popForm.address" />
      </el-form-item>
      <el-form-item label="聯絡電話">
        <el-input v-model="popForm.contact_number" />
      </el-form-item>
      <el-form-item label="營業時間">
        <el-input v-model="popForm.opening_hours" type="textarea" :autosize="{ minRows: 2, maxRows: 6 }" />
      </el-form-item>
      <el-form-item label="圖片">
        <el-upload
          :file-list="fileList"
          class="upload-demo"
          action=""
          list-type="picture"
          :auto-upload="false"
          :limit="1"
        >
          <el-button style="width: 100%;">選擇檔案</el-button>
        </el-upload>
      </el-form-item>
    </el-form>
  `;
};

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

        console.log(response.data);

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
    return stores.data.map((store) =>
        store.opening_hours
            .split("\n") // 按 \n 分割
            .map((line) => line.trim()) // 去除多餘空白
            .filter((line) => line !== "") // 過濾空行
            .join("<br>") // 拼接成 HTML 的換行
    );
});

//popover 
const popoverVisible = reactive({});
const popoverPlacement = ref('left-start');
const popoverRef = ref(null);
const triggerRefs = ref({});

const size = ref('default');
const labelPosition = ref('top');
let offSet = ref(8);

const closePopover = (id) => {
    popoverVisible[id] = false; // 關閉popover
};

const openEditPopover = (row) => {
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

        popForm.is_enabled = row.is_enabled;
        popForm.id = row.id;
        popForm.store_name = row.store_name;
        popForm.store_type = row.store_type;
        popForm.address = row.address;
        popForm.image = row.image;
        popForm.contact_number = row.contact_number;
        popForm.opening_hours = row.opening_hours.split("\n") // 按 \n 分割
            .map((line) => line.trim()) // 去除多餘空白
            .filter((line) => line !== "") // 過濾空行
            .join("\n") // 拼接成 HTML 的換行;

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



// 重置表單
const resetForm = () => {
  popForm.store_name = '';
  popForm.store_type = null;
  popForm.address = '';
  popForm.contact_number = '';
  popForm.opening_hours = '';
  fileList.value = [];
};

//通知
const open3 = () => {
  ElNotification({
    title: '上傳成功',
    // message:'上傳成功',
    type: 'success',
    position: 'bottom-left',
  })
}

const open4 = () => {
  ElNotification({
    title: '上傳失敗',
    type: 'error',
    position: 'bottom-left',
  })
}

//上傳upload
const fileList = ref([]);
const uploadList = ref([]);
const dialogImageUrl = ref('')
const dialogVisible = ref(false)
const upload = ref();

//刪除待上傳的圖片
const handleRemove = (uploadFile, uploadFiles) => {
    console.log(uploadFile, uploadFiles)
    uploadList.value = [];
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
    uploadList.value = [
        {
            name: file.name,
            url: file.url,
            uid: file.uid,
            raw: file.raw
        },
    ]
}

//限制僅上傳一筆
const handleExceed = (files) => {
    console.log(files);



    // console.log(upload.value);
    // handleRemove(files);
    // if (upload.value) {
    //     console.log(upload.value);

    upload.value.clearFiles();
    const file = files[0];

    file.uid = genFileId(); // 生成唯一的 UID
    upload.value.handleStart(file)
    //     file.url = URL.createObjectURL(file);
    //     fileList.value = [];
    //     fileList.value.push({
    //         name: file.name,
    //         url: file.url,
    //         uid: file.uid,
    //     });
    // }
};

//提交
const onSubmit = async () => {
    console.log(popForm);

    const formData = new FormData();
    formData.append('store_name', popForm.store_name);
    formData.append('store_type', popForm.store_type);
    formData.append('address', popForm.address);
    formData.append('contact_number', popForm.contact_number);
    formData.append('opening_hours', popForm.opening_hours);
    formData.append('is_enabled', popForm.is_enabled)
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

    try {
        const response = await axios.post('/back/stores/update_stores', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // console.log('stores.data', stores.data);
        // console.log('提交成功:', response.data);

        // 從後端獲取更新後的數據
        const updatedStore = response.data;
        if(updatedStore){
            // 更新目標行
            const index = stores.data.findIndex(store => store.id === updatedStore.id);
            if (index !== -1) {
                stores.data[index] = updatedStore; // 更新數據
            }
    
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

::v-deep(.el-form-item__label) {
    margin-bottom: 4px;
    color: #172b4d;
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
</style>