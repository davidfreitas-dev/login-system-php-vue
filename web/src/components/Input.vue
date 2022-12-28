<script setup>
  import { UserIcon, EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/24/outline'

  const emit = defineEmits(['update:modelValue', 'onKeyupEnter'])
  
  const props = defineProps(['type', 'label', 'modelValue', 'placeholder'])

  const updateValue = (event) => {
    emit('update:modelValue', event.target.value)
  }
</script>

<template>
  <label>{{ label }}</label>
  <div>
    <UserIcon v-if="type === 'text'" class="h-6 w-6"/>
    <EnvelopeIcon v-if="type === 'email'" class="h-6 w-6"/>
    <LockClosedIcon v-if="type === 'password'" class="h-6 w-6"/>
    <input
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      @input="updateValue"
      @keyup.enter="$emit('onKeyupEnter')"
    />
  </div>
</template>

<style scoped>
label {
  @apply font-medium text-base
}
div {
  @apply flex items-center gap-3 mt-2 py-4 px-3 rounded w-full bg-light focus-within:ring-2 ring-brand transition ease-in-out
}
input {
  @apply bg-transparent flex-1 outline-none placeholder:text-dark
}
</style>