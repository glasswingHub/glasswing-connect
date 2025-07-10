<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ListEmpty from '@/Components/ListEmpty.vue';

defineProps({
    importers: Array,
    auth: Object,
});
</script>

<template>
    <Head title="Importaciones" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader title="Importaciones" />
        </template>

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium">Importadores Activos</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
                v-for="importer in importers" 
                :key="importer.id"
                class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">
                            {{ importer.name }}
                        </h4>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Activo
                        </span>
                    </div>
                    <div class="space-y-2 mb-4">
                        <p v-if="importer.description" class="text-sm text-gray-600 mt-1">
                            {{ importer.description }}
                        </p>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Tabla Origen:</span> {{ importer.source_table }}
                        </div>
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Tabla Destino:</span> 
                            {{ importer.target_table === 'volunteerings' ? 'Volunteerings' : 'Beneficiaries' }}
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <Link 
                            :href="route('import-records.index', importer.id)"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Ver Registros
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <ListEmpty 
            :count="importers.length" 
            title="No hay importadores activos disponibles."
        />

    </AuthenticatedLayout>
</template> 