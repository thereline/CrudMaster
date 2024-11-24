<script setup>
import { ref } from 'vue';
import { DragulaService } from 'vue3-dragula';
import resizeColumn from '../../common/resizeColumn'; // Import the directive

const dragula = new DragulaService();

const props = defineProps({
    columns: Array,
    sortKey: String,
    sortOrder: String
});

const emits = defineEmits(['sort', 'toggleVisibility', 'reorderColumns']);

const sort = (column) => {
    emits('sort', column);
};

const toggleVisibility = (key) => {
    emits('toggleVisibility', key);
};

const pinColumn = (index) => {
    emits('pinColumn', index);
};


//for drag and drop features
const dragulaOptions = {
    moves(el, container, handle) {
        return handle.classList.contains('drag-handle');
    },
    accepts(el, target, source, sibling) {
        return true;
    }
};

dragula.options('dragula-bag', dragulaOptions);

dragula.on('drop', (el, target, source, sibling) => {
    const oldIndex = el.getAttribute('data-index');
    const newIndex = sibling ? sibling.getAttribute('data-index') : columns.length - 1;
    emits('reorderColumns', { oldIndex, newIndex });
});

// Register the directive
const directives = {
    resizeColumn
};
</script>



<template>
    <table class="min-w-full bg-white border">
        <thead>
        <tr>
            <th v-for="(column, index) in columns" :key="index" v-resize-column>
          <span @click="() => sort(column)">
            {{ column.label }}
            <span v-if="sortKey === column.key && sortOrder === 'asc'">▲</span>
            <span v-if="sortKey === column.key && sortOrder === 'desc'">▼</span>
          </span>
                <button @click="() => toggleVisibility(column.key)">
                    {{ column.visible ? 'Hide' : 'Show' }}
                </button>
                <button @click="() => pinColumn(index)">
                    {{ column.pinned ? 'Unpin' : 'Pin' }}
                </button>
            </th>
        </tr>
        </thead>
    </table>
</template>


<style scoped>
.resizer {
    width: 5px;
    height: 100%;
    background: gray;
    position: absolute;
    right: 0;
    top: 0;
    cursor: col-resize;
    user-select: none;
}

.drag-handle {
    cursor: grab;
}
</style>
