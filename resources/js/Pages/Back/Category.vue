<template>
    <BackendLayout>
        <template #switch>
            <div>
                <el-table :data="categories.data" style="width: 100%; height: calc(100vh - 250px)"
                    v-el-table-infinite-scroll="loadMore" v-loading="loading" :infinite-scroll-disabled="loading"
                    element-loading-text="加載中..." border>

                    <el-table-column width="220" :fixed="isFixedStore ? 'left' : false" prop="name">
                        <template #header>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span>類別名稱</span>
                                <el-tooltip content="固定/取消固定類別列">
                                    <el-checkbox v-model="isFixedStore"></el-checkbox>
                                </el-tooltip>
                            </div>
                        </template>
                    </el-table-column>

                    <el-table-column prop="search_key" label="類別索引" />
                    <el-table-column prop="order_index" label="順序" />
                    <el-table-column prop="show_in_list" label="操作" />

                    <el-table-column width="140" :fixed="isFixed ? 'right' : false">
                        <template #header>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span>操作</span>
                                <!-- <el-tooltip content="固定/取消固定操作列">
                                        <el-checkbox v-model="isFixed"></el-checkbox>
                                    </el-tooltip> -->
                            </div>
                        </template>

                        <template #default="scope">
                            <!-- <template #reference> -->
                            <el-button type='primary' text @click="dialogToggle(scope.row)">子類別</el-button>
                            <!-- </template> -->
                        </template>

                        <!-- <el-button size="small">編輯</el-button> -->
                        <!-- <template #default="scope">
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

                            </template> -->
                    </el-table-column>
                </el-table>
            </div>


            <!-- 對話框 -->
            <el-dialog v-model="dialogFormToggle" :title="dialogTitle" width="1000px">
                <!-- <VueDraggable v-model="subcategory" target="tbody" @end="onDragEnd" :animation="150" ghostClass="ghost">
                    <el-table :data="subcategory">
                        <el-table-column property="name" label="子類別" width="200" />
                        <el-table-column property="search_key" label="索引名稱" />
                        <el-table-column property="order_index" label="順序" width="150" />
                        <el-table-column property="show_in_list" label="顯示 / 隱藏" />

                        <el-table-column>
                            <template #header>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span>操作</span>
                                </div>
                            </template>
                            <template #default="scope">
                                <el-button size="small" @click="dialogToggleSecond(scope.row)">編輯</el-button>
                                <el-button size="small" type="danger">移除</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </VueDraggable> -->

                <VueDraggable v-model="subcategory" target="tbody" @end="onDragEnd" :animation="150" ghostClass="ghost">
                    <el-form :model="tempRow" :rules="rules" ref="subFormRef">
                        <el-table :data="subcategory" style="width: 100%">
                            <el-table-column label="子類別" width="220">
                                <template #default="scope">
                                    <div v-if="editingRow !== scope.row.id">
                                        {{ scope.row.name }}
                                    </div>
                                    <el-form-item v-else prop="name">
                                        <el-input v-model="tempRow.name" placeholder="輸入子類別" size="small" />
                                    </el-form-item>
                                </template>
                            </el-table-column>

                            <el-table-column label="索引名稱" width="220">
                                <template #default="scope">
                                    <div v-if="editingRow !== scope.row.id">
                                        {{ scope.row.search_key }}
                                    </div>
                                    <el-form-item v-else prop="search_key">
                                        <el-input v-model="tempRow.search_key" placeholder="輸入索引名稱" size="small" />
                                    </el-form-item>
                                </template>
                            </el-table-column>

                            <!-- show_in_list -->
                            <el-table-column label="顯示 / 隱藏" width="220">
                                <template #default="scope">
                                    <div v-if="editingRow !== scope.row.id">
                                        {{ scope.row.show_in_list == 1 ? "顯示" : "隱藏" }}
                                    </div>
                                    <div v-else>
                                        <el-form-item>
                                            <el-radio-group v-model="tempRow.show_in_list">
                                                <el-radio :value="1">顯示</el-radio>
                                                <el-radio :value="0">隱藏</el-radio>
                                            </el-radio-group>
                                        </el-form-item>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column label="順序" width="108">
                                <template #default="scope">
                                    <div v-if="editingRow !== scope.row.id" >
                                        {{ scope.row.order_index }}
                                    </div>
                                    <el-form-item v-else>
                                        {{ scope.row.order_index }}
                                    </el-form-item>
                                </template>
                            </el-table-column>

                            <el-table-column label="操作" width="200">
                                <template #default="scope">
                                        <el-button :type="editingRow === scope.row.id ? 'primary' : ''" size="small"
                                            @click="toggleEdit(scope.row)">
                                            {{ editingRow === scope.row.id ? "儲存" : "編輯" }}
                                        </el-button>
                                        <el-button v-if="editingRow === scope.row.id" size="small" @click="cancelEdit">
                                            取消
                                        </el-button>
    
                                        <el-popconfirm v-if="editingRow !== scope.row.id" title="確定移除此筆資料?"
                                            @confirm="deleteSubcategory(scope.row.id)" :width="170" :hide-after="100"
                                            v-model:visible="popconfirmVisible[scope.row.id]">
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

                                    <!-- <el-button size="small" type="danger" v-if="editingRow !== scope.row.id">移除</el-button> -->
                                </template>
                            </el-table-column>
                        </el-table>
                    </el-form>
                </VueDraggable>

                <!-- <div v-if="NewSubRow" class="new_sub_row">
                    <el-row>
                        <el-col>
                            <el-input v-model="NewSubRow.name" placeholder="輸入子類別" size="small" />
                        </el-col>
                        <el-col>
                            <el-input v-model="NewSubRow.search_key" placeholder="輸入索引名稱" size="small" />
                        </el-col>
                        <el-col>
                            <el-radio-group v-model="NewSubRow.show_in_list">
                                <el-radio :value="1">顯示</el-radio>
                                <el-radio :value="0">隱藏</el-radio>
                            </el-radio-group>
                        </el-col>
                        <el-col>
                            {{ NewSubRow.order_index }}
                        </el-col>
                        <el-col>
                            <el-button size="small" type="success" @click="insertNewSubRow">
                                新增
                            </el-button>
                            <el-button size="small" @click="cancelNewSubRow">
                                取消
                            </el-button>
                        </el-col>
                    </el-row>
                </div> -->

                <div v-show="NewSubRow" class="new_sub_row">
                    <el-form :model="NewSubRowData" :rules="rules" ref="newSubFormRef">
                        <el-row>
                            <el-col>
                                <el-form-item prop="name">
                                    <el-input v-model="NewSubRowData.name" placeholder="輸入子類別" size="small" />
                                </el-form-item>
                            </el-col>
                            <el-col>
                                <el-form-item prop="search_key">
                                    <el-input v-model="NewSubRowData.search_key" placeholder="輸入索引名稱" size="small" />
                                </el-form-item>
                            </el-col>
                            <el-col>
                                <el-form-item>
                                    <el-radio-group v-model="NewSubRowData.show_in_list">
                                        <el-radio :value="1">顯示</el-radio>
                                        <el-radio :value="0">隱藏</el-radio>
                                    </el-radio-group>
                                </el-form-item>
                            </el-col>
                            <el-col>
                                <el-form-item>
                                    {{ NewSubRowData.order_index }}
                                </el-form-item>
                            </el-col>
                            <el-col>
                                <el-form-item>
                                    <el-button size="small" type="success" @click="insertNewSubRow">
                                        新增
                                    </el-button>
                                    <el-button size="small" @click="cancelNewSubRow">
                                        取消
                                    </el-button>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </el-form>
                </div>

                <el-button v-if="!NewSubRowButton" style="margin: 10px 0;" @click="addNewSubRow">添加子類別</el-button>

            </el-dialog>

            <el-dialog v-model="dialogSecondToggle" :show-close="false" width="800px" :close-on-click-modal="false">
                <template #header="{ close, titleId, titleClass }">
                    <div class="my-header">
                        <el-button size="large" @click="dialogToggleSecond" text>
                            <el-icon :size="18">
                                <ArrowLeft />
                            </el-icon>
                        </el-button>
                        <h4 :id="titleId" :class="titleClass" style="color:#44546f">編輯子類別</h4>
                    </div>
                </template>
            </el-dialog>

        </template>
    </BackendLayout>
