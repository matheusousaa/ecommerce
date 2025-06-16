<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    product: Object
});
</script>

<template>
    <div>
        <h1>{{ product.name }}</h1>
        <p>{{ product.description }}</p>
        <p>Preço: R$ {{ product.price }}</p>
        <Link href="/products">Voltar</Link>
    </div>
</template>
