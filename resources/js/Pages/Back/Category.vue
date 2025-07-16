<template>
    <BackendLayout>
        <template #switch>
            <div class="min-h-full">
                <div style="margin: 10px 0 15px;">
                    <el-button @click="addCgToggle">添加類別</el-button>
                    <el-tooltip class="box-item" effect="dark"
                        :content="isCgDraggable === false ? '進行拖曳表格調整' : '結束拖曳表格調整'" placement="top-start">
                        <el-button v-show="!NewSubRowButton" @click="toggleDraggable(TypeEnum.CATEGORY)"
                            :type="isCgDraggable === false ? '' : 'primary'">
                            {{ isCgDraggable === false ? "調整順序" : "退出調整" }}
                        </el-button>
                    </el-tooltip>
                </div>

                <VueDraggable v-model="categories.data" target="tbody" @end="(evt) => onDragEnd(evt, TypeEnum.CATEGORY)"
                    :animation="150" ghostClass="ghost" :disabled="!isCgDraggable">
                    <el-form :model="tempCgRow" :rules="rules" ref="cgFormRef">
                        <el-table :data="categories.data" style="width: 100%;"  border>

                            <el-table-column width="220" :fixed="isFixedStore ? 'left' : false" prop="name">
                                <!-- <template #header>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span>類別名稱</span>
                                        <el-tooltip content="固定/取消固定類別列">
                                            <el-checkbox v-model="isFixedStore"></el-checkbox>
                                        </el-tooltip>
                                    </div>
                                </template> -->
                                <template #header>
                                    類別名稱
                                </template>
                                <template #default="scope">
                                    <div v-if="editingCgRow !== scope.row.id">
                                        {{ scope.row.name }}
                                    </div>
                                    <el-form-item v-else prop="name">
                                        <el-input v-model="tempCgRow.name" placeholder="輸入子類別" size="small" />
                                    </el-form-item>
                                </template>
                            </el-table-column>

                            <el-table-column prop="search_key" label="類別索引">
                                <template #header>
                                    類別索引
                                </template>
                                <template #default="scope">
                                    <div v-if="editingCgRow !== scope.row.id">
                                        {{ scope.row.search_key }}
                                    </div>
                                    <el-form-item v-else prop="search_key">
                                        <el-input v-model="tempCgRow.search_key" placeholder="輸入子類別" size="small" />
                                    </el-form-item>
                                </template>
                            </el-table-column>

                            <el-table-column prop="order_index" label="順序" />
                            <el-table-column prop="show_in_list" label="顯示 / 隱藏">
                                <template #default="scope">
                                    <div v-if="editingCgRow !== scope.row.id">
                                        {{ scope.row.show_in_list == 1 ? "顯示" : "隱藏" }}
                                    </div>
                                    <div v-else>
                                        <el-form-item>
                                            <el-radio-group v-model="tempCgRow.show_in_list">
                                                <el-radio :value="1">顯示</el-radio>
                                                <el-radio :value="0">隱藏</el-radio>
                                            </el-radio-group>
                                        </el-form-item>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column width="140" :fixed="isFixed ? 'right' : false">
                                <template #header>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span>子類別</span>
                                        <!-- <el-tooltip content="固定/取消固定操作列">
                                                <el-checkbox v-model="isFixed"></el-checkbox>
                                            </el-tooltip> -->
                                    </div>
                                </template>

                                <template #default="scope">
                                    <el-button type='primary' text @click="dialogToggle(scope.row)">查看</el-button>
                                </template>

                            </el-table-column>

                            <el-table-column label="操作" width="200">
                                <template #default="scope">
                                    <el-button :type="editingCgRow === scope.row.id ? 'primary' : ''" size="small"
                                        @click="toggleEdit(scope.row, TypeEnum.CATEGORY)">
                                        {{ editingCgRow === scope.row.id ? "儲存" : "編輯" }}
                                    </el-button>
                                    <el-button v-if="editingCgRow === scope.row.id" size="small" @click="cancelCgEdit">
                                        取消
                                    </el-button>

                                    <el-popconfirm v-if="editingCgRow !== scope.row.id" title="確定移除此筆資料?"
                                        @confirm="deleteItem(scope.row.id, TypeEnum.CATEGORY)" :width="170"
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
                    </el-form>
                </VueDraggable>

                <div class="example-pagination-block">
                    <div class="example-demonstration"></div>
                    <el-pagination layout="prev, pager, next" :total="categories.total" :page-size="categories.per_page"
                        v-model:current-page="categories.current_page" @current-change="goToPage" background />
                </div>

            </div>

            <!-- 對話框 -->
            <el-dialog v-model="dialogFormToggle" :title="dialogTitle" width="1000px">
                <VueDraggable v-model="subcategory" target="tbody" @end="(evt) => onDragEnd(evt, TypeEnum.SUBCATEGORY)"
                    :animation="150" ghostClass="ghost" :disabled="!isDraggable">
                    <el-form :model="tempRow" :rules="rules" ref="subFormRef">
                        <el-table :data="subcategory" style="width: 100%">
                            <el-table-column label="子類別名稱" width="220">
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
                                    <div v-if="editingRow !== scope.row.id">
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
                                        @click="toggleEdit(scope.row, TypeEnum.SUBCATEGORY)">
                                        {{ editingRow === scope.row.id ? "儲存" : "編輯" }}
                                    </el-button>
                                    <el-button v-if="editingRow === scope.row.id" size="small" @click="cancelEdit">
                                        取消
                                    </el-button>

                                    <el-popconfirm v-if="editingRow !== scope.row.id" title="確定移除此筆資料?"
                                        @confirm="deleteItem(scope.row.id, TypeEnum.SUBCATEGORY)" :width="170"
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

                                    <!-- <el-button size="small" type="danger" v-if="editingRow !== scope.row.id">移除</el-button> -->
                                </template>
                            </el-table-column>
                        </el-table>
                    </el-form>
                </VueDraggable>

                <div v-show="NewSubRow" class="new_sub_row">
                    <el-form :model="NewSubRowData" :rules="rules" ref="newSubFormRef">
                        <el-row class="new_sub_form_row">
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
                                    <el-button size="small" type="success" @click="insertNewItem(TypeEnum.SUBCATEGORY)">
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

                <el-button v-show="!NewSubRowButton" style="margin: 10px 0;" @click="addNewSubRow">添加子類別</el-button>
                <el-tooltip class="box-item" effect="dark" :content="isDraggable === false ? '進行拖曳表格調整' : '結束拖曳表格調整'"
                    placement="top-start">
                    <el-button v-show="!NewSubRowButton" style="margin: 10px;"
                        @click="toggleDraggable(TypeEnum.SUBCATEGORY)" :type="isDraggable === false ? '' : 'primary'">
                        {{ isDraggable === false ? "調整順序" : "退出調整" }}
                    </el-button>
                </el-tooltip>
            </el-dialog>

            <!-- 新增類別 -->
            <el-dialog v-model="dialogCgToggle" title="新增類別" width="400px">
                <el-form :model="addCgform" class="demo-ruleForm" :rules="rules" ref="newCgFormRef">
                    <el-form-item label="類別名稱" :label-position="labelPosition" prop="name">
                        <el-input v-model="addCgform.name" />
                    </el-form-item>

                    <el-form-item label="索引名稱" :label-position="labelPosition" prop="search_key">
                        <el-input v-model="addCgform.search_key" />
                    </el-form-item>

                    <!-- <el-form-item label="順序" :label-position="labelPosition" prop="order_index">
                        <el-input v-model="addCgform.show_in_list" />
                    </el-form-item> -->

                    <el-form-item label="顯示 / 隱藏" :label-position="labelPosition" prop="show_in_list">
                        <el-radio-group v-model="addCgform.show_in_list">
                            <el-radio :value="1">顯示</el-radio>
                            <el-radio :value="0">隱藏</el-radio>
                        </el-radio-group>
                    </el-form-item>

                </el-form>
                <template #footer>
                    <el-button type="primary" @click="insertNewItem(TypeEnum.CATEGORY)">提交</el-button>
                    <el-button @click="cancelCgform">取消</el-button>
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
import { VueDraggable } from 'vue-draggable-plus';
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    category: Object
});

