<script setup>
import Papa from 'papaparse';
import { ref, reactive, computed } from 'vue';
import { debounce } from 'lodash'; // Import debounce from lodash
import { usePage } from '@inertiajs/inertia-vue3';
import VirtualScroller from 'vue-virtual-scroller';
import 'vue-virtual-scroller/dist/vue-virtual-scroller.css';

import TableHeader from './TableHeader.vue';
import TableBody from './TableBody.vue';
import Pagination from './Pagination.vue';
import AdvancedFilter from "./AdvancedFilter.vue";

const props = defineProps({
    initialData: Array,
    initialColumns: Array
});

/**
 *  $columns = [
 *         ['key' => 'id', 'label' => 'ID'],
 *         ['key' => 'name', 'label' => 'Name'],
 *         // Add more columns as needed
 *     ];
 *
 *     return Inertia::render('AdvancedTable', [
 *         'initialData' => $data,
 *         'initialColumns' => $columns
 *     ]);
 */


const columns = reactive(props.initialColumns.map(col => ({ ...col, visible: true })));
const data = ref(props.initialData);
const searchQuery = ref('');
const debouncedFilterData = debounce(filterData, 300);
const sortKey = ref('');
const sortOrder = ref('asc');
const currentPage = ref(1);
const perPage = ref(10);
const isDarkMode = ref(false);

const filteredData = computed(() => {
    let filtered = data.value;

    if (searchQuery.value) {
        filtered = filtered.filter(row =>
            columns.some(col => row[col.key].toString().toLowerCase().includes(searchQuery.value.toLowerCase()))
        );
    }

    if (sortKey.value) {
        filtered.sort((a, b) => {
            let result = 0;
            if (a[sortKey.value] > b[sortKey.value]) result = 1;
            if (a[sortKey.value] < b[sortKey.value]) result = -1;
            return sortOrder.value === 'asc' ? result : -result;
        });
    }

    return filtered;
});

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return filteredData.value.slice(start, end);
});

const filterData = () => {
    filteredData.value;
    currentPage.value = 1; // Reset to first page on filter
};

const sortColumn = column => {
    if (sortKey.value === column.key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = column.key;
        sortOrder.value = 'asc';
    }
};

const toggleColumnVisibility = key => {
    const column = columns.find(col => col.key === key);
    if (column) {
        column.visible = !column.visible;
    }
};

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
};

const addRow = () => {
    data.value.push({});
};

const printTable = () => {
    window.print();
};

const handlePageChange = (page) => {
    currentPage.value = page;
};

const exportTable = () => {
    const csv = Papa.unparse(data.value);
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.href = url;
    link.setAttribute('download', 'table.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

const importTable = (event) => {
    const file = event.target.files[0];
    if (file) {
        Papa.parse(file, {
            complete: (results) => {
                data.value = results.data;
            },
            header: true
        });
    }
};

const pinColumn = (index) => {
    const column = columns[index];
    column.pinned = !column.pinned;
};

const pinnedColumns = computed(() => {
    return [
        ...columns.filter(col => col.pinned),
        ...columns.filter(col => !col.pinned)
    ];
});

const columns = reactive(props.initialColumns.map(col => ({ ...col, visible: true })));

</script>

<template>
    <div :class="{'dark': isDarkMode}" class="container mx-auto py-4">
        <div class="flex justify-between items-center mb-4">
            <input
                v-model="searchQuery"
                @input="debouncedFilterData"
                type="text"
                placeholder="Search..."
                class="border p-2 rounded"
            />
            <div>
                <button @click="addRow" class="btn-primary">Add</button>
                <button @click="printTable" class="btn-secondary">Print</button>
                <button @click="exportTable" class="btn-secondary">Export</button>
                <input type="file" @change="importTable" class="btn-secondary" />
                <button @click="toggleDarkMode" class="btn-secondary">Toggle Dark Mode</button>
            </div>
        </div>
        <div>
            <label v-for="(column, index) in columns" :key="index">
                <input type="checkbox" v-model="columns[index].visible" />
                {{ column.label }}
            </label>
        </div>
        <TableHeader :columns="columns" @sort="sortColumn" @toggleVisibility="toggleColumnVisibility" @pinColumn="pinColumn" :sortKey="sortKey" :sortOrder="sortOrder" />
        <virtual-scroller :items="paginatedData" :item-height="50" class="table-body">
            <template #default="{ item: row, index: rowIndex }">
                <TableBody :columns="columns" :data="[row]" :rowIndex="rowIndex" />
            </template>
        </virtual-scroller>
        <AdvancedFilter :columns="columns" :initialFilters="filters" @filter="applyFilter" />
        <TableBody :columns="columns" :data="paginatedData" />
        <Pagination :total="filteredData.length" :current="currentPage" :per-page="perPage" @page-changed="handlePageChange" />
    </div>
</template>


<style scoped>
.container {
    padding: 2rem;
}

.btn-primary {
    @apply bg-blue-500 text-white py-2 px-4 rounded;
}

.btn-secondary {
    @apply bg-gray-500 text-white py-2 px-4 rounded ml-2;
}
</style>


