<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  order: Object
});

const statusClasses = {
  pending: 'bg-yellow-100 text-yellow-800',
  paid: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800'
};
</script>

<template>
  <div class="bg-white rounded-lg p-4 shadow-md border">
    <h2 class="text-lg font-bold">Cliente: {{ order.user.name }}</h2>
    <span
      v-if="order.status"
      class="px-2 py-1 text-sm font-semibold rounded"
      :class="statusClasses[order.status.name]"
    >
      {{ order.status.name.charAt(0).toUpperCase() + order.status.name.slice(1) }}
    </span>
    <p class="text-gray-600 mt-2">Total: R$ {{ order.total }}</p>
    <p class="text-gray-500 text-sm">{{ new Date(order.created_at).toLocaleDateString() }}</p>
    <Link 
      :href="`/orders/${order.id}`"
      class="mt-2 block bg-blue-500 text-white px-4 py-2 rounded text-center hover:bg-blue-600"
    >
      Ver detalhes
    </Link>
  </div>
</template>
