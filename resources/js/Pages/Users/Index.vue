<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchForm from '@/Components/SearchForm.vue';
import PaginationInfo from '@/Components/PaginationInfo.vue';

defineProps({
    users: Object,
    auth: Object,
});
</script>

<template>
    <Head title="Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Usuarios
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">Listado de Usuarios</h3>
                            <Link :href="route('users.create')">
                                <PrimaryButton>Crear Usuario</PrimaryButton>
                            </Link>
                        </div>
                        
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
                                            <div class="flex space-x-2">
                                                <!-- Acciones para usuarios activos -->
                                                <template v-if="!user.deleted_at">
                                                    <Link :href="route('users.show', user.id)" class="text-blue-600 hover:text-blue-900">
                                                        Ver
                                                    </Link>
                                                    
                                                    <!-- Solo mostrar editar y eliminar si no es el usuario en sesión -->
                                                    <template v-if="user.id !== auth.user.id">
                                                        <Link :href="route('users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900">
                                                            Editar
                                                        </Link>
                                                        <button
                                                            @click="$inertia.delete(route('users.destroy', user.id))"
                                                            class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('¿Está seguro de que desea eliminar este usuario?')"
                                                        >
                                                            Eliminar
                                                        </button>
                                                    </template>
                                                    <template v-else>
                                                        <span class="text-gray-400">No disponible</span>
                                                    </template>
                                                </template>
                                                
                                                <!-- Acciones para usuarios eliminados -->
                                                <template v-else>
                                                    <Link :href="route('users.show', user.id)" class="text-blue-600 hover:text-blue-900">
                                                        Ver
                                                    </Link>
                                                    <button
                                                        @click="$inertia.patch(route('users.restore', user.id))"
                                                        class="text-green-600 hover:text-green-900"
                                                        onclick="return confirm('¿Está seguro de que desea restaurar este usuario?')"
                                                    >
                                                        Restaurar
                                                    </button>
                                                    
                                                    <!-- Solo mostrar eliminar permanentemente si no es el usuario en sesión -->
                                                    <template v-if="user.id !== auth.user.id">
                                                        <button
                                                            @click="$inertia.delete(route('users.force-delete', user.id))"
                                                            class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('¿Está seguro de que desea eliminar permanentemente este usuario? Esta acción no se puede deshacer.')"
                                                        >
                                                            Eliminar Permanentemente
                                                        </button>
                                                    </template>
                                                    <template v-else>
                                                        <span class="text-gray-400">No disponible</span>
                                                    </template>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            <Pagination :links="users.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 