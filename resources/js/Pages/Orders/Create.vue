<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    user_id: '',
    total: ''
});

const submit = () => {
    form.post('/orders');
};
</script>

<template>
    <div>
        <h1>Adicionar Compra</h1>
        <form @submit.prevent="submit">
            <input v-model="form.total" type="number" placeholder="Total" required>
            <input v-model="form.user_id" type="number" placeholder="ID do Usuário" required>
            <button type="submit">Salvar</button>
        </form>
    </div>
</template>
