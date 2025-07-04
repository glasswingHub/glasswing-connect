<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputSelect from '@/Components/InputSelect.vue';

defineProps({
    targetTableOptions: Array,
    sourceTableOptions: Array,
});

const form = useForm({
    name: '',
    source_table: '',
    target_table: '',
    active: true,
});

const isLoadingColumns = ref(false);
const isLoadingTargetColumns = ref(false);
const sourceTableColumns = ref([]);
const targetTableColumns = ref([]);

// Watch for changes in source_table
watch(() => form.source_table, async (newValue) => {
    if (newValue) {
        await fetchSourceTableColumns(newValue);
    } else {
        sourceTableColumns.value = [];
    }
});

// Watch for changes in target_table
watch(() => form.target_table, async (newValue) => {
    if (newValue) {
        await fetchTargetTableColumns(newValue);
    } else {
        targetTableColumns.value = [];
    }
});

const fetchSourceTableColumns = async (tableName) => {
    isLoadingColumns.value = true;
    
    try {
        const response = await window.fetchWithCSRF(route('importers.get-source-table-columns'), {
            method: 'POST',
            body: JSON.stringify({ table_name: tableName })
        });
        
        if (response.ok) {
            const data = await response.json();
            sourceTableColumns.value = data;
        } else {
            console.error('Error fetching table columns');
            sourceTableColumns.value = [];
        }
    } catch (error) {
        console.error('Error fetching table columns:', error);
        sourceTableColumns.value = [];
    } finally {
        isLoadingColumns.value = false;
    }
};

const fetchTargetTableColumns = async (tableName) => {
    isLoadingTargetColumns.value = true;
    
    try {
        const response = await window.fetchWithCSRF(route('importers.get-target-table-columns'), {
            method: 'POST',
            body: JSON.stringify({ table_name: tableName })
        });
        
        if (response.ok) {
            const data = await response.json();
            targetTableColumns.value = data;
        } else {
            console.error('Error fetching target table columns');
            targetTableColumns.value = [];
        }
    } catch (error) {
        console.error('Error fetching target table columns:', error);
        targetTableColumns.value = [];
    } finally {
        isLoadingTargetColumns.value = false;
    }
};

const submit = () => {
    form.post(route('importers.store'));
};
</script>

<template>
    <Head title="Crear Importador" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear Importador
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre -->
                                <div>
                                    <InputLabel for="name" value="Nombre" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <!-- Tabla Origen -->
                                <div>
                                    <InputLabel for="source_table" value="Tabla Origen" />
                                    <InputSelect
                                        id="source_table"
                                        class="mt-1 block w-full"
                                        v-model="form.source_table"
                                        required
                                        :options="sourceTableOptions"
                                    />
                                    <InputError class="mt-2" :message="form.errors.source_table" />
                                </div>

                                <!-- Tabla Destino -->
                                <div>
                                    <InputLabel for="target_table" value="Tabla Destino" />
                                    <InputSelect
                                        id="target_table"
                                        class="mt-1 block w-full"
                                        v-model="form.target_table"
                                        required
                                        :options="targetTableOptions"
                                    />
                                    <InputError class="mt-2" :message="form.errors.target_table" />
                                </div>

                                <!-- Activo -->
                                <div class="flex items-center">
                                    <Checkbox
                                        id="active"
                                        v-model:checked="form.active"
                                    />
                                    <InputLabel for="active" value="Activo" class="ml-2" />
                                    <InputError class="mt-2" :message="form.errors.active" />
                                </div>
                            </div>

                            <!-- Columnas de la tabla origen seleccionada -->
                            <div v-if="form.source_table" class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    Columnas de la tabla origen: {{ form.source_table }}
                                </h3>
                                
                                <div v-if="isLoadingColumns" class="text-center py-4">
                                    <div class="inline-flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-gray-600">Cargando columnas...</span>
                                    </div>
                                </div>
                                
                                <div v-else-if="sourceTableColumns.length > 0" class="bg-gray-50 rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                        <div 
                                            v-for="column in sourceTableColumns" 
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

                            <!-- Columnas de la tabla destino seleccionada -->
                            <div v-if="form.target_table" class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    Columnas de la tabla destino: {{ form.target_table }}
                                </h3>
                                
                                <div v-if="isLoadingTargetColumns" class="text-center py-4">
                                    <div class="inline-flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-gray-600">Cargando columnas...</span>
                                    </div>
                                </div>
                                
                                <div v-else-if="targetTableColumns.length > 0" class="bg-gray-50 rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                        <div 
                                            v-for="column in targetTableColumns" 
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

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton
                                    class="ml-4"
                                    :class="{ 'opacity-25': form.processing || isLoadingColumns || isLoadingTargetColumns }"
                                    :disabled="form.processing || isLoadingColumns || isLoadingTargetColumns"
                                >
                                    <span v-if="isLoadingColumns || isLoadingTargetColumns">Cargando...</span>
                                    <span v-else>Crear Importador</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 