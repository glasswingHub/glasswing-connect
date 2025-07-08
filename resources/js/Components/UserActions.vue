<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    currentUserId: {
        type: [Number, String],
        required: true
    },
    showRoute: {
        type: String,
        default: 'users.show'
    },
    editRoute: {
        type: String,
        default: 'users.edit'
    },
    destroyRoute: {
        type: String,
        default: 'users.destroy'
    },
    restoreRoute: {
        type: String,
        default: 'users.restore'
    },
    forceDeleteRoute: {
        type: String,
        default: 'users.force-delete'
    }
});

const isCurrentUser = computed(() => props.user.id === props.currentUserId);
const isDeleted = computed(() => !!props.user.deleted_at);
</script>

<template>
    <div class="flex space-x-2">
        <!-- Acciones para usuarios activos -->
        <template v-if="!isDeleted">
            <Link 
                :href="route(showRoute, user.id)" 
                class="text-blue-600 hover:text-blue-900"
            >
                Ver
            </Link>
            
            <!-- Solo mostrar editar y eliminar si no es el usuario en sesión -->
            <template v-if="!isCurrentUser">
                <Link 
                    :href="route(editRoute, user.id)" 
                    class="text-indigo-600 hover:text-indigo-900"
                >
                    Editar
                </Link>
                <button
                    @click="$inertia.delete(route(destroyRoute, user.id))"
                    class="text-red-600 hover:text-red-900"
                    onclick="return confirm('¿Está seguro de que desea eliminar este usuario?')"
                >
                    Eliminar
                </button>
            </template>
        </template>
        
        <!-- Acciones para usuarios eliminados -->
        <template v-else>
            <Link 
                :href="route(showRoute, user.id)" 
                class="text-blue-600 hover:text-blue-900"
            >
                Ver
            </Link>
            <button
                @click="$inertia.patch(route(restoreRoute, user.id))"
                class="text-green-600 hover:text-green-900"
                onclick="return confirm('¿Está seguro de que desea restaurar este usuario?')"
            >
                Restaurar
            </button>
            
            <!-- Solo mostrar eliminar permanentemente si no es el usuario en sesión -->
            <template v-if="!isCurrentUser">
                <button
                    @click="$inertia.delete(route(forceDeleteRoute, user.id))"
                    class="text-red-600 hover:text-red-900"
                    onclick="return confirm('¿Está seguro de que desea eliminar permanentemente este usuario? Esta acción no se puede deshacer.')"
                >
                    Eliminar Permanentemente
                </button>
            </template>
        </template>
    </div>
</template> 