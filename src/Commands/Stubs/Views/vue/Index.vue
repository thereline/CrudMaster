<script setup lang="ts">
import { ref, onMounted } from 'vue';

const items = ref([]);
const resource = 'Resource Name'; // Replace with actual resource name

const fetchItems = async () => {
    // Add your fetch logic here
};

onMounted(fetchItems);
</script>


<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">List of {{ resource }}</h1>
    <table class="min-w-full leading-normal">
      <thead>
        <tr>
          <!-- Add your table headers here -->
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.id">
          <!-- Add your table data here -->
        </tr>
      </tbody>
    </table>
  </div>
</template>


<style scoped>
/* Add custom styles or import Tailwind CSS */
</style>

