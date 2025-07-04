<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    importer: Object,
    columnMappings: Array,
    auth: Object,
});
</script>

<template>
    <Head title="Ver Importador" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ver Importador
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">Detalles del Importador</h3>
                            <div class="flex space-x-2">
                                <Link :href="route('importers.edit', importer.id)">
                                    <PrimaryButton>Editar</PrimaryButton>
                                </Link>
                                <Link :href="route('importers.index')">
                                    <PrimaryButton class="bg-gray-600 hover:bg-gray-700">
                                        Volver
                                    </PrimaryButton>
                                </Link>
                            </div>
                        </div>

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

                        <!-- Mapeos de Columnas -->
                        <div v-if="columnMappings && columnMappings.length > 0" class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Mapeos de Columnas</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="space-y-3">
                                    <div 
                                        v-for="mapping in columnMappings" 
                                        :key="mapping.id"
                                        class="flex items-center justify-between bg-white p-3 rounded border"
                                    >
                                        <div class="flex items-center space-x-4">
                                            <span class="font-medium text-gray-700">{{ mapping.source_column }}</span>
                                            <span class="text-gray-400">→</span>
                                            <span class="font-medium text-gray-700">{{ mapping.target_column }}</span>
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 