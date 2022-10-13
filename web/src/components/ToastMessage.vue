<script setup>
    import { ref } from 'vue'
    import { TransitionRoot } from '@headlessui/vue'
    
    const props = defineProps(['toastData', 'type'])
    
    const isShowing = ref(false)
    
    const showToast = () => {
        isShowing.value = true
    }
    
    defineExpose({showToast})
</script>    

<template>
    <TransitionRoot
        :show="isShowing"
        enter="transition-opacity duration-75"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="transition-opacity duration-150"
        leave-from="opacity-100"
        leave-to="opacity-0"
    >
        <div 
            id="toast" 
            class="toast" 
            role="alert"
        >
            <div 
                class="toast-icon" 
                :class="{ 
                    'text-success bg-success': props.toastData.type === 'success', 
                    'text-danger bg-danger': props.toastData.type === 'error' 
                }"
            >
                <svg 
                    aria-hidden="true" 
                    class="w-5 h-5" 
                    fill="currentColor" 
                    viewBox="0 0 20 20" 
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path 
                        fill-rule="evenodd" 
                        :d="props.toastData.type === 'success'
                            ? 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z'
                            : 'M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z'" 
                        clip-rule="evenodd">
                    </path>
                </svg>

                <span class="sr-only">
                    Icon
                </span>
            </div>

            <div class="toast-content">
                {{ props.toastData.message }}
            </div>

            <button 
                @click="isShowing = false" 
                type="button" 
                class="close-button" 
                data-dismiss-target="#toast" 
                aria-label="Close"
            >
                <span class="sr-only">Close</span>

                <svg 
                    aria-hidden="true" 
                    class="w-5 h-5" 
                    fill="currentColor" 
                    viewBox="0 0 20 20" 
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path 
                        fill-rule="evenodd" 
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" 
                        clip-rule="evenodd">
                    </path>
                </svg>
            </button>
        </div>
    </TransitionRoot>
</template>
  
<style scoped>
.toast {
    @apply absolute top-10 -translate-x-1/2 left-1/2 flex items-center p-4 mb-4 w-full max-w-xs text-dark bg-white rounded-lg shadow-md
}
.toast-icon {
    @apply inline-flex flex-shrink-0 justify-center items-center w-8 h-8 bg-opacity-10 rounded-lg
}
.toast-content {
    @apply ml-3 text-sm font-normal
}
.close-button {
    @apply ml-auto -mx-1.5 -my-1.5 bg-white rounded-lg p-1.5 inline-flex h-8 w-8
}
</style>