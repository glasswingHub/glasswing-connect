<template>
    <nav v-if="registrations && registrations.total > 0" class="flex items-center justify-between">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Mostrando
                    <span class="font-medium">{{ registrations.from }}</span>
                    a
                    <span class="font-medium">{{ registrations.to }}</span>
                    de
                    <span class="font-medium">{{ registrations.total }}</span>
                    resultados
                </p>
            </div>
        </div>
    </nav>
</template>

<script setup>
defineProps({
    registrations: Object,
});
</script> 