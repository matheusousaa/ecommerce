<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import {
    Search,
    ChevronLeft,
    ChevronRight,
    PackageX,
    Eye,
    Loader2
} from 'lucide-vue-next';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    orders: Array,
    statuses: Array
});

// State
const orders = ref(props.orders ?? []);
const statuses = ref(props.statuses ?? []);

const searchQuery = ref('');
const selectedStatus = ref('');
const dateRange = ref('all');
const sortBy = ref('date_desc');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const showOrderModal = ref(false);
const selectedOrder = ref(null);
const newStatus = ref(selectedOrder.status_id);
const isUpdating = ref(false);

// Computed properties
const filteredOrders = computed(() => {
    let result = [...orders.value];

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(order =>
            order.id.toString().includes(query) ||
            order.user.name.toLowerCase().includes(query) ||
            order.user.email.toLowerCase().includes(query)
        );
    }

    // Apply status filter
    if (selectedStatus.value) {
        result = result.filter(order => order.status.name.toLowerCase() === selectedStatus.value.toLowerCase());
    }

    // Apply date filter
    if (dateRange.value !== 'all') {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const startOfWeek = new Date(now);
        startOfWeek.setDate(now.getDate() - now.getDay());
        const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
        const startOfYear = new Date(now.getFullYear(), 0, 1);

        result = result.filter(order => {
            const orderDate = new Date(order.updated_at);
            switch (dateRange.value) {
                case 'today':
                    return orderDate >= today;
                case 'week':
                    return orderDate >= startOfWeek;
                case 'month':
                    return orderDate >= startOfMonth;
                case 'year':
                    return orderDate >= startOfYear;
                default:
                    return true;
            }
        });
    }

    // Apply sorting
    switch (sortBy.value) {
        case 'date_desc':
            result.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
            break;
        case 'date_asc':
            result.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
            break;
        case 'total_desc':
            result.sort((a, b) => b.total - a.total);
            break;
        case 'total_asc':
            result.sort((a, b) => a.total - b.total);
            break;
    }

    // Pagination
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    const endIndex = startIndex + itemsPerPage.value;
    return result.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
    const filtered = orders.value.filter(order => {
        if (searchQuery.value) {
            const query = searchQuery.value.toLowerCase();
            if (!order.id.toString().includes(query) &&
                !order.user.name.toLowerCase().includes(query) &&
                !order.user.email.toLowerCase().includes(query)) {
                return false;
            }
        }

        if (selectedStatus.value && order.status.name.toLowerCase() !== selectedStatus.value.toLowerCase()) {
            return false;
        }

        return true;
    });

    return Math.ceil(filtered.length / itemsPerPage.value) || 1;
});

