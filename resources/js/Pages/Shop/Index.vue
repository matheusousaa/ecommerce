<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import {
    Search,
    ChevronLeft,
    ChevronRight,
    PackageX,
    Plus,
    Eye,
    Loader2
} from 'lucide-vue-next';

import { Link } from '@inertiajs/vue3';
import ShopProductCard from '@/Components/ShopProductCard.vue';

import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
  products: Array,
  categories: Array
});

function addToCart(product) {
  // Implemente a lógica de adicionar ao carrinho (localStorage, store, ou backend)
  alert(`Produto ${product.name} adicionado ao carrinho!`);
}

// State
const products = ref(props.products ?? []);
const categories = ref(props.categories ?? []);
const searchQuery = ref('');
const selectedCategory = ref('');
const sortBy = ref('name');
const currentPage = ref(1);
const itemsPerPage = ref(12);

// Computed properties
const filteredProducts = computed(() => {
  let result = [...products.value];

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(product =>
      product.name.toLowerCase().includes(query) ||
      product.description.toLowerCase().includes(query)
    );
  }

  // Apply category filter
  if (selectedCategory.value) {
    result = result.filter(product => product.category_id === selectedCategory.value);
  }

  // Apply sorting
  switch (sortBy.value) {
    case 'name':
      result.sort((a, b) => a.name.localeCompare(b.name));
      break;
    case 'price_asc':
      result.sort((a, b) => a.price - b.price);
      break;
    case 'price_desc':
      result.sort((a, b) => b.price - a.price);
      break;
    case 'quantity':
      result.sort((a, b) => b.quantity - a.quantity);
      break;
    case 'newest':
      result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      break;
  }

  // Pagination
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  const endIndex = startIndex + itemsPerPage.value;
  return result.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
  const filtered = products.value.filter(product => {
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase();
      if (!product.name.toLowerCase().includes(query) &&
        !product.description.toLowerCase().includes(query)) {
        return false;
      }
    }

    if (selectedCategory.value && product.category_id !== selectedCategory.value) {
      return false;
    }

    return true;
  });

  return Math.ceil(filtered.length / itemsPerPage.value) || 1;
});

// Watch for filter changes to reset pagination
watch([searchQuery, selectedCategory, sortBy], () => {
  currentPage.value = 1;
});
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

/* Line clamp for product descriptions */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

<template>
  <PublicLayout>
    <div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
      <div class="max-w-7xl mx-auto">

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
          <div class="flex flex-wrap items-center gap-4">
            <div>
              <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
              <select id="category" v-model="selectedCategory"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                <option value="">All Categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            <div>
              <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
              <select id="sort" v-model="sortBy"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                <option value="name">Name</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="newest">Newest</option>
              </select>
            </div>
            <div class="ml-auto">
              <div class="relative">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                <input type="text" placeholder="Search products..." v-model="searchQuery"
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary" />
              </div>
            </div>
          </div>
        </div>

        <!-- Products Grid -->
        <div v-if="filteredProducts.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
          <PackageX class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-lg font-medium text-gray-900">No products found</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ searchQuery ? 'Try adjusting your search or filters' : 'Get started by adding your first product' }}
          </p>
          <div class="mt-6">
            <Link href="/products/create"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
              style="font-size: 1rem;">
            <Plus class="h-4 w-4 mr-2" />
            Add Product
            </Link>
          </div>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <ShopProductCard v-for="product in filteredProducts" :key="product.id" :product="product" />
        </div>

        <!-- Pagination -->
        <div v-if="filteredProducts.length > 0" class="mt-6 flex justify-center">
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <button @click="currentPage > 1 && (currentPage--)" :disabled="currentPage === 1"
              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              :class="{ 'opacity-50 cursor-not-allowed': currentPage === '1' }">
              <span class="sr-only">Previous</span>
              <ChevronLeft class="h-5 w-5" />
            </button>

            <button v-for="page in totalPages" :key="page" @click="currentPage = page" :class="[
              currentPage === page ? 'z-10 bg-primary border-primary text-white' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
            ]">
              {{ page }}
            </button>

            <button @click="currentPage < totalPages && (currentPage++)" :disabled="currentPage === totalPages"
              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }">
              <span class="sr-only">Next</span>
              <ChevronRight class="h-5 w-5" />
            </button>
          </nav>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>