<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: null
    }
});
</script>

<template>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ title }}
        <span v-if="subtitle" class="block text-sm text-gray-600">{{ subtitle }}</span>
    </h2>
</template> 