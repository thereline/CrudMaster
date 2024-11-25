<script setup>

const props = defineProps({
    columns: Array,
    data: Array
});

const selectedRows = reactive({});

const toggleRowSelection = (index) => {
    if (selectedRows[index]) {
        delete selectedRows[index];
    } else {
        selectedRows[index] = true;
    }
};

const editableCell = ref(null);

const editCell = (rowIndex, colIndex) => {
    editableCell.value = `${rowIndex}-${colIndex}`;
};

const saveEdit = (rowIndex, colIndex) => {
    editableCell.value = null;
};
</script>

<template>
    <table class="min-w-full bg-white border">
        <tbody>
        <tr v-for="(row, rowIndex) in data" :key="rowIndex">
            <td>
                <input type="checkbox" v-model="selectedRows[rowIndex]" @change="toggleRowSelection(rowIndex)" />
            </td>
            <td v-for="(column, colIndex) in columns" :key="colIndex" v-if="column.visible">
                <div v-if="editableCell === `${rowIndex}-${colIndex}`">
                    <input
                        type="text"
                        v-model="row[column.key]"
                        @blur="saveEdit(rowIndex, colIndex)"
                        @keyup.enter="saveEdit(rowIndex, colIndex)"
                        class="border p-1"
                    />
                </div>
                <div v-else @dblclick="editCell(rowIndex, colIndex)">
                    {{ row[column.key] }}
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</template>

