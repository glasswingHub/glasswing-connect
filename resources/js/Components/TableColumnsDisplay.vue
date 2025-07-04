<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    tableName: {
        type: String,
        required: true
    },
    tableType: {
        type: String,
        required: true,
        validator: (value) => ['source', 'target'].includes(value)
    },
    title: {
        type: String,
        default: ''
    }
});

const isLoading = ref(false);
const columns = ref([]);

// Watch for changes in tableName
watch(() => props.tableName, async (newValue) => {
    if (newValue) {
        await fetchTableColumns(newValue);
    } else {
        columns.value = [];
    }
});

const fetchTableColumns = async (tableName) => {
    isLoading.value = true;
    
    try {
        const routeName = props.tableType === 'source' 
            ? 'importers.get-source-table-columns' 
            : 'importers.get-target-table-columns';
            
        const response = await window.fetchWithCSRF(route(routeName), {
            method: 'POST',
            body: JSON.stringify({ table_name: tableName })
        });
        
        if (response.ok) {
            const data = await response.json();
            columns.value = data;
        } else {
            console.error(`Error fetching ${props.tableType} table columns`);
            columns.value = [];
        }
    } catch (error) {
        console.error(`Error fetching ${props.tableType} table columns:`, error);
        columns.value = [];
    } finally {
        isLoading.value = false;
    }
};

// Load columns on mount if tableName is already set
onMounted(() => {
    if (props.tableName) {
        fetchTableColumns(props.tableName);
    }
});

// Expose loading state to parent component
defineExpose({
    isLoading
});
</script>

<template>
    <div v-if="tableName" class="mt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ title || `Columnas de la tabla ${tableType === 'source' ? 'origen' : 'destino'}: ${tableName}` }}
        </h3>
        
        <div v-if="isLoading" class="text-center py-4">
            <div class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-600">Cargando columnas...</span>
            </div>
        </div>
        
        <div v-else-if="columns.length > 0" class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                <div 
                    v-for="column in columns" 
                    :key="column.value"
                    class="bg-white px-3 py-2 rounded border text-sm"
                >
                    {{ column.label }}
                </div>
            </div>
        </div>
        
        <div v-else class="text-gray-500 text-center py-4">
            No se encontraron columnas para esta tabla.
        </div>
    </div>
</template> 