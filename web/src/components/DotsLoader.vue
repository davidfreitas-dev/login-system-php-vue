<template>
    <div class="loader-wrapper" style="margin: 0 !important;">
        <div class="loader" style="margin: 0 !important;">
            <div class="dots"></div>
            <div class="dots"></div>
            <div class="dots"></div>
        </div>
    </div>
</template>

<style scoped>
.loader-wrapper {
    @apply flex justify-center items-center h-7
}
.loader {
    @apply rounded-full flex space-x-2
}
.dots {
    @apply w-2.5 h-2.5 bg-white rounded-full animate-bounce
}
.loader div {
  animation-duration: 0.5s;
}
.loader div:first-child {
  animation-delay: 0.1s;
}
.loader div:nth-child(2) {
  animation-delay: 0.3s;
}
.loader div:nth-child(3) {
  animation-delay: 0.6s;
}
</style>