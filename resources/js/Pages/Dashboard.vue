<script setup>
import { ref, onMounted } from 'vue';
import {
  Package,
  ShoppingCart,
  AlertTriangle,
  TrendingUp,
  DollarSign,
  Eye
} from 'lucide-vue-next';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  totalProducts: {
    type: Number,
  },
  lowStockProducts: {
    type: Object,
  },
  totalOrders: {
    type: Number,
  },
  recentOrders: {
    type: Object
  },
  totalRevenue: {
    type: Number,
  }
});

// State
const userMenuOpen = ref(false);
const showLowStockModal = ref(false);
const isLoading = ref(true);
const error = ref(null);

// Data
const stats = ref({
  totalProducts: 0,
  totalOrders: 0,
  totalRevenue: 0,
  productGrowth: 0,
  orderGrowth: 0,
  revenueGrowth: 0
});

// Methods
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const getStatusClass = (status) => {
  const classes = {
    'Pending': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800',
    'Paid': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800',
    'Cancelled': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'
  };

  return classes[status] || 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
};

// Fetch data from Laravel API
const fetchDashboardData = async () => {
  try {
    isLoading.value = true;

    // In a real application, you would fetch from your Laravel API endpoints
    // const response = await axios.get('/api/dashboard');
    // stats.value = response.data.stats;
    // recentOrders.value = response.data.recentOrders;

    // For demo purposes, we'll use mock data
    setTimeout(() => {
      stats.value = {
        totalProducts: props.totalProducts || 248,
        totalOrders: props.totalOrders || 1024,
        totalRevenue: props.totalRevenue || 52495.75,
        productGrowth: 12,
        orderGrowth: 18,
        revenueGrowth: 24
      };

      isLoading.value = false;
    }, 500);

  } catch (err) {
    error.value = 'Failed to load dashboard data';
    console.error(err);
    isLoading.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  fetchDashboardData();

  // Close user menu when clicking outside
  const handleClickOutside = (event) => {
    if (userMenuOpen.value && !event.target.closest('button')) {
      userMenuOpen.value = false;
    }
  };

  document.addEventListener('click', handleClickOutside);

  return () => {
    document.removeEventListener('click', handleClickOutside);
  };
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

.focus\:ring-primary:focus {
  --tw-ring-color: rgb(var(--color-primary));
}

.hover\:bg-primary-dark:hover {
  background-color: rgb(var(--color-primary-dark));
}
</style>

<template>
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Dashboard Content -->
      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"> 
          <!-- Total Products Card -->
          <DashboardCard
            :stats="totalProducts"
            :growth="stats.productGrowth"
            :color="'green'"
          >
            <template #icon><Package class="h-6 w-6 text-green-600" /></template>  
            <template #title>Total Products</template>
            <template #growthIcon><TrendingUp class="h-4 w-4 mr-1" /></template>
          </DashboardCard>

          <!-- Total Orders Card -->
          <DashboardCard
            :stats="totalOrders"
            :growth="stats.orderGrowth"
            :color="'blue'"
          >
            <template #icon><ShoppingCart class="h-6 w-6 text-blue-600" /></template>  
            <template #title>Total Orders</template>
            <template #growthIcon><TrendingUp class="h-4 w-4 mr-1" /></template>
          </DashboardCard>

          <!-- Revenue Card -->
          <DashboardCard
            :stats="stats.totalRevenue"
            :growth="stats.revenueGrowth"
            :color="'purple'"
          >
            <template #icon><DollarSign class="h-6 w-6 text-purple-600" /></template>
            <template #title>Total Revenue</template>
            <template #stats>
              ${{ stats.totalRevenue }}
            </template>
            <template #growthIcon><TrendingUp class="h-4 w-4 mr-1" /></template>
          </DashboardCard>

          <!-- Low Stock Alert Card -->
          <DashboardCard
            :stats="props.lowStockProducts.length"
            :growth="0"
            :color="'red'"
          >
            <template #icon><AlertTriangle class="h-6 w-6 text-red-600" /></template>  
            <template #title>Low Stock Items</template>
            <template #growthIcon><Eye class="h-4 w-4 mr-1" /></template>
            <template #footer>
              <button @click="showLowStockModal = true" class="text-sm text-red-600 flex items-center">
                View all items
              </button>
            </template>
          </DashboardCard>
        </div>

        <!-- Low Stock Products Section -->
        <div class="bg-white rounded-lg shadow mb-8">
          <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Products with Low Stock</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in lowStockProducts.slice(0, 5)" :key="product.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="h-10 w-10 flex-shrink-0">
                        <img class="h-10 w-10 rounded-full" :src="`/storage/${product.image}`" alt="" />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                        <div v-if="product.sku" class="text-sm text-gray-500">SKU: {{ product.sku }} </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ product.quantity }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                      Low Stock
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <Link :href="`/products/${product.id}/edit`" class="text-primary hover:text-primary-dark">
                      Restock
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="lowStockProducts.length > 5" class="px-6 py-3 flex justify-center">
              <button @click="showLowStockModal = true"
                class="text-sm text-primary hover:text-primary-dark flex items-center">
                View all {{ lowStockProducts.length }} items
              </button>
            </div>
          </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="order in recentOrders" :key="order.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">#{{ order.id }}</div>
                  </td>
                  <td class="px-2 py-4 whitespace-nowrap">
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ order.user.name }}</div>
                      <div class="text-sm text-gray-500">{{ order.user.email }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(order.updated_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${{ order.total.toLocaleString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(order.status.name)">
                      {{ order.status.name }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <Link :href="`/orders/${order.id}`" class="text-primary hover:text-primary-dark">
                      View
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="px-6 py-3 flex justify-center">
              <Link :href="route('orders.index')" class="text-sm text-primary hover:text-primary-dark flex items-center">
                View all orders
              </Link>
            </div>
          </div>
        </div>
      </main>

      <!-- Low Stock Modal -->
      <div v-if="showLowStockModal" class="fixed inset-0 overflow-y-auto z-50">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showLowStockModal = false"></div>
          </div>
          <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div
                  class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                  <AlertTriangle class="h-6 w-6 text-red-600" />
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Low Stock Products</h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">
                      These products need to be restocked soon.
                    </p>
                  </div>
                </div>
              </div>
              <div class="mt-4 max-h-60 overflow-y-auto">
                <ul class="divide-y divide-gray-200">
                  <li v-for="product in lowStockProducts" :key="product.id"
                    class="py-3 flex justify-between items-center">
                    <div class="flex items-center">
                      <img class="h-10 w-10 rounded-full" :src="`/storage/${product.image}`" alt="" />
                      <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                        <p class="text-sm text-gray-500">Stock: {{ product.stock }}</p>
                      </div>
                    </div>
                    <Link :href="`/products/${product.id}/edit`" class="text-sm text-primary hover:text-primary-dark">
                      Restock
                    </Link>
                  </li>
                </ul>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button @click="showLowStockModal = false" type="button"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
