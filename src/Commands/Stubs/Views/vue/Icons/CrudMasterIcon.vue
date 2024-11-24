<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        required: true
    },
    size: {
        type: [String, Number],
        default: 24
    },
    color: {
        type: String,
        default: 'currentColor'
    }
});

const icons = {
    person: defineAsyncComponent(() => import('./PersonIcon.vue')),
    home: defineAsyncComponent(() => import('./HomeIcon.vue')),
    settings: defineAsyncComponent(() => import('./SettingsIcon.vue')),
    calendar: defineAsyncComponent(() => import('./CalendarIcon.vue')),
    chart: defineAsyncComponent(() => import('./ChartIcon.vue')),
    bell: defineAsyncComponent(() => import('./BellIcon.vue')),
    // Add more icons here
};

const selectedIcon = computed(() => icons[props.name] || null);

const viewBox = computed(() => {
    switch (props.name) {
        case 'person':
        case 'home':
        case 'settings':
            return '0 0 24 24';
        // Add specific viewBox sizes for different icons if needed
        default:
            return '0 0 24 24';
    }
});

const viewBox = computed(() => {
    switch (props.name) {
        case 'person':
            return '0 0 24 24';
        case 'home':
            return '0 0 24 24';
        case 'settings':
            return '0 0 24 24';
        case 'calendar':
            return '0 0 24 24';
        case 'chart':
            return '0 0 24 24';
        case 'bell':
            return '0 0 24 24';
        case 'learner':
            return '0 0 24 24';
        case 'tutor':
            return '0 0 24 24';
        case 'report':
            return '0 0 24 24';
        case 'print':
            return '0 0 24 24';


        // Add specific viewBox sizes for different icons if needed
        default:
            return '0 0 24 24';
    }
});

const svgClasses = computed(() => `icon icon-${props.name}`);
</script>


<template>
    <svg
        :class="svgClasses"
        :width="size"
        :height="size"
        :viewBox="viewBox"
        fill="currentColor"
        xmlns="http://www.w3.org/2000/svg"
    >
        <component :is="selectedIcon" />
    </svg>
</template>


<style scoped>
.icon {
    display: inline-block;
    vertical-align: middle;
}
</style>

