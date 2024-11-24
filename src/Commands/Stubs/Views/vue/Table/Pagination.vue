<script setup>
import { computed } from 'vue';

const props = defineProps({
    total: Number,
    current: Number,
    perPage: Number
});

const emits = defineEmits(['page-changed']);

const totalPages = computed(() => {
    return Math.ceil(props.total / props.perPage);
});

const currentPage = ref(props.current);

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value -= 1;
        emits('page-changed', currentPage.value);
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value += 1;
        emits('page-changed', currentPage.value);
    }
};

const pageChanged = () => {
    emits('page-changed', currentPage.value);
};
</script>



<template>
    <div class="flex justify-between items-center mt-4">
        <button @click="prevPage" :disabled="current === 1" class="btn-secondary">Previous</button>
        <span>
      Page
      <select v-model="currentPage" @change="pageChanged">
        <option v-for="page in totalPages" :key="page" :value="page">{{ page }}</option>
      </select>
      of {{ totalPages }}
    </span>
        <button @click="nextPage" :disabled="current === totalPages" class="btn-secondary">Next</button>
    </div>
</template>

<style scoped>
.btn-secondary {
    @apply bg-gray-500 text-white py-2 px-4 rounded ml-2;
}
</style>

