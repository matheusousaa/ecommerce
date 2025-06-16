<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Edit, Trash2 } from 'lucide-vue-next';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue';

const {category} = defineProps({
  category: Object
});

const form = useForm();
const showDeleteModal = ref(false);
const isDeleting = ref(false);

const editCategory = (id) => {
    window.location.href = `/categories/${id}/edit`;
};

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteCategory = () => {
    isDeleting.value = false;
    form.delete(`/categories/${category.id}`, {
        onSuccess: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
        },
        onError: (errors) => {
            isDeleting.value = false;
            showDeleteModal.value = false;
            alert('Erro ao excluir a categoria.');
        }
    });
};

const handleDelete = (id) => {
    if (confirm('Tem certeza que deseja excluir esta categoria?')) {
        form.delete(`/categories/${id}`, {
            onSuccess: () => {
                alert('Categoria excluída com sucesso!');
            },
            onError: (errors) => {
                console.error(errors);
                alert('Erro ao excluir a categoria.');
            }
        });
    }
};
</script>

<template>
  <!-- <div class="bg-white rounded-lg p-4 shadow-md">
    <h2 class="text-lg font-bold">{{ category.name }}</h2>
    <p class="text-gray-600">ID: {{ category.id }}</p>
    <Link :href="`/categories/${category.id}/edit`" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 inline-block">
      Editar
    </Link>
    <button @click="handleDelete(category.id)" class="mt-2 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
      Excluir
    </button>
  </div> -->

  <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-4">
          <h2 class="text-lg font-bold">{{ category.name }}</h2>
          <p class="text-gray-600">ID: {{ category.id }}</p>
          <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ category.description }}</p>
          <div class="mt-4 flex justify-between">
              <button @click="editCategory(category.id)"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                  <Edit class="h-4 w-4 mr-1" />
                  Edit
              </button>
              <button @click="confirmDelete"
                  class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                  <Trash2 class="h-4 w-4 mr-1" />
                  Delete
              </button>
          </div>
      </div>

      <DeleteConfirmationModal 
          :show="showDeleteModal"
          :loading="isDeleting"
          @confirm="deleteCategory"
          @cancel="showDeleteModal = false"
      >
          <template #message>
              Are you sure you want to delete <b>{{ category.name }}</b>? This action cannot be undone.
          </template>
      </DeleteConfirmationModal>
  </div>
</template>

