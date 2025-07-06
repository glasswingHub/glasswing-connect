<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    importer: Object,
    records: Object,
});

const columns = computed(() => {
    if (props.records.data.length === 0) return [];
    return Object.keys(props.records.data[0]);
});

function goToPage(page) {
    router.get(route('import-records.index', props.importer.id), { page }, { preserveState: true });
}
</script>

<template>
    <Head :title="`Registros de ${importer.name}`" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Registros de {{ importer.name }}
                <br />
                Tabla: {{ importer.source_table }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
                            <Link :href="route('imports.index')" class="text-blue-600 hover:underline">&larr; Volver a Importadores</Link>
                        </div>
                        <div v-if="records.data.length === 0" class="text-center py-8">
                            <p class="text-gray-500">No hay registros disponibles en la tabla <b>{{ importer.source_table }}</b>.</p>
                        </div>
                        <div v-else>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th v-for="col in columns" :key="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ col }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(row, idx) in records.data" :key="idx">
                                            <td v-for="col in columns" :key="col" class="px-4 py-2 whitespace-nowrap">
                                                {{ row[col] }}
                                            </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <Link :href="route('import-records.show', { importer: importer.id, record: row['id'] })" class="text-blue-600 hover:underline">
                                                Ver
                                            </Link>
                                        </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Paginación -->
                            <div class="flex justify-center mt-6 space-x-2">
                                <button
                                    v-for="page in records.last_page"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :class="[
                                        'px-3 py-1 rounded',
                                        page === records.current_page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                    ]"
                                >
                                    {{ page }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 