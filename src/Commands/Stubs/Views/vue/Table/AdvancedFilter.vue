<script setup>
const props = defineProps({
  columns: Array,
  initialFilters: Object
});

const emits = defineEmits(['filter']);

const filters = ref({ ...props.initialFilters });

const applyFilter = () => {
  emits('filter', filters.value);
};
</script>



<template>
  <div class="flex space-x-4 mb-4">
    <div v-for="(column, index) in columns" :key="index" v-if="column.filterable">
      <label :for="column.key">{{ column.label }}</label>
      <input
          v-model="filters[column.key]"
          @input="applyFilter"
          type="text"
          :id="column.key"
          class="border p-2 rounded"
      />
    </div>
  </div>
</template>

