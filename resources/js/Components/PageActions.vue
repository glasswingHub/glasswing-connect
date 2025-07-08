<script setup>
import PrimaryLink from '@/Components/PrimaryLink.vue';


defineProps({
    title: {
        type: String,
        required: true
    },
    actions: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium">{{ title }}</h3>
        <div class="flex space-x-2">
            <template v-for="action in actions" :key="action.key">
                <PrimaryLink :href="action.href">
                    {{ action.label }}
                </PrimaryLink>
            </template>
        </div>
    </div>
</template> 