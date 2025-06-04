<template>
    <div class="hello__div">
        <svg class="hello__svg" viewBox="0 0 1230.94 414.57">
            <defs>
                <linearGradient id="helloGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="hsl(211, 66%, 87%)" />
                    <stop offset="50%" stop-color="hsl(348, 67%, 88%)" />
                    <stop offset="100%" stop-color="hsl(272, 26%, 72%)" />
                </linearGradient>
            </defs>
            <path ref="helloPath"
                d="M-293.58-104.62S-103.61-205.49-60-366.25c9.13-32.45,9-58.31,0-74-10.72-18.82-49.69-33.21-75.55,31.94-27.82,70.11-52.22,377.24-44.11,322.48s34-176.24,99.89-183.19c37.66-4,49.55,23.58,52.83,47.92a117.06,117.06,0,0,1-3,45.32c-7.17,27.28-20.47,97.67,33.51,96.86,66.93-1,131.91-53.89,159.55-84.49,31.1-36.17,31.1-70.64,19.27-90.25-16.74-29.92-69.47-33-92.79,16.73C62.78-179.86,98.7-93.8,159-81.63S302.7-99.55,393.3-269.92c29.86-58.16,52.85-114.71,46.14-150.08-7.44-39.21-59.74-54.5-92.87-8.7-47,65-61.78,266.62-34.74,308.53S416.62-58,481.52-130.31s133.2-188.56,146.54-256.23c14-71.15-56.94-94.64-88.4-47.32C500.53-375,467.58-229.49,503.3-127a73.73,73.73,0,0,0,23.43,33.67c25.49,20.23,55.1,16,77.46,6.32a111.25,111.25,0,0,0,30.44-19.87c37.73-34.23,29-36.71,64.58-127.53C724-284.3,785-298.63,821-259.13a71,71,0,0,1,13.69,22.56c17.68,46,6.81,80-6.81,107.89-12,24.62-34.56,42.72-61.45,47.91-23.06,4.45-48.37-.35-66.48-24.27a78.88,78.88,0,0,1-12.66-25.8c-14.75-51,4.14-88.76,11-101.41,6.18-11.39,37.26-69.61,103.42-42.24,55.71,23.05,100.66-23.31,100.66-23.31"
                transform="translate(311.08 476.02)" :style="{
                    fill: 'none',
                    stroke: 'url(#helloGradient)',
                    strokeLinecap: 'round',
                    strokeMiterlimit: 10,
                    strokeWidth: '35px',
                    strokeDasharray: dashArray,
                    strokeDashoffset: dashOffset,
                    transition: pathTransition,
                }" />
        </svg>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'



const helloPath = ref(null)
const dashArray = ref('5800px')
const dashOffset = ref('5800px')
const pathTransition = ref('none')

// 動畫時長 & 間隔
const animDuration = 5 // 秒
const interval = 2     // 間隔時間（秒）

function playAnim() {
    // 1. 回到初始（隱藏），瞬間取消動畫
    dashOffset.value = dashArray.value
    pathTransition.value = 'none'
    // 2. 微延遲再啟動動畫，避免transition無效
    setTimeout(() => {
        pathTransition.value = `stroke-dashoffset ${animDuration}s cubic-bezier(0.4,0,0.2,1)`
        dashOffset.value = '0px'
    }, 50)
}

onMounted(() => {
    // 自動根據 path 長度設 dasharray
    if (helloPath.value) {
        const len = helloPath.value.getTotalLength()
        dashArray.value = `${len}px`
        dashOffset.value = `${len}px`
    }
    playAnim()
    setInterval(() => {
        playAnim()
    }, (animDuration + interval) * 1000)
})
</script>

<style scoped>
.hello__div {
    display: flex;
    justify-content: center;
    flex-direction: column;
    margin: 0 auto;
    text-align: center;
    width: 100%;
    max-width: 140px;
}

.hello__svg {
    fill: none;
    stroke: #fff;
    stroke-linecap: round;
    stroke-miterlimit: 10;
    stroke-width: 48px;
    stroke-dasharray: 5800px;
    stroke-dashoffset: 5800px;
    animation: anim__hello linear 5s forwards;
    width: 100%;
    display: flex;
    justify-content: center;
    flex-direction: column;
    margin: 0 auto;
    text-align: center;
}

/* @keyframes anim__hello {
    0% {
        stroke-dashoffset: 5800;
    }

    25% {
        stroke-dashoffset: 5800;
    }

    100% {
        stroke-dashoffset: 0;
    }
} */
</style>