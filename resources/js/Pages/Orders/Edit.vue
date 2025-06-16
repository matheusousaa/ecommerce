<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    order: Object
});

const form = useForm({
    user_id: '',
    total: ''
});

const submit = () => {
    form.put(`/orders/${props.order.id}`, {
        onSuccess: () => {
            alert('Compra atualizada com sucesso!');
        },
        onError: (errors) => {
            console.error(errors);
            alert('Erro ao atualizar a compra.');
        }
    });
};
</script>

<template>
    <div>
        <h1>Editar Compra</h1>
        <form @submit.prevent="submit">
            <textarea v-model="form.description" placeholder="Descrição"></textarea>
            <input v-model="form.total" type="number" placeholder="Total" required>
            <input v-model="form.user_id" type="number" placeholder="ID do Usuário" required>
            <button type="submit">Salvar</button>
        </form>
    </div>
</template>
