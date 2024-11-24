<script setup lang="ts">
import { ref } from 'vue';

const resource = 'Resource Name'; // Replace with actual resource name

const createItem = async () => {
    // Add your create logic here
};
</script>


<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Create New {{ resource }}</h1>
    <form @submit.prevent="createItem">
      <!-- Add your form fields here -->

      <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Create</button>
    </form>
  </div>
</template>



<style scoped>
/* Add custom styles or import Tailwind CSS */
</style>

