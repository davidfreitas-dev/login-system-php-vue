<script setup>
    const emit = defineEmits(['update:modelValue', 'onKeyupEnter'])
    
    const props = defineProps(['type', 'label', 'modelValue', 'placeholder'])

    const updateValue = (event) => {
        emit('update:modelValue', event.target.value)
    }
</script>

<template>
    <label>{{ label }}</label>
    <input
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        @input="updateValue"
        @keyup.enter="$emit('onKeyupEnter')"
    />
</template>

<style scoped>
label {
    @apply font-medium text-base
}
input {
    @apply block w-full my-2 p-3 rounded border border-solid border-dark-gray focus:border-brand focus:outline-none transition ease-in-out
}
</style>