<template>
    <!-- <div class="checkout-page">
        <h2>訂單商品</h2>

        <div v-for="item in cartItems" :key="item.productOption.id" class="checkout-item">
            <img :src="item.productOption.image" alt="商品圖片" width="100" />
            <div>{{ item.productOption.name }} - {{ item.productOption.color_name }}</div>
            <div>單價：{{ formatCurrency(item.productOption.price) }}</div>
            <div>數量：{{ item.quantity }}</div>
            <div>小計：{{ formatCurrency(item.productOption.price * item.quantity) }}</div>
        </div>

        <hr />

        <h3>寄送資訊</h3>
        <label>
            姓名：<input v-model="form.name" type="text" />
        </label>
        <label>
            電話：<input v-model="form.phone" type="text" />
        </label>
        <label>
            地址：<input v-model="form.address" type="text" />
        </label>

        <label>
            備註：<input v-model="form.remark" type="text" />
        </label>

        <div class="total">
            運費：{{ formatCurrency(shippingFee) }}<br />
            總付款金額：<strong>{{ formatCurrency(totalPrice + shippingFee) }}</strong>
        </div>

        <button @click="submitOrder">下訂單</button>
    </div> -->
    <FrontendLayout />

    <div class="checkout-container">
        <div v-if="checkoutItems.length">
            <div class="checkout-con-layout">
                <div class="chk-product">訂單商品名稱</div>
                <div class="empty"></div>
                <div class="chk-price gray-font">單價</div>
                <div class="chk-qty gray-font">數量</div>
                <div class="chk-subtotal-price gray-font">總價</div>
            </div>
            <div class="checkout-con-layout" v-for="(item, idx) in checkoutItems" :key="item.productOption.id">
                <div class="chk-product">
                    <div class="chk-product-img">
                        <img :src="item.productOption.image" style="max-width: 50px;">
                    </div>
                    <div class="chk-product-name">
                        {{ item.product.name }}
                        ({{ item.productOption.color_name }})
                    </div>
                </div>
                <div class="empty"></div>
                <div class="chk-price">{{ toCurrency(item.productOption.price) }}</div>
                <div class="chk-qty">{{ item.quantity }}</div>
                <div class="chk-subtotal-price">{{ toCurrency(item.subtotal) }}</div>
            </div>

            <!-- <div class="checkout-con-layout">
                <div class="chk-total-text">
                    小計
                </div>
                <div class="chk-total-price">
                    {{ toCurrency(checkoutSubtotal) }}
                </div>
            </div> -->

            <div class="checkout-con-layout">
                <div class="chk-remark">
                    <el-input v-model="order_form.note" style="width: 100%" :rows="4" type="textarea"
                        placeholder="備註:指定收貨時間 / 管理室代收等 ..." />
                </div>

                <div class="chk-shpInfo">
                    <div class="shipInfo-contact">
                        <div>
                            <span>
                                寄送資訊
                            </span>
                            <div class="buyer-info" v-if="!editing">
                                <div>
                                    <span>
                                        賣家宅配:
                                    </span>
                                </div>
                                <div>
                                    {{ user.name }}
                                </div>
                                <div>
                                    {{ user.phone }}
                                </div>
                                <div>
                                    <span>
                                        {{ user.address }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="edit-fields buyer-info">
                                <div>
                                    <span>
                                        賣家宅配:
                                    </span>
                                </div>
                                <div class="buyer-name">
                                    <el-input v-model="user.name" placeholder="姓名" />
                                </div>
                                <div class="buyer-phone">
                                    <el-input v-model="user.phone" placeholder="電話" />
                                </div>
                                <div class="buyer-address">
                                    <el-input v-model="user.address" placeholder="地址" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <el-button type="primary" link @click="editing = !editing">
                                {{ editing ? '完成' : '變更' }}
                            </el-button>
                        </div>
                    </div>
                    <div class="shipInfo-line"></div>
                    <div class="shipInfo-invoice">
                        <div class="invoice-content1">
                            <span>電子發票</span>
                        </div>
                        <div class="invoice-content2">
                            <span>
                                二聯式發票(個人)
                            </span>
                            <span>
                                手機載具
                            </span>
                        </div>
                        <div style="display: flex; justify-content: flex-end; align-items: start;">
                            <el-button type="primary" link>
                                變更
                            </el-button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="checkout-con-layout">
                <div class="payment-text">
                    <span>付款方式</span>
                </div>
                <div class="payment-sel">
                    <div class="payment-options">
                        <label class="payment-btn " :class="{ active: paymentMethod === 'cash' }">
                            <input type="radio" name="payment" value="cash" v-model="paymentMethod" />
                            <span>貨到付款</span>
                            <i v-show="paymentMethod === 'cash'" class="checkmark">
                                <el-icon><Select /></el-icon>
                            </i>
                        </label>

                        <label class="payment-btn" :class="{ active: paymentMethod === 'credit' }">
                            <input type="radio" name="payment" value="credit" v-model="paymentMethod" />
                            <span>信用卡/金融卡</span>
                            <i v-show="paymentMethod === 'credit'" class="checkmark">
                                <el-icon><Select /></el-icon>
                            </i>

                        </label>
                    </div>
                </div>
            </div>

            <div class="checkout-con-layout" v-show="paymentMethod === 'credit'">
                <div class="payment-acc">
                    <span>選擇付款帳戶</span>
                </div>
                <div class="payment-acc-info-con">
                    <div>
                        <div class="payment-acc-info">
                            <img src="https://spm.susercontent.com/api/v4/50092786/shopee_logo_bucket/static/images/credit_card_icon_visa@3x.png"
                                alt="">
                        </div>
                        <div class="payment-acc-info2">
                            <div>
                                <span>XX國際商業銀行</span>
                                <span>**** 1234</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <el-button plain>
                            <el-icon>
                                <Plus />
                            </el-icon>
                            <span>新增信用卡付款</span>
                        </el-button>
                    </div>
                </div>
            </div>

            <div class="checkout-con-layout" style="background-color: #fafdff;">
                <div class="total-price-gp">
                    <div class="checkout-subtotal-price">
                        <span class="gray-font">
                            商品總金額
                        </span>
                        <span>
                            {{ toCurrency(checkoutSummary.subtotal) }}
                        </span>
                    </div>
                    <div class="checkout-ship-fee">
                        <span class="gray-font">
                            運費總金額
                        </span>
                        <span>
                            {{ toCurrency(checkoutSummary.shippingFee) }}
                        </span>
                    </div>
                    <div class="checkout-total-price">
                        <span class="gray-font">
                            總付款金額
                        </span>
                        <span>
                            {{ toCurrency(checkoutSummary.total) }}
                        </span>
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; margin: 12px auto;">
                <el-button color="#f56c6c" style="color: white; font-weight: bold;" size="large"
                    @click="place_order">下訂單</el-button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import { useForm, router } from '@inertiajs/vue3'

// 模擬購物車資料（你可從 props 傳入）
const props = defineProps({
    checkoutItems: {
        type: Array,
    },
    user: {
        type: Object
    },
    checkoutSummary: {
        type: Object
    }
    // checkoutSubtotal: {
    //     type: Number
    // },
    // shippingFee: {
    //     type: Number
    // },
    // checkoutTotal: {
    //     type: Number
    // }
});

console.log(props.checkoutItems);
console.log(props.checkoutSummary);

// console.log(props.checkoutSubtotal);
// console.log(props.user);
// console.log(props.shippingFee);



const order_form = reactive({
    note: '',
    address: '',
    name: '',
    phone: ''
});

function toCurrency(num) {
    if (!num && num !== 0) return "NT$"; // 避免 null 或 undefined
    return `NT$${Number(num).toLocaleString("en-US")}`; // 確保是數字再轉換
}

const paymentMethod = ref('cash');
const editing = ref(false);

const mapToOrderStatus = {
    credit: 2,
    cash: 3,
}

const place_order = () => {
    // const selectedIds = props.checkoutItems.map(item => item.productOption.id);
    // order_form.address = props.user.address;
    // order_form.name = props.user.name;
    // order_form.phone = props.user.phone;
    // order_form.selected_ids = selectedIds;
    // order_form.order_status = mapToOrderStatus[paymentMethod.value];

    const submit_form = useForm({
        address: props.user.address,
        name: props.user.name,
        phone: props.user.phone,
        note: order_form.note, // 預設空備註
        selected_ids: props.checkoutItems.map(item => item.productOption.id),
        order_status: mapToOrderStatus[paymentMethod.value]
    })

    submit_form.post(route('checkout.placeOrder'));

    // const placeOrder = () => {
    //     order_form.post(route('place_order'), {
    //         onSuccess: () => {
    //             // 成功後轉跳到訂單列表頁，或訂單完成頁
    //             router.visit(route('orders.index'))
    //         }
    //     })
    // }


    // console.log(order_form);

    // const response = await axios.post('/checkout/placeOrder', order_form);
    // console.log(response.data);

}

</script>

<style scoped>
.checkout-container {
    --grid-columns: 12;
    --offset-col-4: calc(100% / var(--grid-columns) * 4);
    --grid-span-2: span 2 / span 2;
    --grid-span-4: span 4 / span 4;
    --grid-span-8: span 8 / span 8;
    --grid-span-1: span 1 / span 1;
    --grid-span-3: span 3 / span 3;
    --grid-span-7: span 7 / span 7;
    --grid-span-5: span 5 / span 5;
}

.checkout-container {
    max-width: 1200px;
    margin: auto;
    padding: 0px 40px;
}

.checkout-con-layout {
    display: grid;
    grid-template-columns: repeat(var(--grid-columns), 1fr);
    padding: 15px 10px;
    border-bottom: .07rem solid rgba(0, 0, 0, .05);
    /* margin-bottom: 10px; */
}

.chk-product {
    grid-column: var(--grid-span-4);
    display: flex;
    align-items: center;
    gap: 10px;
}

.chk-price,
.chk-qty,
.chk-subtotal-price,
.empty,
.chk-total-price {
    grid-column: var(--grid-span-2);
    display: flex;
    justify-content: flex-end;
}

.chk-total-text {
    grid-column: 9 / 11;
}

.chk-total-price {
    grid-column: 11 / 12;
}

.chk-remark {
    grid-column: var(--grid-span-5);
    padding-inline-end: 20px;
    border-right: 1px solid #dcdfe6;
}

.chk-shpInfo {
    grid-column: var(--grid-span-7);
    margin-inline-start: 20px;
}

.shipInfo-contact {
    display: flex;
    justify-content: space-between;
}

.shipInfo-invoice {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
}

.invoice-content1 {}

.invoice-content2 {
    grid-column: 2 / 4;
    display: grid;
}

.shipInfo-line {
    border-top: 1px dashed rgba(0, 0, 0, .09);
    height: 1px;
    width: 100%;
    margin: 15px 0;
}

.payment-text {
    grid-column: 1 / 3;
    display: flex;
    align-items: center;
}

.payment-acc {
    grid-column: 1 / 3;
    display: flex;
    align-items: center;
}

.payment-sel {
    grid-column: 3 / 8;
}

.payment-options {
    display: flex;
    gap: 16px;
}

.payment-btn {
    position: relative;
    border: 1px solid #ccc;
    padding: 12px 16px;
    border-radius: 2px;
    cursor: pointer;
    user-select: none;
    color: #555;
    background: #fff;
    transition: all 0.2s ease;
}

.payment-btn input[type="radio"] {
    display: none;
}

.payment-btn:hover {
    border-color: #f56c6c;
    color: #f56c6c;
}

.payment-btn input[type="radio"]:checked+span {
    color: #f56c6c;
}

.payment-btn input[type="radio"]:checked~.checkmark {
    display: block;
}

.checkmark {
    bottom: 0;
    height: .9375rem;
    overflow: hidden;
    position: absolute;
    right: 0;
    width: .9375rem;
}

.checkmark .el-icon {
    bottom: 0;
    color: #fff;
    font-size: 8px;
    position: absolute;
    right: 0;
}

.checkmark::before {
    border: .9375rem solid transparent;
    border-bottom: .9375rem solid var(--brand-primary-color, #ee4d2d);
    bottom: 0;
    content: "";
    position: absolute;
    right: -.9375rem;
}

/* Disabled 狀態 */
.payment-btn.disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.payment-btn.active {
    border-color: #f56c6c;
}

.payment-acc-info {
    align-items: center;
    border: .0625rem solid rgba(0, 0, 0, .14);
    border-radius: .125rem;
    display: flex;
    height: 2.5rem;
    justify-content: center;
    width: 4.1875rem;
    margin-inline-end: 20px;
}

.payment-acc-info-con {
    grid-column: 3 / 6;
    display: flex;
    gap: 15px;
    flex-direction: column;
}

.payment-acc-info-con div:first-child {
    display: flex;
    align-items: center;
}

.payment-acc-info img {
    padding: 7px;
}

.total-price-gp {
    grid-column: 10 / 13;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.checkout-subtotal-price,
.checkout-ship-fee,
.checkout-total-price {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.checkout-total-price span:nth-of-type(2) {
    font-size: 1.25rem;
    color: #f56c6c;
}

.gray-font {
    color: #909399;
    font-size: .9rem;
}

.buyer-info {
    display: flex;
    gap: 5px;
}

.buyer-name {
    flex: 1;
}

.buyer-phone {
    flex: 1.25;
}

.buyer-address {
    flex: 2;
}

::v-deep(.el-input__inner:focus) {
    outline: none;
    box-shadow: none;
}
</style>