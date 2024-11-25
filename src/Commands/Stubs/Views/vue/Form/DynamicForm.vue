
/*

use Inertia\Inertia;

public function create()
{
$fields = [
['name' => 'username', 'label' => 'Username', 'component' => 'Input', 'colSpan' => 2, 'required' => true],
['name' => 'email', 'label' => 'Email', 'component' => 'Input', 'colSpan' => 2, 'required' => true],
['name' => 'password', 'label' => 'Password', 'component' => 'Input', 'colSpan' => 2 , 'required' => true],
['name' => 'role', 'label' => 'Role', 'component' => 'Select', 'colSpan' => 1, 'options' => ['Admin', 'User '], 'defaultValue' => 'User '],
];

return Inertia::render('YourComponent', [
'fields' => $fields,
]);
}

*/
<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    fields: {
        type: Array,
        required: true,
    },
});

const formData = ref({});
const errors = ref({});

// Initialize form data and errors
const initializeForm = () => {
    props.fields.forEach(field => {
        formData.value[field.name] = field.defaultValue || '';
        errors.value[field.name] = '';
    });
};

// Validate field based on rules
const validateField = (field) => {
    if (field.required && !formData.value[field.name]) {
        errors.value[field.name] = `${field.label} is required.`;
    } else {
        errors.value[field.name] = '';
    }
};

// Handle form submission
const handleSubmit = () => {
    // Validate all fields
    props.fields.forEach(field => validateField(field));

    // Check for errors
    if (Object.values(errors.value).some(error => error)) {
        console.log('Validation errors:', errors.value);
        return;
    }

    // Submit the form data (you can replace this with an Inertia post request)
    console.log('Form submitted:', formData.value);
};

// Compute grid style based on column spans
const gridStyle = computed(() => {
    const columns = props.fields.reduce((acc, field) => acc + (field.colSpan || 1), 0);
    return {
        gridTemplateColumns: `repeat(${columns}, minmax(0, 1fr))`,
    };
});

// Initialize form on component mount
initializeForm();
</script>

<template>
    <div class="p-4">
        <form @submit.prevent="handleSubmit">
            <div class="grid" :style="gridStyle">
                <div v-for="(field, index) in fields" :key="index" :class="`col-span-${field.colSpan || 1}`">
                    <label :for="field.name" class="block text-sm font-medium text-gray-700">{{ field.label }}</label>
                    <component
                        :is="field.component"
                        v-model="formData[field.name]"
                        v-bind="field.props"
                        :id="field.name"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50"
                        @blur="validateField(field)"
                    />
                    <p v-if="errors[field.name]" class="text-red-500 text-sm">{{ errors[field.name] }}</p>
                </div>
            </div>
            <button type="submit" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded">Submit</button>
        </form>
    </div>
</template>


<style scoped>
.grid {
    display: grid;
    gap: 1rem;
}
</style>