console.log(props.category);

const categories = reactive({ ...props.category })
const isFixed = ref(false); // 默認不固定
const isFixedStore = ref(false); // 默認不固定

const TypeEnum = {
    CATEGORY: "cat",
    SUBCATEGORY: "sub",
};

//對話框
const dialogFormToggle = ref(false);
const dialogTitle = ref();
const subcategory = ref();
const category_id = ref(null);

//查看子類別
const dialogToggle = (row) => {
    dialogFormToggle.value = !dialogFormToggle.value;
    dialogTitle.value = '類別 : ' + row.name;
    subcategory.value = row.subcategories;
    category_id.value = row.id;
    cancelNewSubRow();
}

//子類別順序調整
const isDraggable = ref(false);
const toggleDraggable = (type) => {
    switch (type) {
        case TypeEnum.CATEGORY:
            isCgDraggable.value = !isCgDraggable.value;
            break
        case TypeEnum.SUBCATEGORY:
            isDraggable.value = !isDraggable.value;
            break
    }

}

const onDragEnd = (evt, type) => {
    let data = type === TypeEnum.SUBCATEGORY ? subcategory.value : categories.data;
    console.log(data);
    

    const startIndex = evt.oldIndex;
    const endIndex = evt.newIndex;

    // 計算影響範圍的數據（startIndex 到 endIndex 之間）
    const minIndex = Math.min(startIndex, endIndex);
    const maxIndex = Math.max(startIndex, endIndex);

    // 更新範圍內的數據順序
    // const affectedRows = subcategory.value.slice(minIndex, maxIndex + 1);
    const affectedRows = data.slice(minIndex, maxIndex + 1);
    affectedRows.forEach((item, index) => {
        item.order_index = minIndex + index + 1;
    });

    reorder(affectedRows, type);
};

