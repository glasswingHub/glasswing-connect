<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    role: {
        type: Object,
        required: true
    },
    showRoute: {
        type: String,
        default: 'roles.show'
    },
    editRoute: {
        type: String,
        default: 'roles.edit'
    },
    destroyRoute: {
        type: String,
        default: 'roles.destroy'
    }
});
</script>

<template>
    <div class="flex space-x-2">
        <Link 
            :href="route(showRoute, role.id)" 
            class="text-blue-600 hover:text-blue-900"
        >
            Ver
        </Link>
        <Link 
            :href="route(editRoute, role.id)" 
            class="text-indigo-600 hover:text-indigo-900"
        >
            Editar
        </Link>
        <button
            @click="$inertia.delete(route(destroyRoute, role.id))"
            class="text-red-600 hover:text-red-900"
            onclick="return confirm('¿Está seguro de que desea eliminar este rol?')"
        >
            Eliminar
        </button>
    </div>
</template> 