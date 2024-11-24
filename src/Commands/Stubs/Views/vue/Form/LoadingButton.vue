<template>
    <button :type="type" :disabled="loading" class="btn-primary flex items-center">
        <span v-if="loading" class="loader mr-2"></span>
        <slot></slot>
    </button>
</template>

<script setup>
const props = defineProps({
    loading: Boolean,
    type: String
});
</script>

<style scoped>
.btn-primary {
    @apply bg-blue-500 text-white py-2 px-4 rounded;
}

.btn-primary:disabled {
    @apply opacity-50 cursor-not-allowed;
}

.loader {
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-left-color: white;
    border-radius: 50%;
    width: 1rem;
    height: 1rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
