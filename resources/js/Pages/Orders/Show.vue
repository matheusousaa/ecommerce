<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    order: Object
});
</script>

<template>
    <Head title="Detalhes do Pedido" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detalhes do Pedido #{{ order.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl bg-white p-6 shadow rounded">
                <p class="text-lg font-bold">Cliente: {{ order.user.name }}</p>
                <p class="text-gray-600">Data: {{ new Date(order.created_at).toLocaleDateString() }}</p>
                <p class="text-gray-600">Total: R$ {{ order.total }}</p>
                <p class="text-gray-600">Status: <span :class="{
                    'text-green-600': order.status === 'Pago',
                    'text-red-600': order.status === 'Cancelado',
                    'text-yellow-600': order.status === 'Pendente'
                }">{{ order.status }}</span></p>

                <h3 class="mt-4 text-lg font-semibold">Itens do Pedido:</h3>
                <ul class="mt-2 space-y-2">
                    <li v-for="item in order.items" :key="item.id" class="border-b py-2">
                        <p>{{ item.product.name }} - Quantidade: {{ item.quantity }} - R$ {{ item.price }}</p>
                    </li>
                </ul>

                <div class="mt-6">
                    <a href="/orders" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Voltar</a>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
