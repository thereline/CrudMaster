<script setup>
import { computed, toRefs, watch } from 'vue';

const props = defineProps({
    field: Object,
    modelValue: [String, Number, Boolean],
    error: String
});

const emits = defineEmits(['update:modelValue']);

const fieldClasses = computed(() => [
    'col-span-1',
    `row-span-${props.field.rowSpan || 1}`,
    `col-span-${props.field.colSpan || 1}`,
    'border p-2 rounded',
    `row-start-${props.field.row + 1}`,
    `col-start-${props.field.col + 1}`,
]);

const { modelValue } = toRefs(props);

const updateValue = (value) => {
    emits('update:modelValue', value);
};

watch(modelValue, updateValue);
</script>


<template>
    <div :class="fieldClasses">
        <label :for="field.name" class="block text-sm font-medium text-gray-700">{{ field.label }}</label>
        <component
            :is="field.type"
            v-model="modelValue"
            :id="field.name"
            :name="field.name"
            :placeholder="field.label"
            :type="field.inputType"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        />
        <span v-if="error" class="text-red-600 text-sm">{{ error }}</span>
    </div>
</template>


