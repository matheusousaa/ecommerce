<script setup>
import { ref } from 'vue';
import { Loader2 } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    errors: Object
});

const isSubmitting = ref(false);

const form = useForm({
    name: ''
});

const submit = () => {
    isSubmitting.value = true;
    form.post('/categories', {
        onError: (errors) => {
            console.error(errors);
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};
</script>

<style>
:root {
  --color-primary: 59 130 246;
  /* blue-500 */
  --color-primary-dark: 37 99 235;
  /* blue-600 */
}

.text-primary {
  color: rgb(var(--color-primary));
}

.text-primary-dark {
  color: rgb(var(--color-primary-dark));
}

.bg-primary {
  background-color: rgb(var(--color-primary));
}

.bg-primary-dark {
  background-color: rgb(var(--color-primary-dark));
}

.border-primary {
  border-color: rgb(var(--color-primary));
}

.focus\:ring-primary:focus {
  --tw-ring-color: rgb(var(--color-primary));
}

.focus\:border-primary:focus {
  border-color: rgb(var(--color-primary));
}

.hover\:bg-primary-dark:hover {
  background-color: rgb(var(--color-primary-dark));
}

.hover\:text-primary-dark:hover {
  color: rgb(var(--color-primary-dark));
}
</style>

<template>
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
      <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Add New Category</h1>
          <p class="mt-1 text-sm text-gray-500">
            Fill in the details to add a new category
          </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
          <div class="p-6 space-y-6">
            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Category Name <span
                  class="text-red-500">*</span></label>
              <input type="text" id="name" v-model="form.name"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm"
                :class="{ 'border-red-300': errors.name }" placeholder="Enter category name" required />
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-3 rounded-b-lg">
            <a href="/categories"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Cancel</a>
            <button type="submit"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
              :disabled="isSubmitting">
              <Loader2 v-if="isSubmitting" class="animate-spin h-4 w-4 mr-2" />
              {{ isSubmitting ? 'Creating...' : 'Create Category' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>