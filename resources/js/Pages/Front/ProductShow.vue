<template>
    <FrontendLayout>
        <template #switch>
            <section>
                <div style="max-width: 1200px; margin: auto;">

                    <div>
                        <!-- 主要圖片 -->
                        <el-image :src="selectedImage" style="width: 400px; height: 400px;" />

                        <!-- 縮圖列表 -->
                        <div class="thumbnail-container">
                            <img v-for="(image, index) in filteredThumbnails" :key="index" :src="image.image"
                                class="thumbnail" @click="selectedImage = image.image">
                        </div>

                        <!-- 顏色選擇 -->
                        <div class="color-options">
                            <span v-for="color in productOptions" :key="color.id" class="color-box"
                                :class="{ active: selectedColor === color.color_code }" @click="changeColor(color)">
                                {{ color.color_name }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>
        </template>
    </FrontendLayout>
</template>


<script setup>
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    productOptions: {
        type: Array,
        required: true
    },
})
const productOptions = computed(() => {
    return props.productOptions.filter(e => e.color_name !== "組合色")
})
const selectedImage = ref(productOptions.value[0]?.product_images[0]?.image || ''); // 預設為第一個顏色的第一張圖


// 預設選擇的顏色
const selectedColor = ref(null);

//初次載入時拿所有縮圖
const allThumbnails = computed(() => {
    return productOptions.value.flatMap(option => option.product_images);
});


// **當使用者選擇顏色時，顯示對應的 `product_images`**
const filteredThumbnails = computed(() => {
    if (!selectedColor.value) {
        return allThumbnails.value; // 預設顯示所有縮圖
    }
    // console.log(productOptions.value);
    
    const colorOption = productOptions.value.find(option => option.color_code === selectedColor.value);
    return colorOption ? colorOption.product_images : [];
});

// **切換顏色時**
const changeColor = (color) => {
    selectedColor.value = color.color_code;
};

</script>

<style scoped>
.thumbnail-container {
    display: flex;
    gap: 10px;
}

.thumbnail {
    width: 50px;
    height: 50px;
    cursor: pointer;
    border: 2px solid transparent;
}

.thumbnail.active {
    border: 2px solid blue;
}

.color-box {
    /* width: 30px; */
    /* height: 30px; */
    display: inline-block;
    cursor: pointer;
    border-radius: 5px;
    margin-right: 10px;
    border: 2px solid transparent;
    padding: 3px;
}

.color-box.active {
    border: 2px solid rgb(169, 169, 169);
}
</style>