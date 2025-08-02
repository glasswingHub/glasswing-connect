<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageActions from '@/Components/PageActions.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchForm from '@/Components/SearchForm.vue';
import PaginationInfo from '@/Components/PaginationInfo.vue';
import UserActions from '@/Components/UserActions.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ListEmpty from '@/Components/ListEmpty.vue';

defineProps({
    users: Object,
    auth: Object,
});

const pageActions = [
    {
        href: route('users.create'),
        label: 'Crear Usuario'
    }
]
</script>

<template>
    <Head title="Usuarios" />

    <AuthenticatedLayout>

        <template #header>      
            <PageHeader title="Usuarios" />
        </template>
                        
        <PageActions 
            title="Listado de Usuarios"
            :actions="pageActions"
        />
        
        <SearchForm
            route-name="users.index"
            placeholder="Buscar por nombres o correo electrónico..."
        />
        
        <div class="px-6">
            <PaginationInfo :users="users" />
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rol
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="user in users.data" :key="user.id" :class="{ 'bg-red-50': user.deleted_at, 'bg-blue-50': user.id === auth.user.id }">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ user.name }}
                            <span v-if="user.deleted_at" class="ml-2 text-xs text-red-600">(Eliminado)</span>
                            <span v-if="user.id === auth.user.id" class="ml-2 text-xs text-blue-600">(Tú)</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ user.email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span v-if="user.roles && user.roles.length > 0" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ user.roles[0].name.charAt(0).toUpperCase() + user.roles[0].name.slice(1) }}
                            </span>
                            <span v-else class="text-gray-500 text-xs">Sin rol</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                :class="[
                                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                    user.deleted_at
                                        ? 'bg-red-100 text-red-800'
                                        : user.active
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-yellow-100 text-yellow-800'
                                ]"
                            >
                                {{ user.deleted_at ? 'Eliminado' : (user.active ? 'Activo' : 'Inactivo') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <UserActions 
                                :user="user" 
                                :current-user-id="auth.user.id" 
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="users.links" />

        <ListEmpty :count="users.data?.length" />

    </AuthenticatedLayout>
</template> 