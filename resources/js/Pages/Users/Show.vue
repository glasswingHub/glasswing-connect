<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PageActions from '@/Components/PageActions.vue';
import PageHeader from '@/Components/PageHeader.vue';

const props = defineProps({
    user: Object,
    auth: Object,
});

const pageActions = [
    {
        href: route('users.edit', props.user.id),
        label: 'Editar'
    },
    {
        href: route('users.index'),
        label: 'Volver al listado',
    }
]
</script>

<template>
    <Head title="Detalles del Usuario" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader 
                title="Detalles del Usuario"
            />
        </template>

        <PageActions 
            title="Información del Usuario"
            :actions="pageActions"
        />

        <!-- Alerta para usuarios eliminados -->
        <div v-if="user.deleted_at" class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Usuario Eliminado
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Este usuario fue eliminado el {{ new Date(user.deleted_at).toLocaleDateString() }} a las {{ new Date(user.deleted_at).toLocaleTimeString() }}.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerta para el usuario en sesión -->
        <div v-if="user.id === auth.user.id" class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Tu Perfil
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Este es tu perfil de usuario. Para editar tu información personal, usa la sección de perfil.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium">Información del Usuario</h3>
            <div class="flex space-x-2">
                <!-- Acciones para usuarios activos -->
                <template v-if="!user.deleted_at">
                    <!-- Solo mostrar editar si no es el usuario en sesión -->
                    <template v-if="user.id !== auth.user.id">
                        <Link :href="route('users.edit', user.id)">
                            <PrimaryButton>Editar</PrimaryButton>
                        </Link>
                    </template>
                </template>
                
                <!-- Acciones para usuarios eliminados -->
                <template v-else>
                    <button
                        @click="$inertia.patch(route('users.restore', user.id))"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
                        onclick="return confirm('¿Está seguro de que desea restaurar este usuario?')"
                    >
                        Restaurar
                    </button>
                </template>
                
                <Link :href="route('users.index')">
                    <PrimaryButton class="bg-gray-600 hover:bg-gray-700">Volver</PrimaryButton>
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Nombre</h4>
                <p class="mt-1 text-sm text-gray-900">{{ user.name }}</p>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Email</h4>
                <p class="mt-1 text-sm text-gray-900">{{ user.email }}</p>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Estado</h4>
                <span
                    :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1',
                        user.deleted_at
                            ? 'bg-red-100 text-red-800'
                            : user.active
                            ? 'bg-green-100 text-green-800'
                            : 'bg-yellow-100 text-yellow-800'
                    ]"
                >
                    {{ user.deleted_at ? 'Eliminado' : (user.active ? 'Activo' : 'Inactivo') }}
                </span>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Fecha de Creación</h4>
                <p class="mt-1 text-sm text-gray-900">{{ new Date(user.created_at).toLocaleDateString() }}</p>
            </div>

            <div v-if="user.updated_at">
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Última Actualización</h4>
                <p class="mt-1 text-sm text-gray-900">{{ new Date(user.updated_at).toLocaleDateString() }}</p>
            </div>

            <div v-if="user.deleted_at">
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Fecha de Eliminación</h4>
                <p class="mt-1 text-sm text-gray-900">{{ new Date(user.deleted_at).toLocaleDateString() }} a las {{ new Date(user.deleted_at).toLocaleTimeString() }}</p>
            </div>
        </div>

    </AuthenticatedLayout>
</template> 