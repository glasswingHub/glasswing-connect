<script setup>
const props = defineProps({
    mappings: {
        type: Array,
        required: true
    },
    showEmptyMessage: {
        type: Boolean,
        default: true
    },
    emptyMessage: {
        type: String,
        default: 'No hay mapeos de columnas configurados'
    },
    emptyDescription: {
        type: String,
        default: 'No hay mapeos de columnas para mostrar.'
    }
});
</script>

<template>
    <div v-if="mappings && mappings.length > 0" class="mt-8">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="space-y-3">
                <div 
                    v-for="mapping in mappings" 
                    :key="mapping.id"
                    class="flex flex-col md:flex-row md:items-center md:justify-between bg-white p-3 rounded border"
                >
                    <div class="flex items-center space-x-4">
                        <span class="font-medium text-gray-700">{{ mapping.source_column }}</span>
                        <span class="text-gray-400">→</span>
                        <span class="font-medium text-gray-700">{{ mapping.target_column }}</span>
                        <span v-if="mapping.display_name" class="ml-2 text-gray-500 text-sm">({{ mapping.display_name }})</span>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                        <span 
                            v-if="mapping.primary_key" 
                            class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"
                        >
                            Llave primaria
                        </span>
                        <span 
                            v-if="mapping.country_key" 
                            class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded"
                        >
                            Llave país
                        </span>
                        <span 
                            v-if="mapping.uniqueness_key" 
                            class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded"
                        >
                            Llave única
                        </span>
                        <span 
                            v-if="!mapping.show_in_list" 
                            class="inline-block bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded"
                        >
                            Oculto en lista
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-else-if="showEmptyMessage" class="mt-8">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        {{ emptyMessage }}
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>{{ emptyDescription }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 