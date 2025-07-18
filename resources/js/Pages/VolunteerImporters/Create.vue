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
import ColumnMapping from '@/Components/ColumnMapping.vue';
import PageActions from '@/Components/PageActions.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ColumnMappingInstructions from '@/Components/ColumnMappingInstructions.vue';

const props = defineProps({
    sourceTableOptions: Array,
    users: Array,
    selectedUserIds: Array,
    countries: Array,
    targetTableColumns: Array,
    columnMappings: Object,
});

const form = useForm({
    name: '',
    source_table: '',
    target_table: 'volunteerings',
    active: true,
    country_code: '',
    description: '',
    column_mappings: props.columnMappings,
    user_ids: props.selectedUserIds || [],
});

const isLoadingColumns = ref(false);
const sourceTableColumns = ref([]);

// Watch for changes in source_table
watch(() => form.source_table, async (newValue) => {
    if (newValue) {
        await fetchSourceTableColumns(newValue);
    } else {
        sourceTableColumns.value = [];
    }
});

const fetchSourceTableColumns = async (tableName) => {
    isLoadingColumns.value = true;
    
    try {
        const response = await window.fetchWithCSRF(route('volunteer_importers.get-source-table-columns'), {
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

const submit = () => {
    form.post(route('volunteer_importers.store'));
};

const pageActions = [
    {
        href: route('importers.index'),
        label: 'Volver al listado',
    }
]
</script>

<template>
    <Head title="Crear Importador" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader title="Crear Importador" />
        </template>
        
        <PageActions 
            title="Crear Importador"
            :actions="pageActions"
        />

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

                <!-- Código de País -->
                <div>
                    <InputLabel for="country_code" value="Código de País" />
                    <InputSelect
                        id="country_code"
                        class="mt-1 block w-full"
                        v-model="form.country_code"
                        required
                        :options="countries"
                    />
                    <InputError class="mt-2" :message="form.errors.country_code" />
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <InputLabel for="description" value="Descripción" />
                    <textarea
                        id="description"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        v-model="form.description"
                        rows="3"
                        placeholder="Descripción opcional del importador..."
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.description" />
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

                <!-- Usuarios -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900">
                        Usuarios asociados
                    </h3>
                    <p class="text-sm text-gray-500">
                        Selecciona los usuarios que tendrán acceso a este importador.
                    </p>
                    <div class="mt-2 space-y-2 max-h-48 overflow-y-auto">
                        <div v-for="user in users" :key="user.id" class="flex items-center">
                            <Checkbox
                                :id="'user_' + user.id"
                                :value="user.id"
                                v-model:checked="form.user_ids"
                                class="mr-2"
                            />
                            <InputLabel :for="'user_' + user.id" :value="user.name + ' (' + user.email + ')'" class="text-sm" />
                        </div>
                        <div v-if="users.length === 0" class="text-gray-500 text-sm">
                            No hay usuarios disponibles
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.user_ids" />
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

            <!-- Mapeo de Columnas -->

            <div v-if="sourceTableColumns.length > 0" class="mt-8" v-for="(columns, table_name) in targetTableColumns">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Columnas de la tabla destino: <span class="font-bold">{{ table_name }}</span>
                </h3>

                <ColumnMappingInstructions :target-table="table_name" />

                <ColumnMapping
                    :target-table="table_name"
                    :source-columns="sourceTableColumns"
                    :target-columns="columns"
                    :existing-mappings="form.column_mappings[table_name]"
                    @update:mappings="form.column_mappings[table_name] = $event"
                />
                <InputError class="mt-2" :message="form.errors.column_mappings" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing || isLoadingColumns }"
                    :disabled="form.processing || isLoadingColumns"
                >
                    <span v-if="isLoadingColumns">Cargando...</span>
                    <span v-else>Crear Importador</span>
                </PrimaryButton>
            </div>
        </form>
                    
    </AuthenticatedLayout>
</template> 