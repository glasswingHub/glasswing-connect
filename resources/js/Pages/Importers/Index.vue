<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageActions from '@/Components/PageActions.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchForm from '@/Components/SearchForm.vue';
import PaginationInfo from '@/Components/PaginationInfo.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ListEmpty from '@/Components/ListEmpty.vue';

defineProps({
    importers: Object,
    auth: Object,
});

const pageActions = [
    {
        href: route('importers.create'),
        label: 'Crear Importador'
    }
]
</script>

<template>
    <Head title="Importadores" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader title="Importadores" />
        </template>

        <PageActions 
            title="Listado de Importadores"
            :actions="pageActions"
        />

        <SearchForm
            route-name="importers.index"
            placeholder="Buscar por nombre o tabla origen..."
        />

        <PaginationInfo :users="importers" />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tabla Origen
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tabla Destino
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            País
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="importer in importers.data" :key="importer.id" :class="{ 'bg-red-50': importer.deleted_at }">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                :class="[
                                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                    importer.deleted_at
                                        ? 'bg-red-100 text-red-800'
                                        : importer.active
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-yellow-100 text-yellow-800'
                                ]"
                            >
                                {{ importer.deleted_at ? 'Eliminado' : (importer.active ? 'Activo' : 'Inactivo') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ importer.source_table }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ importer.target_table === 'volunteerings' ? 'Volunteerings' : 'Beneficiaries' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ importer.country_code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ importer.name }}
                            <span v-if="importer.deleted_at" class="ml-2 text-xs text-red-600">(Eliminado)</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <!-- Acciones para importadores activos -->
                                <template v-if="!importer.deleted_at">
                                    <Link :href="route('importers.show', importer.id)" class="text-blue-600 hover:text-blue-900">
                                        Ver
                                    </Link>
                                    <Link :href="route('importers.edit', importer.id)" class="text-indigo-600 hover:text-indigo-900">
                                        Editar
                                    </Link>
                                    <button
                                        @click="$inertia.delete(route('importers.destroy', importer.id))"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('¿Está seguro de que desea eliminar este importador?')"
                                    >
                                        Eliminar
                                    </button>
                                </template>
                                
                                <!-- Acciones para importadores eliminados -->
                                <template v-else>
                                    <Link :href="route('importers.show', importer.id)" class="text-blue-600 hover:text-blue-900">
                                        Ver
                                    </Link>
                                    <button
                                        @click="$inertia.patch(route('importers.restore', importer.id))"
                                        class="text-green-600 hover:text-green-900"
                                        onclick="return confirm('¿Está seguro de que desea restaurar este importador?')"
                                    >
                                        Restaurar
                                    </button>
                                    <button
                                        @click="$inertia.delete(route('importers.force-delete', importer.id))"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('¿Está seguro de que desea eliminar permanentemente este importador? Esta acción no se puede deshacer.')"
                                    >
                                        Eliminar Permanentemente
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="importers.links" />

        <ListEmpty :count="importers.data?.length" />

    </AuthenticatedLayout>
</template> 