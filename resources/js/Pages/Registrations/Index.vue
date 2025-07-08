<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import PaginationInfo from '@/Components/PaginationInfo.vue';
import SearchForm from '@/Components/SearchForm.vue';
import OptionSelector from '@/Components/OptionSelector.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ListEmpty from '@/Components/ListEmpty.vue';

const props = defineProps({
    registrations: {
        type: Object,
        required: true
    },
    groups: {
        type: Array,
        default: () => []
    },
    currentGroup: {
        type: Object,
        default: null
    }
});
</script>

<template>
    <Head title="Personas" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader 
                title="Lista de Personas"
            />
        </template>

        <!-- Header with create button -->
        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                Personas Registradas
            </h3>
        </div>

        <!-- Options Selector -->
        <OptionSelector
            :options="groups"
            :selected-option="currentGroup"
            route-name="registrations.set_group"
        />

        <!-- Search Form -->
        <SearchForm
            route-name="registrations.index"
            placeholder="Buscar por nombres, apellidos o identidad..."
        />

        <div class="px-6">
            <PaginationInfo :registrations="registrations" />
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Identidad
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombres
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Apellidos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Teléfono
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha de creación
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="registration in registrations.data" :key="registration.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ registration.identidad }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ registration.nombres || '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ registration.apellidos || '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ registration.telefono || '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ registration.created_at ? new Date(registration.created_at).toLocaleDateString() : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <Link
                                    :href="route('registrations.show', registration.id)"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Ver
                                </Link>
                                <Link
                                    v-if="registration.Importado === null"
                                    :href="route('registrations.import', registration.id)"
                                    class="text-green-600 hover:text-green-900"
                                >
                                    Importar
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="registrations.links" />

        <ListEmpty :count="registrations.data?.length" />

    </AuthenticatedLayout>
</template>
