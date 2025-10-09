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
    Loader2,
    ShoppingCartIcon
} from 'lucide-vue-next';

import { Link } from '@inertiajs/vue3';
import ShopProductCard from '@/Components/ShopProductCard.vue';

import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
  products: Array,
  categories: Array
});

function addToCart(product){
  addToCartWithQuantity(product, 1);
};

const addToCartWithQuantity = (product, quantity) => {
  if (product.quantity <= 0) return;
  
  const existingItem = cart.value.find(item => item.id === product.id);
  
  if (existingItem) {
    existingItem.quantity += quantity;
  } else {
    cart.value.push({
      id: product.id,
      name: product.name,
      price: product.price,
      image: product.image,
      quantity: quantity
    });
  }
  
  showToastMessage(`${product.name} added to cart!`);
  
  // Close quick view modal if open
  if (showQuickView.value) {
    showQuickView.value = false;
  }
};

// State
const products = ref(props.products ?? []);
const categories = ref(props.categories ?? []);
const searchQuery = ref('');
const selectedCategory = ref('');
const sortBy = ref('name');
const currentPage = ref(1);
const itemsPerPage = ref(12);
const showCart = ref(false);
const cart = ref([]);

const cartItemsCount = computed(() => {
  return cart.value.reduce((total, item) => total + item.quantity, 0);
});

const cartTotal = computed(() => {
  return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

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
    <template v-slot:shoppingCart>
      <div class="flex items-center space-x-4">
        <button 
          @click="showCart = true"
          class="relative p-2 text-gray-600 hover:text-primary transition-colors"
        >
          <ShoppingCartIcon class="h-6 w-6" />
          <span 
            v-if="cartItemsCount > 0"
            class="absolute -top-1 -right-1 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
          >
            {{ cartItemsCount }}
          </span>
        </button>
        <div class="text-sm">
          <div class="font-medium text-gray-900">Cart</div>
          <div class="text-gray-500">${{ cartTotal.toFixed(2) }}</div>
        </div>
      </div>
    </template>

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
          <ShopProductCard 
            v-for="product in filteredProducts"
            :key="product.id"
            :product="product"
            @add-to-cart="addToCart"
          />
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

    <!-- Cart Sidebar -->
    <div v-if="showCart" class="fixed inset-0 overflow-hidden z-50">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCart = false"></div>
        <section class="absolute right-0 top-0 h-full w-full max-w-md flex flex-col bg-white shadow-xl">
          <div class="flex-1 overflow-y-auto py-6 px-4 sm:px-6">
            <div class="flex items-start justify-between">
              <h2 class="text-lg font-medium text-gray-900">Shopping Cart</h2>
              <div class="ml-3 h-7 flex items-center">
                <button @click="showCart = false" class="text-gray-400 hover:text-gray-500">
                  <X class="h-6 w-6" />
                </button>
              </div>
            </div>

            <div class="mt-8">
              <div v-if="cart.length === 0" class="text-center py-12">
                <ShoppingCart class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
                <p class="mt-1 text-sm text-gray-500">Start adding some products!</p>
              </div>
              
              <div v-else class="flow-root">
                <ul class="-my-6 divide-y divide-gray-200">
                  <li v-for="item in cart" :key="item.id" class="py-6 flex">
                    <div class="flex-shrink-0 w-16 h-16 border border-gray-200 rounded-md overflow-hidden">
                      <img :src="item.image || '/placeholder.svg'" :alt="item.name" class="w-full h-full object-center object-cover">
                    </div>

                    <div class="ml-4 flex-1 flex flex-col">
                      <div>
                        <div class="flex justify-between text-base font-medium text-gray-900">
                          <h3>{{ item.name }}</h3>
                          <p class="ml-4">${{ formatPrice(item.price * item.quantity) }}</p>
                        </div>
                      </div>
                      <div class="flex-1 flex items-end justify-between text-sm">
                        <div class="flex items-center space-x-2">
                          <button
                            @click="updateCartQuantity(item.id, item.quantity - 1)"
                            class="p-1 rounded-md border border-gray-300 hover:bg-gray-50"
                            :disabled="item.quantity <= 1"
                          >
                            <Minus class="h-3 w-3" />
                          </button>
                          <span class="text-gray-500">Qty {{ item.quantity }}</span>
                          <button
                            @click="updateCartQuantity(item.id, item.quantity + 1)"
                            class="p-1 rounded-md border border-gray-300 hover:bg-gray-50"
                          >
                            <Plus class="h-3 w-3" />
                          </button>
                        </div>

                        <div class="flex">
                          <button @click="removeFromCart(item.id)" class="font-medium text-primary hover:text-primary-dark">
                            Remove
                          </button>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div v-if="cart.length > 0" class="border-t border-gray-200 py-6 px-4 sm:px-6">
            <div class="flex justify-between text-base font-medium text-gray-900">
              <p>Subtotal</p>
              <p>${{ cartTotal.toFixed(2) }}</p>
            </div>
            <p class="mt-0.5 text-sm text-gray-500">Shipping and taxes calculated at checkout.</p>
            <div class="mt-6">
              <button class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary hover:bg-primary-dark">
                Checkout
              </button>
            </div>
            <div class="mt-6 flex justify-center text-sm text-center text-gray-500">
              <p>
                or 
                <button @click="showCart = false" class="text-primary font-medium hover:text-primary-dark">
                  Continue Shopping<span aria-hidden="true"> &rarr;</span>
                </button>
              </p>
            </div>
          </div>
        </section>
      </div>
    </div>
  </PublicLayout>
</template>