</template>

<script setup>

import { ref, reactive, onMounted, computed, nextTick, watch } from "vue";
import axios from "axios";
import BackendLayout from '@/Layouts/BackendLayout.vue';
// import CategoryForm from "./FormComponent/CategoryForm.vue";
import debounce from "lodash.debounce";
import { genFileId } from 'element-plus';
import { VueDraggable } from 'vue-draggable-plus'

// 初始化加載
onMounted(() => {
    loadMore();
})

// 初始數據
const categories = reactive({
    data: [],
    current_page: 1,
    last_page: null,
});

const loading = ref(false);
const noMoreData = ref(false);
const isFixed = ref(false); // 默認不固定
const isFixedStore = ref(false); // 默認不固定

// 加載更多數據
const loadMore = debounce(async () => {
    if (loading.value || noMoreData.value) return;
    loading.value = true;

    try {
        const response = await axios.get("/back/categories", {
            params: {
                page: categories.current_page,
                // store_type: storeType.value || null,
                // search_key: addressFilter.value || null,
            },
        });

        console.log(response.data);

        const newData = response.data.data; // 新數據 
        const lastPage = response.data.last_page; // 總頁數 ex:11

        // 如果有數據，追加到 stores.data
        if (newData.length > 0) {
            categories.data.push(...newData);
            categories.current_page += 1; // 頁碼 +1
            categories.last_page = lastPage;

            // 如果當前頁碼超過最後一頁，標記沒有更多數據
            if (categories.current_page > categories.last_page) {
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

//對話框
const dialogFormToggle = ref(false);
const dialogSecondToggle = ref(false);
const dialogTitle = ref();
const subcategory = ref();
const category_id = ref(null);

const dialogToggle = (row) => {
    dialogFormToggle.value = !dialogFormToggle.value;
    dialogTitle.value = '類別 : ' + row.name;
    subcategory.value = row.subcategories;
    category_id.value = row.id;
}

const dialogToggleSecond = (row) => {
    // dialogFormToggle.value = !dialogFormToggle.value;
    // dialogSecondToggle.value = !dialogSecondToggle.value;
    console.log(row);

}

const onDragEnd = (evt) => {
    console.log("拖拽完成：", evt);
};

// 編輯狀態的行 ID
const editingRow = ref(null);

// 臨時數據保存編輯值
const tempRow = ref({});

// 切換到編輯模式
const toggleEdit = async (row) => {
    if (editingRow.value === row.id) {
        
        try {
            // 發送 AJAX 保存數據
            await saveRow(tempRow.value, row);
            // editingRow.value = null; // 退出編輯模式
        } catch (error) {
            console.error('保存失敗', error);
            // editingRow.value = null;
        }
    } else {
        tempRow.value = { ...row }
        editingRow.value = row.id; // 進入編輯模式
    }
};


// 保存行數據
const saveRow = async (tempRow, originalRow) => {
    try {
        const hasChanged = Object.keys(tempRow).some(
            (key) => tempRow[key] !== originalRow[key]
        );

        if (!hasChanged) {
            editingRow.value = null;
            return; // 不發送 AJAX 請求
        }

        await formValidate(subFormRef);

        let subcategory_id = originalRow.id;
        let formData = {
            category_id: tempRow.category_id,
            name: tempRow.name,
            search_key: tempRow.search_key,
            show_in_list: tempRow.show_in_list,
            order_index: tempRow.order_index
        }

        // console.log(formData);
        // console.log(subcategory_id);
        const response = await axios.post(`/back/subcategories/${subcategory_id}/update_sub`, formData);
        if (response.data) {
            Object.assign(originalRow, tempRow); // 更新原始數據
            editingRow.value = null; // 退出編輯模式
            showMessage("success", "保存成功");
            return
        } else {
            showMessage("error", "保存失敗");
            throw new Error("保存失敗");
        }

    } catch (error) {
        console.error("保存失敗", error);
        showMessage("error", "保存失敗");
    }
};

//刪除
const deleteSubcategory = async (id) => {
    try {
        const response = await axios.delete(`/back/subcategories/${id}`);
        if (response.data.message == 'success') {
            subcategory.value = subcategory.value.filter((item) => item.id !== id);
            showMessage("success", "刪除成功");
        } else {
            showMessage("success", "刪除失敗");
        }
    } catch (error) {
        showMessage("error", "刪除失敗");
    }
}

// 取消編輯
const cancelEdit = () => {
    editingRow.value = null;
};

// Popconfirm 的顯示狀態
const popconfirmVisible = ref({});

const showMessage = (type, title) => {
    ElNotification({
        type, // "success" 或 "error"
        title,
        position: 'bottom-left',
    });
};

const NewSubRowButton = ref(false);
const NewSubRow = ref(false);

// 儲存新增行的數據
const NewSubRowData = reactive({
    name: "",
    search_key: "",
    show_in_list: "",
    order_index: "",
});


const newSubFormRef = ref(null); // 表單引用
const subFormRef = ref(null);

// 表單驗證規則
const rules = reactive({
    name: [
        { required: true, message: "子類別名稱為必填項", trigger: "blur" },
    ],
    search_key: [
        { required: true, message: "索引名稱為必填項", trigger: "blur" },
        {
            pattern: /^[a-zA-Z0-9_]+$/,
            message: "索引名稱只能包含字母、數字或底線",
            trigger: "change",
        },
    ],
    // show_in_list: [
    //     { required: true, message: "必須選擇顯示/隱藏", trigger: "change" },
    // ],
});



//添加子類別
const addNewSubRow = () => {
    // if (!NewSubRow.value) {
    //     NewSubRow.value = {
    //         name: "",
    //         search_key: "",
    //         show_in_list: 1,
    //         order_index: subcategory.value.length + 1,
    //     };
    //     NewSubRowButton.value = !NewSubRowButton.value;
    // }
    // console.log(subcategory.value);
    const maxOrderIndex = subcategory.value.length > 0
    ? Math.max(...subcategory.value.map(item => item.order_index))
    : 0;
    

    Object.assign(NewSubRowData, {
        name: "",
        search_key: "",
        show_in_list: 1,
        order_index: maxOrderIndex + 1,
    });


    NewSubRowButton.value = !NewSubRowButton.value;
    NewSubRow.value = !NewSubRow.value;

};

//新增(取消)
const cancelNewSubRow = () => {
    NewSubRow.value = null;
    NewSubRowButton.value = !NewSubRowButton.value;
    newSubFormRef.value.resetFields();
}

//新增(送出)
const insertNewSubRow = async () => {
    // console.log(category_id.value);
    // console.log(NewSubRow.value);

    try {
        await formValidate(newSubFormRef);
        const formData = {
            name: NewSubRowData.name,
            search_key: NewSubRowData.search_key,
            show_in_list: NewSubRowData.show_in_list,
            // order_index: NewSubRowData.order_index
        };


        // 發送 POST 請求到後端
        const response = await axios.post(
            `/back/categories/${category_id.value}/subcategories`,
            formData
        );

        console.log(response.data.data);

        if (response.data.data) {
            // 如果請求成功，將新數據插入到子類別列表中
            subcategory.value.push(response.data.data);
            NewSubRow.value = null;
            NewSubRowButton.value = !NewSubRowButton.value;
            showMessage("success", "新增成功");
        } else {
            showMessage("error", "新增失敗");
        }
    } catch (error) {
        // showMessage("error", "新增失敗");
    }
};


const formValidate = (forRef) => {
    return new Promise((resolve, reject) => {
        if (!forRef.value) {
            reject(new Error("未綁定"));
        } else {
            forRef.value.validate((isValid) => {
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

</script>

<style scoped>
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}

.my-header {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    align-items: center;
}

.my-header>.el-button {
    grid-column: 1 / 2;
    width: max-content;
}

.my-header>.el-dialog__title {
    grid-column: 4 / 7;
}

.el-radio {
    margin-right: 15px;
}

::v-deep(.el-input__inner:focus) {
    outline: none;
    box-shadow: none;
}

::v-deep(.el-row) {
    display: grid;
    grid-template-columns: 220px 220px 220px 108px 200px;
    align-items: center;
    padding: 8px 0;
    /* margin-top: 10px; */

}

::v-deep(.el-row > div) {
    padding: 0 12px !important;
}

::v-deep(.el-form-item){
    margin: auto;
}
</style>