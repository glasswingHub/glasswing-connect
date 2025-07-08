<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageActions from '@/Components/PageActions.vue';
import PageHeader from '@/Components/PageHeader.vue';

const props = defineProps({
    importer: Object,
    columnMappings: Array,
    associatedUsers: Array,
    auth: Object,
});

const pageActions = [
    {
        href: route('importers.edit', props.importer.id),
        label: 'Editar'
    },
    {
        href: route('importers.index'),
        label: 'Volver al listado',
    }
]
</script>

<template>
    <Head title="Ver Importador" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader title="Ver Importador" />
        </template>

        <PageActions 
            title="Detalles del Importador"
            :actions="pageActions"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Nombre</h4>
                <p class="mt-1 text-sm text-gray-900">{{ importer.name }}</p>
            </div>

            <!-- Tabla Origen -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Tabla Origen</h4>
                <p class="mt-1 text-sm text-gray-900">{{ importer.source_table }}</p>
            </div>

            <!-- Tabla Destino -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Tabla Destino</h4>
                <p class="mt-1 text-sm text-gray-900">
                    {{ importer.target_table === 'volunteerings' ? 'Volunteerings' : 'Beneficiaries' }}
                </p>
            </div>

            <!-- Estado -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Estado</h4>
                <span
                    :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1',
                        importer.active
                            ? 'bg-green-100 text-green-800'
                            : 'bg-yellow-100 text-yellow-800'
                    ]"
                >
                    {{ importer.active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>

            <!-- Fecha de Creación -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Fecha de Creación</h4>
                <p class="mt-1 text-sm text-gray-900">{{ new Date(importer.created_at).toLocaleDateString() }}</p>
            </div>

            <!-- Última Actualización -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Última Actualización</h4>
                <p class="mt-1 text-sm text-gray-900">{{ new Date(importer.updated_at).toLocaleDateString() }}</p>
            </div>
        </div>

        <!-- Usuarios Asociados -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Usuarios Asociados</h3>
            <div v-if="associatedUsers && associatedUsers.length > 0" class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div 
                        v-for="user in associatedUsers" 
                        :key="user.id"
                        class="bg-white p-3 rounded border flex items-center space-x-3"
                    >
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-indigo-600 font-medium text-sm">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ user.email }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            No hay usuarios asociados
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Este importador no tiene usuarios asociados. Edita el importador para asociar usuarios.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mapeos de Columnas -->
        <div v-if="columnMappings && columnMappings.length > 0" class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Mapeos de Columnas</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="space-y-3">
                    <div 
                        v-for="mapping in columnMappings" 
                        :key="mapping.id"
                        class="flex flex-col md:flex-row md:items-center md:justify-between bg-white p-3 rounded border"
                    >
                        <div class="flex items-center space-x-4">
                            <span class="font-medium text-gray-700">{{ mapping.source_column }}</span>
                            <span class="text-gray-400">→</span>
                            <span class="font-medium text-gray-700">{{ mapping.target_column }}</span>
                            <span v-if="mapping.display_name" class="ml-2 text-gray-500 text-sm">({{ mapping.display_name }})</span>
                        </div>
                        <div class="flex space-x-2 mt-2 md:mt-0">
                            <span v-if="mapping.primary_key" class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Llave primaria</span>
                            <span v-if="!mapping.show_in_list" class="inline-block bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">Oculto en lista</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="mt-8">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            No hay mapeos de columnas configurados
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Este importador no tiene mapeos de columnas configurados. Edita el importador para agregar mapeos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </AuthenticatedLayout>
</template> 