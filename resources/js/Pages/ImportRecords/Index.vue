<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageActions from '@/Components/PageActions.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ListEmpty from '@/Components/ListEmpty.vue';
import ColumnValueTranslator from '@/Components/ColumnValueTranslator.vue';

const props = defineProps({
    importer: Object,
    records: Object,
    columns: Array,
});

function goToPage(page) {
    router.get(route('import-records.index', props.importer.id), { page }, { preserveState: true });
}

const pageActions = [
    {
        href: route('imports.index'),
        label: 'Volver a Importadores',
    }
]
</script>

<template>
    <Head :title="`Registros de ${importer.name}`" />
    <AuthenticatedLayout>

        <template #header>
            <PageHeader 
                :title="`Registros de ${importer.name}`"
                :subtitle="`Tabla ${importer.source_table}`"
            />
        </template>

        <PageActions 
            :title="`Registros de ${importer.name}`"
            :actions="pageActions"
        />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th v-for="col in columns" :key="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ col.label }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(record, idx) in records.data" :key="idx">
                        <td v-for="col in columns" :key="col" class="px-4 py-2 whitespace-nowrap">
                        <ColumnValueTranslator 
                            :column="col"
                            :record="record"
                        />
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <Link :href="route('import-records.show', { importer: importer.id, record: record['id'] })" class="text-blue-600 hover:underline">
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

        <ListEmpty :count="records.data?.length" />

    </AuthenticatedLayout>
</template> 