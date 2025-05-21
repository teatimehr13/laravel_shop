<template>
    <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-[100%]"
        enter-to-class="opacity-100 translate-y-0">
        <div v-if="open" class="fixed inset-0 z-40 bg-black z-10" @click="toggleHam">
            <div class="fixed top-0 left-0 w-full h-screen bg-white shadow-lg" @click.stop>
                <div class="bg-stone-600 w-full p-4">
                    <div class="flex justify-between align-middle text-white">
                        <div>
                            產品分類
                        </div>
                        <div>
                            <el-icon size="22" class="align-middle cursor-pointer" @click="toggleHam">
                                <Close />
                            </el-icon>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="px-4 py-2">
                        <ul class="relative" v-for="(categoryList, idx) in categoryLists">
                            <li class="product-list-item py-2 relative"
                                :class="{ is_active: currentRoute === `/product/list/${categoryList.search_key}` }">
                                <Link :href="route('product.front.index', categoryList.search_key)" class="hover:text-blue-600 align-middle">
                                {{ categoryList.name }}
                                <el-icon v-if="currentRoute === `/product/list/${categoryList.search_key}`"
                                    class="arrow-right">
                                    <ArrowRightBold />
                                </el-icon>
                                </Link>

                                <noscript>
                                    <a :href="`/product/list/${categoryList.search_key}`" class="align-middle hover:text-blue-600 align-middle">{{ categoryList.name }}</a>
                                </noscript>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </transition>

</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, watchEffect } from 'vue';
const props = defineProps({
    categoryLists: Array
});

const open = ref(false);


const toggleHam = () => {
    open.value = !open.value
}

const currentRoute = ref(window.location.pathname);

watchEffect(() => {
    currentRoute.value = window.location.pathname;
});

defineExpose({
    toggleHam
})

</script>

<style scoped>

.is_active {
    color: #000000;
    font-weight: bold;
}


.arrow-right {
    position: absolute;
    right: 0;
    top: 50%;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    color: #626aef;
}
</style>