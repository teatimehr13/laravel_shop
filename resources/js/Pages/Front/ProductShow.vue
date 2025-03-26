<template>
    <FrontendLayout>
        <template #switch>
            <Breadcrumb :category="category" :subcategory="subcategory" :product="product" />

            <section>
                <div class="layout-container">
                    <div class="product-show">
                        <div class="product-img-con">
                            <div class="prodcut-main-img">
                                <!-- 主要圖片 -->
                                <el-image :src="selectedImage" style="width: 400px; height: 400px;" />
                            </div>

                            <!-- 縮圖列表 -->
                            <div class="thumbnail-container">
                                <img v-for="(image, index) in filteredThumbnails" :key="index" :src="image.image"
                                    class="thumbnail" @click="selectedImage = image.image"
                                    :class="{ active: selectedImage === image.image }">
                            </div>
                        </div>

                        <div class="product-details-con">
                            <div>
                                <h1 class="product-title">
                                    <strong>
                                        {{ product.name }}
                                    </strong>
                                </h1>

                                <div v-if="isInEventPeriod(product.special_start_at, product.special_end_at)">
                                    <div v-html="product.special_message" class="special_msg product-describe"></div>
                                </div>

                                <div>
                                    <div v-html="product.description" class="product-describe"></div>
                                </div>

                                <hr>

                                <div>
                                    <!-- 顏色選擇 -->
                                    <div style="display: block; margin-bottom: 10px;">
                                        <span style="margin-right: 10px;">
                                            <strong>顏色</strong>
                                        </span>
                                        <span>
                                            {{ selectedColorName }}
                                        </span>
                                    </div>

                                    <div class="color-options color-cube-outner color-box"
                                        v-for="option in productOptions"
                                        :class="{ active: selectedColor === option.color_code }"
                                        :style="{ '--border-color': option.color_code }" :key="option.id"
                                        @click="changeColor(option)" @mouseenter="changeBigImage(option)">
                                        <span class="color-cube ">
                                            <span class="color-cube-inner"
                                                :style="{ backgroundColor: option.color_code }">
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <div class="product-text-content">
                                    <div class="price-text">
                                        優惠價: <span>{{ toCurrency(product.price) }}</span>
                                    </div>
                                </div>

                                <div class="button-group">
                                    <el-button color="#626aef" size="large" class="addTocart"
                                        @click="addTOCart">加入購物車</el-button>
                                    <el-button size="large" class="addTocart" @click="testCart">測試</el-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>
    </FrontendLayout>
</template>


<script setup>
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import { computed, ref, onMounted, reactive } from 'vue';
import Breadcrumb from './Component/Breadcrumb.vue';
import axios from 'axios';

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    productOptions: {
        type: Array,
        required: true
    },
    category: {
        type: Object
    },
    subcategory: {
        type: Object
    }
})

console.log(props.product);
console.log(props.category);
console.log(props.subcategory);




const productOptions = computed(() => {
    return props.productOptions.filter(e => e.color_name !== "組合色")
})
const selectedImage = ref(productOptions.value[0]?.product_images[0]?.image || ''); // 預設為第一個顏色的第一張圖
console.log(productOptions.value[0]);


// 預設選擇的顏色
// const selectedColor = ref(productOptions.value[0]?.color_code);
const selectedColor = ref(null);
const selectedColorName = ref(null);
const selectedProductOptionId = ref(null);

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
    console.log(color);
    // console.log(color.product_images[0].image);
    // console.log(selectedImage.value);

    selectedImage.value = color.product_images.length ? color.product_images[0].image : '';
    selectedColor.value = color.color_code;
    selectedColorName.value = color.color_name;
    selectedProductOptionId.value = color.id;
    // console.log(selectedProductOptionId.value);

};

const changeBigImage = (color) => {
    // console.log(color);
    selectedImage.value = color.product_images.length ? color.product_images[0].image : '';
}


function toCurrency(num) {
    if (!num && num !== 0) return "$"; // 避免 null 或 undefined
    return `NT$${Number(num).toLocaleString("en-US")}`; // 確保是數字再轉換
}

function isInEventPeriod(startAt, endAt) {
    const now = new Date();
    const start = new Date(startAt);
    const end = new Date(endAt);

    return now >= start && now <= end;
}


const productOptionCartData = reactive({
    quantity: 1,
    id: null
});

const addTOCart = async () => {
    try {
        productOptionCartData.id = selectedProductOptionId.value;
        if (productOptionCartData.id) {
            const response = await axios.post('/cart/addToCart', productOptionCartData);
            // console.log(response);
            await msg_feedback(response.data.msg, 'success')
        } else {
            await msg_feedback('請選擇商品規格', 'info')
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
    }
}

const testCart = async () => {
    try {
        console.log(123);
        // const response = await axios.get('/cart/getCartFromCookie');
        const response = await axios.get('/cart');
        
        console.log(response.data);
        // await msg_feedback(response.data.msg, 'success')

    } catch {

    }
}

const msg_feedback = (msg, type) => {
    ElMessage({
        message: msg,
        type: type,
        plain: true,
    })
}
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
    /* border-radius: 5px; */
    margin-right: 10px;
    border: 2px solid transparent;
    padding: 3px;
}

.color-box.active {
    /* border: 2px solid var(--border-color);  */
    /* border: 2px solid #000000;  */
    border: 2px solid color-mix(in srgb, var(--border-color) 85%, black);
}

.product-show {
    grid-column: aside / right-space;
    display: grid;
    grid-template-columns: 1fr 1fr;
}

::v-deep(.product-describe>ul) {
    list-style-type: square;
    padding-left: 1rem;
}

.product-describe>ul>li {}

.product-title {
    position: relative;
    display: block;
    margin-bottom: 1.25rem;
    word-break: break-all;
    font-size: 2rem;
}

.product-img-con {}

.prodcut-main-img {
    border: 1px solid #e4e7ec;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    cursor: pointer;
    overflow: hidden;
    display: flex;
    justify-content: center;
    margin-bottom: .75rem;
}

.product-details-con {
    padding-left: 3rem;
}

.product-details-con hr {
    clear: both;
    max-width: 105rem;
    height: 0;
    margin: 1.25rem auto;
    border-top: 0;
    border-right: 0;
    border-bottom: 1px solid #e5e7eb;
    border-left: 0;
}

.price-text {
    color: #666666;
    padding: 5px 0;
}

.price-text span {
    color: #282828;
    font-size: 2.25rem;
    font-weight: bold;
}

.product-text-content {
    margin-top: 1rem;
    margin-bottom: 1.125rem;
}

.color-cube-outner {
    /* border: 1px solid var(--el-border-color); */
    /* border-radius: 4px; */
    box-sizing: border-box;
    display: inline-flex;
    font-size: 0;
    height: 32px;
    justify-content: center;
    padding: 4px;
    position: relative;
    width: 32px;
    transition: transform 0.3s ease;
}

.color-cube {
    border: 1px solid var(--el-text-color-secondary);
    border-radius: var(--el-border-radius-small);
    box-sizing: border-box;
    display: block;
    height: 100%;
    position: relative;
    text-align: center;
    width: 100%;
}

.color-cube-inner {
    align-items: center;
    display: inline-flex;
    height: 100%;
    justify-content: center;
    width: 100%;
}

.special_msg {
    color: #bd0000;
}

.addTocart {
    font-size: 1.25rem;
    padding: 25px 40px;
}
</style>