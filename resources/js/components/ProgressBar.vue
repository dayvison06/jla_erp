<script setup lang="ts">
const props = defineProps<{
    progress: number
    visible?: boolean
}>()
console.log('ProgressBar props:', props)
</script>

<template>
<div
  v-if="visible"
  class="fixed p-6 bg-white rounded-lg shadow-lg inset-0 flex z-100 items-center justify-center"
  :class="{ 'animate-curtain': visible }"
>
        <div class="flex flex-col items-center gap-3">
            <svg
                width="120"
                height="120"
                viewBox="0 0 100 100"
                xmlns="http://www.w3.org/2000/svg"
            >
                <!-- círculo de fundo -->
                <circle
                    cx="50"
                    cy="50"
                    r="46"
                    stroke="currentColor"
                    stroke-opacity="0.2"
                    stroke-width="8"
                    fill="none"
                />

                <!-- parte giratória -->
                <g>
                    <path
                        d="M50 4a46 46 0 0 1 41.5 24"
                        fill="none"
                        stroke="#cc2127"
                        stroke-width="8"
                        stroke-linecap="round"
                    >
                        <animateTransform
                            attributeName="transform"
                            type="rotate"
                            from="0 50 50"
                            to="360 50 50"
                            dur="1s"
                            repeatCount="indefinite"
                        />
                    </path>
                </g>
                <!-- o “J” no centro -->
                <g transform="translate(25,25) scale(0.40)" fill="#1D1D1D">
                    <path
                        d="M63.8616 36.7695C79.3868 36.263 95.0538 36.7191 110.596 36.6973C108.341 45.2108 105.019 53.4501 102.017 61.7243C95.5198 79.6321 90.9868 96.7503 73.3598 106.973C69.6268 109.137 65.6928 110.662 61.6179 112.052C43.6917 118.169 16.175 115.51 8.0875 115.066C0 114.622 8.0875 115.066 0 114.622C0.5083 111.52 1.6613 108.557 2.6741 105.592C4.8612 99.1903 8.0042 91.3303 9.5237 84.8667C15.1594 85.7056 9.5237 84.8667 15.2496 85.7056C22.5084 86.2693 30.4437 86.4133 37.6545 85.2776C43.1687 83.4966 47.3432 81.1889 50.0697 75.8573C52.1475 71.7941 53.4176 67.2775 54.8627 62.9598C56.7098 57.419 58.6083 51.896 60.5584 46.3907C61.6543 43.1961 62.5948 39.8992 63.8616 36.7695Z"
                    />
                    <path
                        d="M110.72 0.1361C114.99 -0.0557002 119.306 0.0306 123.582 0C120 10 123.582 0 114.093 28.9575C102.802 28.7544 91.5108 28.6775 80.2188 28.7267C75.6858 28.6902 95.5 28.7371 65 28.7267C66.629 22.7717 71.5798 16.5926 75.7908 12.0694C84.7768 2.4172 98.1868 0.5817 110.72 0.1361Z"
                    />
                </g>
            </svg>
            <span class="animate-pulse text-xl items-center justify-center font-extrabold text-gray-700">{{ progress }}%</span>
        </div>
    </div>
</template>

<style scoped>
@keyframes curtain-fall {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-curtain {
    animation: curtain-fall 0.3s ease-out;
}
</style>
