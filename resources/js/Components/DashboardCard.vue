<script setup>
import { computed } from 'vue';

const props = defineProps({
    stats: Number,
    growth: Number,
    color: {
        type: String,
        default: 'green'
    }
});

const bgColor = computed(() => {
    return {
        green: 'bg-green-100',
        blue: 'bg-blue-100',
        purple: 'bg-purple-100',
        red: 'bg-red-100'
    }[props.color] || 'bg-gray-100';
});

const textColor = computed(() => {
    return {
        green: 'text-green-600',
        blue: 'text-blue-600',
        purple: 'text-purple-600',
        red: 'text-red-600'
    }[props.color] || 'text-gray-600';
});
</script>

<template>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full mr-4" :class="bgColor">
                <slot name="icon" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">
                    <slot name="title" />
                </p>
                <p class="text-2xl font-semibold text-gray-900">
                    <slot name="stats">{{ stats }}</slot>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm" :class="textColor">
                <slot name="growthIcon" />
                <slot name="footer">{{ growth }}% from last month</slot>
            </div>
        </div>
    </div>
</template>