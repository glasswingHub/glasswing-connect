<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    importer: Object,
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 