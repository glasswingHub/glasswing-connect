<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PageActions from '@/Components/PageActions.vue';
import PageHeader from '@/Components/PageHeader.vue';

const props = defineProps({
    role: Object,
    auth: Object,
});

const pageActions = [
    {
        href: route('roles.edit', props.role.id),
        label: 'Editar'
    },
    {
        href: route('roles.index'),
        label: 'Volver al listado',
    }
]
</script>

<template>
    <Head title="Detalles del Rol" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader 
                title="Detalles del Rol"
            />
        </template>

        <PageActions 
            title="Información del Rol"
            :actions="pageActions"
        />

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium">Información del Rol</h3>
            <div class="flex space-x-2">
                <Link :href="route('roles.edit', role.id)">
                    <PrimaryButton>Editar</PrimaryButton>
                </Link>
                <Link :href="route('roles.index')">
                    <PrimaryButton class="bg-gray-600 hover:bg-gray-700">Volver</PrimaryButton>
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Nombre</h4>
                <p class="mt-1 text-sm text-gray-900">{{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}</p>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Fecha de Creación</h4>
                <p class="mt-1 text-sm text-gray-900">{{ new Date(role.created_at).toLocaleDateString() }}</p>
            </div>

            <div v-if="role.updated_at">
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Última Actualización</h4>
                <p class="mt-1 text-sm text-gray-900">{{ new Date(role.updated_at).toLocaleDateString() }}</p>
            </div>
        </div>

        <div class="mt-8">
            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Permisos Asignados</h4>
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div v-if="role.permissions && role.permissions.length > 0" class="p-4">
                    <div class="flex flex-wrap gap-2">
                        <span 
                            v-for="permission in role.permissions" 
                            :key="permission.id"
                            class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800"
                        >
                            {{ permission.name.replace('_', ' ') }}
                        </span>
                    </div>
                </div>
                <div v-else class="p-4 text-gray-500 text-sm">
                    Este rol no tiene permisos asignados.
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Usuarios con este Rol</h4>
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div v-if="role.users && role.users.length > 0" class="divide-y divide-gray-200">
                    <div v-for="user in role.users" :key="user.id" class="px-4 py-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                <p class="text-sm text-gray-500">{{ user.email }}</p>
                            </div>
                            <Link :href="route('users.show', user.id)" class="text-blue-600 hover:text-blue-900 text-sm">
                                Ver usuario
                            </Link>
                        </div>
                    </div>
                </div>
                <div v-else class="p-4 text-gray-500 text-sm">
                    No hay usuarios asignados a este rol.
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template> 