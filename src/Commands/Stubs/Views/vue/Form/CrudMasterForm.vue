<script setup>
import {ref, reactive, computed} from 'vue';
import {useField, useForm} from 'vee-validate';
import * as yup from 'yup';
import {usePage} from '@inertiajs/inertia-vue3';
import FormField from './FormField.vue';
import LoadingButton from './LoadingButton.vue';
import formSchema from './formSchema.sample.json';

const props = defineProps({
    initialValues: Object
});

const formValues = reactive({...props.initialValues});
const fields = ref(formSchema.fields);
const loading = ref(false);

const validationSchema = computed(() => {
    const schema = {};
    fields.value.forEach(field => {
        if (field.required) {
            schema[field.name] = field.inputType === 'email' ? yup.string().email().required() : yup.string().required();
        } else {
            schema[field.name] = yup.string();
        }
    });
    return yup.object(schema);
});

const {handleSubmit, errors} = useForm({
    validationSchema,
    initialValues: formValues,
    validateOnMount: true,
    validateOnBlur: true,
    validateOnChange: true
});

const gridClasses = computed(() => `grid grid-cols-${formSchema.initialColumns} gap-4`);

const addField = () => {
    fields.value.push({
        name: `field_${fields.value.length + 1}`,
        type: 'input',
        label: 'New Field',
        inputType: 'text',
        required: false,
        row: Math.floor(fields.value.length / formSchema.initialColumns),
        col: fields.value.length % formSchema.initialColumns,
        rowSpan: 1,
        colSpan: 1,
    });
};
</script>


<template>
    <div class="container mx-auto py-4">
        <form @submit.prevent="handleSubmit">
            <div class="grid gap-4" :class="gridClasses">
                <template v-for="(field, index) in fields" :key="index">
                    <FormField :field="field" v-model="formValues[field.name]" :error="errors[field.name]" />
                </template>
            </div>
            <div class="flex justify-between mt-4">
                <button type="button" @click="addField" class="btn-secondary">Add Field</button>
                <LoadingButton :loading="loading" type="submit">Submit</LoadingButton>
            </div>
        </form>
    </div>
</template>


<style scoped>
.container {
    padding: 2rem;
}

.btn-primary {
    @apply bg-blue-500 text-white py-2 px-4 rounded flex items-center;
}

.btn-secondary {
    @apply bg-gray-500 text-white py-2 px-4 rounded;
}
</style>
