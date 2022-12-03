<script setup>
    const props = defineProps(['text'])
</script>

<template>
    <h1>{{ text }}</h1>
</template>

<style scoped>
h1 {
    @apply text-4xl font-bold text-dark
}
</style>