const reorder = async (affectedRows, type) => {
    switch (type) {
        case TypeEnum.CATEGORY:
            try {
                const response = await axios.post("/back/categories/reorder", affectedRows);
            } catch (error) {
                console.error("更新排序失敗", error);
            }
            break
        case TypeEnum.SUBCATEGORY:
            try {
                const response = await axios.post("/back/subcategories/reorder", affectedRows);
            } catch (error) {
                console.error("更新排序失敗", error);
            }
            break
    }
}


//類別順序調整
const isCgDraggable = ref(false);

//子類別按鈕
const NewSubRowButton = ref(false);
const NewSubRow = ref(false);

// 編輯子類別、類別的輸入行
const editingRow = ref(null);
const editingCgRow = ref(null);

// 臨時數據保存編輯值
const tempRow = ref({});
const tempCgRow = ref({});


//編輯模式
const toggleEdit = async (row, type) => {
    const isEditing = type === TypeEnum.CATEGORY ? editingCgRow.value === row.id : editingRow.value === row.id;

    if (isEditing) {
        await saveData(row, type); // 保存數據的邏輯
    } else {
        enterEditMode(row, type); // 進入編輯模式的邏輯
    }
};

const saveData = async (row, type) => {
    try {
        switch (type) {
            case TypeEnum.CATEGORY:
                await saveRow(tempCgRow.value, row, type);
                break
            case TypeEnum.SUBCATEGORY:
                await saveRow(tempRow.value, row, type);
                break
        }
    } catch (error) {
        console.error("保存失敗", error);
    }
};

const enterEditMode = (row, type) => {
    switch (type) {
        case TypeEnum.CATEGORY:
            editingCgRow.value = row.id;
            tempCgRow.value = { ...row };
            break
        case TypeEnum.SUBCATEGORY:
            editingRow.value = row.id;
            tempRow.value = { ...row };
            break
    }
};

