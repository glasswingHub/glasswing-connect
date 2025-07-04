<script setup>
import { ref, watch, onMounted } from 'vue';
import InputSelect from '@/Components/InputSelect.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    sourceColumns: {
        type: Array,
        default: () => []
    },
    targetColumns: {
        type: Array,
        default: () => []
    },
    existingMappings: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:mappings']);

const columnMappings = ref([]);

// Initialize mappings from props
onMounted(() => {
    if (props.existingMappings.length > 0) {
        columnMappings.value = [...props.existingMappings];
    }
});

// Watch for changes in mappings and emit updates
watch(columnMappings, (newMappings) => {
    emit('update:mappings', newMappings);
}, { deep: true });

const addMapping = () => {
    columnMappings.value.push({
        id: Date.now(), // Temporary ID for new mappings
        source_column: '',
        target_column: ''
    });
};

const removeMapping = (index) => {
    columnMappings.value.splice(index, 1);
};

const updateMapping = (index, field, value) => {
    columnMappings.value[index][field] = value;
};

// Get available source columns (excluding already mapped ones)
const getAvailableSourceColumns = () => {
    const mappedSourceColumns = columnMappings.value
        .map(mapping => mapping.source_column)
        .filter(column => column !== '');
    
    return props.sourceColumns.filter(column => 
        !mappedSourceColumns.includes(column.value) || 
        columnMappings.value.find(mapping => mapping.source_column === column.value)
    );
};

// Get available target columns (excluding already mapped ones)
const getAvailableTargetColumns = () => {
    const mappedTargetColumns = columnMappings.value
        .map(mapping => mapping.target_column)
        .filter(column => column !== '');
    
    return props.targetColumns.filter(column => 
        !mappedTargetColumns.includes(column.value) || 
        columnMappings.value.find(mapping => mapping.target_column === column.value)
    );
};
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                Mapeo de Columnas
            </h3>
            <SecondaryButton @click="addMapping" type="button">
                Agregar Mapeo
            </SecondaryButton>
        </div>

        <!-- Mappings List -->
        <div v-if="columnMappings.length === 0" class="text-center py-8 text-gray-500">
            <p>No hay mapeos de columnas configurados.</p>
            <p class="text-sm mt-1">Haz clic en "Agregar Mapeo" para comenzar.</p>
        </div>

        <div v-else class="space-y-4">
            <div 
                v-for="(mapping, index) in columnMappings" 
                :key="mapping.id || index"
                class="bg-gray-50 rounded-lg p-4 border"
            >
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-700">
                        Mapeo #{{ index + 1 }}
                    </h4>
                    <DangerButton 
                        @click="removeMapping(index)" 
                        type="button"
                        class="text-xs px-2 py-1"
                    >
                        Eliminar
                    </DangerButton>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Source Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Columna Origen
                        </label>
                        <InputSelect
                            v-model="mapping.source_column"
                            @update:model-value="updateMapping(index, 'source_column', $event)"
                            :options="getAvailableSourceColumns()"
                            placeholder="Seleccionar columna origen"
                            class="w-full"
                        />
                    </div>

                    <!-- Target Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Columna Destino
                        </label>
                        <InputSelect
                            v-model="mapping.target_column"
                            @update:model-value="updateMapping(index, 'target_column', $event)"
                            :options="getAvailableTargetColumns()"
                            placeholder="Seleccionar columna destino"
                            class="w-full"
                        />
                    </div>
                </div>

                <!-- Mapping Preview -->
                <div v-if="mapping.source_column && mapping.target_column" class="mt-3 p-2 bg-blue-50 rounded border-l-4 border-blue-400">
                    <p class="text-sm text-blue-800">
                        <span class="font-medium">{{ mapping.source_column }}</span>
                        <span class="mx-2">→</span>
                        <span class="font-medium">{{ mapping.target_column }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div v-if="columnMappings.length > 0" class="bg-blue-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-900 mb-2">Resumen de Mapeos</h4>
            <div class="text-sm text-blue-800">
                <p>Total de mapeos: {{ columnMappings.length }}</p>
                <p>Mapeos completos: {{ columnMappings.filter(m => m.source_column && m.target_column).length }}</p>
                <p>Mapeos pendientes: {{ columnMappings.filter(m => !m.source_column || !m.target_column).length }}</p>
            </div>
        </div>
    </div>
</template> 