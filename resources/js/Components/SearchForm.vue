<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Buscar...'
    },
    routeName: {
        type: String,
        required: true
    },
    searchParam: {
        type: String,
        default: 'search'
    },
    showClearButton: {
        type: Boolean,
        default: true
    },
    clearButtonText: {
        type: String,
        default: 'Limpiar'
    },
    searchButtonText: {
        type: String,
        default: 'Buscar'
    }
});

const emit = defineEmits(['search', 'clear']);

const searchTerm = ref('');

onMounted(() => {
    // Set the search term from URL params if it exists
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get(props.searchParam);
    if (searchParam) {
        searchTerm.value = searchParam;
    }
});

const handleSearch = () => {
    const params = {};
    params[props.searchParam] = searchTerm.value;
    
    router.get(route(props.routeName), params);
    emit('search', searchTerm.value);
};

const clearSearch = () => {
    searchTerm.value = '';
    router.get(route(props.routeName));
    emit('clear');
};
</script>

<template>
    <div class="mb-6">
        <form @submit.prevent="handleSearch" class="flex gap-4">
            <div class="flex-1">
                <input
                    v-model="searchTerm"
                    type="text"
                    :placeholder="placeholder"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
            </div>
            <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                {{ searchButtonText }}
            </button>
            <button
                v-if="searchTerm && showClearButton"
                type="button"
                @click="clearSearch"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
            >
                {{ clearButtonText }}
            </button>
        </form>
    </div>
</template> 