// 編輯(保存)類別行數據
const saveRow = async (tempRow, originalRow, type) => {
    switch (type) {
        case TypeEnum.CATEGORY:
            try {
                const hasChanged = Object.keys(tempRow).some(
                    (key) => tempRow[key] !== originalRow[key]
                );

                if (!hasChanged) {
                    editingCgRow.value = null;
                    return; // 不發送 AJAX 請求
                }

                await formValidate(cgFormRef);
                let formData = {
                    name: tempRow.name,
                    search_key: tempRow.search_key,
                    show_in_list: tempRow.show_in_list,
                    order_index: tempRow.order_index
                }

                let category_id = tempRow.id;

                console.log(formData);
                const response = await axios.patch(`/back/categories/${category_id}`, formData);

                if (response.data) {
                    Object.assign(originalRow, tempRow); // 更新原始數據
                    editingCgRow.value = null; // 退出編輯模式
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
            break
        case TypeEnum.SUBCATEGORY:
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
            break
    }
};

//刪除類別
const deleteItem = async (id, type) => {
    try {
        const { url, list, setList, errorMessages } = getDeleteConfig(type);

        const response = await axios.delete(`${url}/${id}`);
        if (response.data.message === 'success') {
            // 更新本地數據
            setList(list.filter((item) => item.id !== id));
            showMessage("success", "刪除成功");
        } else if (response.data.message === errorMessages.exist) {
            showMessage("error", "關聯之子類別尚存在");
        } else {
            showMessage("error", "刪除失敗");
        }
    } catch (error) {
        console.error("刪除失敗", error);
        showMessage("error", "刪除失敗");
    }
};

// 配置刪除操作
const getDeleteConfig = (type) => {
    switch (type) {
        case TypeEnum.CATEGORY:
            return {
                url: '/back/categories',
                list: categories.data,
                //將新的數據列表newList賦值給categories.data
                setList: (newList) => (categories.data = newList),
                errorMessages: {
                    exist: 'subcategory exist'
                }
            };
        case TypeEnum.SUBCATEGORY:
            return {
                url: '/back/subcategories',
                list: subcategory.value,
                setList: (newList) => (subcategory.value = newList),
                errorMessages: {}
            };
        default:
            throw new Error("未知的刪除類型");
    }
};


// 取消子類別編輯模式
const cancelEdit = () => {
    editingRow.value = null;
};

// 儲存新增行的數據
const NewSubRowData = reactive({
    name: "",
    search_key: "",
    show_in_list: "",
    order_index: "",
});

//添加子類別
const labelPosition = ref('top');
const addNewSubRow = () => {
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

//新增子類別(取消)
const cancelNewSubRow = () => {
    NewSubRow.value = null;
    NewSubRowButton.value = false;

    if (newSubFormRef.value) {
        newSubFormRef.value.resetFields();
    }
}

//新增類別對話框
const dialogCgToggle = ref(false);
const addCgToggle = () => {
    dialogCgToggle.value = !dialogCgToggle.value;
    if (newCgFormRef.value) {
        newCgFormRef.value.resetFields();
    }
}

//新增類別/子類別
const insertNewItem = async (type) => {
    try {
        const isCategory = type === TypeEnum.CATEGORY;

        // 表單參考與數據源
        const formRef = isCategory ? newCgFormRef : newSubFormRef;
        const formData = isCategory
            ? {
                name: addCgform.name,
                search_key: addCgform.search_key,
                show_in_list: addCgform.show_in_list,
            }
            : {
                name: NewSubRowData.name,
                search_key: NewSubRowData.search_key,
                show_in_list: NewSubRowData.show_in_list,
            };

        const url = isCategory
            ? `/back/categories`
            : `/back/categories/${category_id.value}/subcategories`;

        const dataList = isCategory ? categories.data : subcategory.value;

        // 表單驗證
        await formValidate(formRef);

        // 發送 POST 請求
        const response = await axios.post(url, formData);

        // 檢查返回數據
        const responseData = isCategory ? response.data : response.data.data;
        if (responseData) {
            dataList.push(responseData); // 插入新數據到對應列表

            // 重置表單狀態
            if (isCategory) {
                dialogCgToggle.value = false;
            } else {
                NewSubRow.value = null;
                NewSubRowButton.value = !NewSubRowButton.value;
            }

            showMessage("success", "新增成功");
        } else {
            showMessage("error", "新增失敗");
        }
    } catch (error) {
        console.error("新增失敗", error);
        showMessage("error", "新增失敗");
    }
};

// 取消類別編輯模式
const cancelCgEdit = () => {
    editingCgRow.value = null;
}

// Popconfirm 的顯示狀態
const popconfirmVisible = ref({});

const showMessage = (type, title) => {
    ElNotification({
        type, // "success" 或 "error"
        title,
        position: 'bottom-left',
    });
};

const newSubFormRef = ref(null); // 表單引用
const newCgFormRef = ref(null); // 表單引用
const subFormRef = ref(null);
const cgFormRef = ref(null);


//新增類別之表單
const addCgform = reactive({
    name: "",
    search_key: "",
    show_in_list: "",
    order_index: "",
});

//關閉新增類別表單
const cancelCgform = () => {
    dialogCgToggle.value = false;
}

//表單驗證規則
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
    show_in_list: [
        { required: true, message: "必須選擇顯示/隱藏", trigger: "change" },
    ],
});

//表單驗證
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


function goToPage(p) {
    p = data.current_page;
    router.get('/back/categories', {
        page: p
    });
}

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

::v-deep(.new_sub_form_row) {
    display: grid;
    grid-template-columns: 220px 220px 220px 108px 200px;
    align-items: center;
    padding: 8px 0;
    /* margin-top: 10px; */

}

::v-deep(.new_sub_form_row > div) {
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

::v-deep(.el-scrollbar .el-scrollbar__wrap) {
    height: auto !important;
}

.example-pagination-block+.example-pagination-block {
    margin-top: 10px;
}

.example-pagination-block .example-demonstration {
    margin-bottom: 16px;
}

.el-pagination {
    justify-content: center;
    padding-top: 10px;
}
</style>