const formatPrice = (price) => {
    return parseFloat(price).toFixed(2);
};

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const formatTime = (dateString) => {
    const options = { hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleTimeString(undefined, options);
};

const getStatusClass = (status) => {
    const classes = {
        'Pending': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800',
        'Paid': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800',
        'Cancelled': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'
    };

    return classes[status] || 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
};

const viewOrderDetails = (order) => {
    selectedOrder.value = order;
    newStatus.value = order.status.id;
    showOrderModal.value = true;
};

const updateOrderStatus = async () => {
    if (!selectedOrder.value) return;

    isUpdating.value = true;

    router.patch(`/orders/${selectedOrder.value.id}/status`, {
        status_id: newStatus.value
    }, {
        onSuccess: () => {
            selectedOrder.value.status.id = newStatus.value;
            isUpdating.value = false;
            window.location.reload();
            showOrderModal.value = false;
        },
        onError: (error) => {
            console.error('Failed to update order status:', error);
            isUpdating.value = false;
            // Show error message
            alert('Failed to update order status');
        }
    });
};

const resetFilters = () => {
    searchQuery.value = '';
    selectedStatus.value = '';
    dateRange.value = 'all';
    sortBy.value = 'date_desc';
    currentPage.value = 1;
};

// Watch for filter changes to reset pagination
watch([searchQuery, selectedStatus, dateRange, sortBy], () => {
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
</style>

<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Manage and track your customer orders
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                            <input type="text" placeholder="Search orders..." v-model="searchQuery"
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary" />
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" v-model="selectedStatus"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                <option value="">All Statuses</option>
                                <option v-for="status in statuses" :key="status.id" :value="status.name">
                                    {{ status.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label for="date-range" class="block text-sm font-medium text-gray-700">Date Range</label>
                            <select id="date-range" v-model="dateRange"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                <option value="all">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
                            <select id="sort" v-model="sortBy"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                <option value="date_desc">Date: Newest First</option>
                                <option value="date_asc">Date: Oldest First</option>
                                <option value="total_desc">Total: High to Low</option>
                                <option value="total_asc">Total: Low to High</option>
                            </select>
                        </div>
                        <div class="ml-auto">
                            <button @click="resetFilters"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Orders List -->
                <div v-if="filteredOrders.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
                    <PackageX class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No orders found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Try adjusting your search or filters to find what you're looking for.
                    </p>
                </div>

                <div v-else>
                    <!-- Desktop View: Table -->
                    <div class="hidden md:block bg-white shadow rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order ID</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Customer</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="order in filteredOrders" :key="order.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#{{ order.id }}</div>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ order.user.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ order.user.email }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatDate(order.updated_at) }}</div>
                                        <div class="text-sm text-gray-500">{{ formatTime(order.updated_at) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ formatPrice(order.total) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getStatusClass(order.status.name)">
                                            {{ order.status.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button @click="viewOrderDetails(order)"
                                            class="text-primary hover:text-primary-dark mr-3">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View: Cards -->
                    <div class="md:hidden space-y-4">
                        <div v-for="order in filteredOrders" :key="order.id"
                            class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div class="text-sm font-medium text-gray-900">Order #{{ order.id }}</div>
                                    <span :class="getStatusClass(order.status.name)">
                                        {{ order.status.name }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ order.user.name }}</div>
                                        <div class="text-xs text-gray-500">{{ order.user.email }}</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Date</div>
                                        <div>{{ formatDate(order.updated_at) }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Total</div>
                                        <div class="font-medium">${{ formatPrice(order.total) }}</div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button @click="viewOrderDetails(order)"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                        <Eye class="h-4 w-4 mr-2" />
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex justify-center">
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button @click="currentPage > 1 && (currentPage--)" :disabled="currentPage === 1"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
                                <span class="sr-only">Previous</span>
                                <ChevronLeft class="h-5 w-5" />
                            </button>

                            <button v-for="page in totalPages" :key="page" @click="currentPage = page" :class="[
                                currentPage === page ? 'z-10 bg-primary border-primary text-white' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                            ]">
                                {{ page }}
                            </button>

                            <button @click="currentPage < totalPages && (currentPage++)"
                                :disabled="currentPage === totalPages"
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }">
                                <span class="sr-only">Next</span>
                                <ChevronRight class="h-5 w-5" />
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Order Details Modal -->
            <div v-if="showOrderModal" class="fixed inset-0 overflow-y-auto z-50">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showOrderModal = false"></div>
                    </div>
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Order Details</h3>
                                        <span :class="getStatusClass(selectedOrder?.status.name)">
                                            {{ selectedOrder?.status.name }}
                                        </span>
                                    </div>

                                    <div class="mt-4 border-t border-gray-200 pt-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Order Information -->
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500">Order Information</h4>
                                                <div class="mt-2 grid grid-cols-2 gap-2">
                                                    <div class="text-sm">
                                                        <p class="text-gray-500">Order ID</p>
                                                        <p class="font-medium">#{{ selectedOrder?.id }}</p>
                                                    </div>
                                                    <div class="text-sm">
                                                        <p class="text-gray-500">Date Placed</p>
                                                        <p class="font-medium">{{ formatDate(selectedOrder?.updated_at)
                                                            }}</p>
                                                    </div>
                                                    <div class="text-sm">
                                                        <p class="text-gray-500">Payment Method</p>
                                                        <p class="font-medium">{{ selectedOrder?.paymentMethod }}</p>
                                                    </div>
                                                    <div class="text-sm">
                                                        <p class="text-gray-500">Shipping Method</p>
                                                        <p class="font-medium">{{ selectedOrder?.shippingMethod }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Customer Information -->
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500">Customer Information</h4>
                                                <div class="mt-2 flex items-center">
                                                    <div>
                                                        <p class="text-sm font-medium">{{ selectedOrder?.user.name
                                                        }}</p>
                                                        <p class="text-sm text-gray-500">{{
                                                            selectedOrder?.user.email }}</p>
                                                        <p class="text-sm text-gray-500">{{
                                                            selectedOrder?.user.phone }}</p>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <h5 class="text-xs font-medium text-gray-500">Shipping Address</h5>
                                                    <p class="text-sm">{{ selectedOrder?.shippingAddress?.street }}</p>
                                                    <p class="text-sm">{{ selectedOrder?.shippingAddress?.city }}, {{
                                                        selectedOrder?.shippingAddress?.state }} {{
                                                            selectedOrder?.shippingAddress?.zip }}</p>
                                                    <p class="text-sm">{{ selectedOrder?.shippingAddress?.country }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Order Items -->
                                    <div class="mt-6">
                                        <h4 class="text-sm font-medium text-gray-500">Order Items</h4>
                                        <div class="mt-2 -mx-4 sm:-mx-6 overflow-x-auto">
                                            <div class="inline-block min-w-full py-2 align-middle">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col"
                                                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Product</th>
                                                            <th scope="col"
                                                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Price</th>
                                                            <th scope="col"
                                                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Quantity</th>
                                                            <th scope="col"
                                                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        <tr v-for="item in selectedOrder?.items" :key="item.id">
                                                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                                                <div class="flex items-center">
                                                                    <div class="h-10 w-10 flex-shrink-0">
                                                                        <img class="h-10 w-10 rounded object-cover"
                                                                            :src="`/storage/${item.product.image}`"
                                                                            alt="" />
                                                                    </div>
                                                                    <div class="ml-4">
                                                                        <div class="text-sm font-medium text-gray-900">
                                                                            {{ item.product.name }}</div>
                                                                        <div class="text-sm text-gray-500">SKU: {{
                                                                            item.product.sku }}</div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td
                                                                class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                ${{ formatPrice(item.price) }}
                                                            </td>
                                                            <td
                                                                class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                {{ item.quantity }}
                                                            </td>
                                                            <td
                                                                class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                ${{ formatPrice(item.price * item.quantity) }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Order Summary -->
                                    <div class="mt-6 border-t border-gray-200 pt-4">
                                        <div class="flex justify-end">
                                            <div class="w-full sm:w-1/2 md:w-1/3">
                                                <div class="flex justify-between py-1 text-sm font-medium">
                                                    <dt class="text-gray-900">Total</dt>
                                                    <dd class="text-primary">${{ formatPrice(selectedOrder?.total) }}
                                                    </dd>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Update Status -->
                                    <div class="mt-6 border-t border-gray-200 pt-4">
                                        <div class="flex flex-col sm:flex-row justify-between items-center">
                                            <div class="flex items-center mb-4 sm:mb-0">
                                                <label for="order-status"
                                                    class="block text-sm font-medium text-gray-700 mr-3">Update
                                                    Status:</label>
                                                <select id="order-status" v-model="newStatus"
                                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                                    <option v-for="status in statuses" :key="status.id" :value="status.id">
                                                        {{ status.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <button @click="updateOrderStatus(newStatus)"
                                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                                :disabled="isUpdating">
                                                <Loader2 v-if="isUpdating" class="animate-spin h-4 w-4 mr-2" />
                                                {{ isUpdating ? 'Updating...' : 'Update Status' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button @click="showOrderModal = false" type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>