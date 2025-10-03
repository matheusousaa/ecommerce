<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ShoppingCartIcon } from 'lucide-vue-next';

const { product } = defineProps({
    product: Object
});

const form = useForm();

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2);
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
            </div>
            <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ product.description }}</p>
            <div class="mt-2 flex items-center justify-between">
                <span class="text-lg font-bold text-gray-900">${{ formatPrice(product.price) }}</span>
                <button
                  @click="addToCart(product)"
                  :disabled="product.quantity <= 0"
                  class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <ShoppingCartIcon class="h-4 w-4 inline mr-1" />
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</template>
