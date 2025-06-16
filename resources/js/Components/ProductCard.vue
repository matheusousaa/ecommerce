<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Edit, Trash2 } from 'lucide-vue-next';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue';

const { product } = defineProps({
    product: Object
});

const form = useForm();
const showDeleteModal = ref(false);
const isDeleting = ref(false);

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2);
};

const getStockClass = (quantity) => {
  if (quantity <= 0) return 'bg-red-100 text-red-800';
  if (quantity < 10) return 'bg-yellow-100 text-yellow-800';
  return 'bg-green-100 text-green-800';
};

const getStockLabel = (quantity) => {
  if (quantity <= 0) return 'Out of Stock';
  if (quantity < 10) return 'Low Stock';
  return 'In Stock';
};

const editProduct = (id) => {
    window.location.href = `/products/${id}/edit`;
};

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteProduct = () => {
    isDeleting.value = true;
    form.delete(`/products/${product.id}`, {
        onSuccess: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
        },
        onError: (errors) => {
            isDeleting.value = false;
            showDeleteModal.value = false;
            alert('Erro ao excluir o produto.');
        }
    });
};
</script>

<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="relative pb-[56.25%] bg-gray-100">
            <img 
            :src="`/storage/${product.image}` || '/placeholder.svg'" 
            :alt="product.name"
            class="absolute h-full w-full object-cover"
            />
        </div>

        <div class="p-4">
            <div class="flex justify-between items-start">
                <h3 class="text-lg font-medium text-gray-900 truncate">{{ product.name }}</h3>
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="getStockClass(product.quantity)">
                    {{ getStockLabel(product.quantity) }}
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ product.description }}</p>
            <div class="mt-2 flex items-center justify-between">
                <span class="text-lg font-bold text-gray-900">${{ formatPrice(product.price) }}</span>
                <span class="text-sm text-gray-500">Stock: {{ product.quantity }}</span>
            </div>
            <div class="mt-4 flex justify-between">
                <button @click="editProduct(product.id)"
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
            :product="product"
            @confirm="deleteProduct"
            @close="showDeleteModal = false"
            @cancel="showDeleteModal = false"
        >
            <template #message>
                Are you sure you want to delete <b>{{ product.name }}</b>? This action cannot be undone.
            </template>
        </DeleteConfirmationModal>
    </div>
</template>
