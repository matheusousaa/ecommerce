<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    category: Object
});
</script>

<template>
    <div>
        <h1>{{ category.name }}</h1>
        <Link href="/categories">Voltar</Link>
    </div>
</template>
