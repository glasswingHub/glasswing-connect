<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageActions from '@/Components/PageActions.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchForm from '@/Components/SearchForm.vue';
import PaginationInfo from '@/Components/PaginationInfo.vue';
import RoleActions from '@/Components/RoleActions.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ListEmpty from '@/Components/ListEmpty.vue';

defineProps({
    roles: Object,
    auth: Object,
});

const pageActions = [
    {
        href: route('roles.create'),
        label: 'Crear Rol'
    }
]
</script>

<template>
    <Head title="Roles" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader title="Roles" />
        </template>

        <PageActions 
            title="Listado de Roles"
            :actions="pageActions"
        />

        <SearchForm
            route-name="roles.index"
            placeholder="Buscar por nombre de rol..."
        />

        <PaginationInfo :users="roles" />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permisos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Usuarios
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="role in roles.data" :key="role.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex flex-wrap gap-1">
                                <span 
                                    v-for="permission in role.permissions" 
                                    :key="permission.id"
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"
                                >
                                    {{ permission.name.replace('_', ' ') }}
                                </span>
                                <span v-if="role.permissions.length === 0" class="text-gray-500 text-xs">
                                    Sin permisos
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ role.users_count || 0 }} usuarios
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <RoleActions :role="role" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="roles.links" />

        <ListEmpty :count="roles.data?.length" />

    </AuthenticatedLayout>
</template> 