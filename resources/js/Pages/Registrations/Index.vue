<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import PaginationInfo from '@/Components/PaginationInfo.vue';
import SearchForm from '@/Components/SearchForm.vue';
import OptionSelector from '@/Components/OptionSelector.vue';

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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Lista de Personas
            </h2>
        </template>

        

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
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

                        <!-- Empty state -->
                        <div v-if="!registrations.data || registrations.data.length === 0" class="text-center py-12">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron registros</h3>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            <Pagination :links="registrations.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
