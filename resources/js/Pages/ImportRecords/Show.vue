<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed, ref  } from 'vue';
import axios from 'axios';

const props = defineProps({
    importer: Object,
    record: Object,
});

const recordFields = computed(() => {
    if (!props.record) return [];
    return Object.entries(props.record).map(([key, value]) => ({
        field: key,
        value: value
    }));
});

const message = ref('');
const loading = ref(false);

async function handleImport() {
    loading.value = true;
    try {
        const response = await axios.post(
            `/imports/${props.importer.id}/records/${props.record.id}/import`,
            {}
        );
        message.value = response.data.message;
    } catch (error) {
        message.value = error.response?.data?.message || 'Error al importar.';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head :title="`Registro - ${importer.name}`" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Registro de {{ importer.name }}
                <br />
                <span class="text-sm text-gray-600">Tabla: {{ importer.source_table }} | ID: {{ record.id }}</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Breadcrumb -->
                        <div class="mb-6">
                            <Link :href="route('imports.index')" class="text-blue-600 hover:underline">
                                &larr; Volver a Importadores
                            </Link>
                            <span class="mx-2 text-gray-400">/</span>
                            <Link :href="route('import-records.index', importer.id)" class="text-blue-600 hover:underline">
                                Registros de {{ importer.name }}
                            </Link>
                            <span class="mx-2 text-gray-400">/</span>
                            <span class="text-gray-600">Ver Registro</span>
                        </div>

                        <!-- Record Details -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                Detalles del Registro
                            </h3>
                            
                            <dl class="divide-y divide-gray-200">
                                <template v-for="(field) in recordFields" :key="field.field">
                                    <div class="py-3 flex justify-between text-sm">
                                        <dt class="font-medium text-gray-600">{{ field.field }}</dt>
                                        <dd class="text-gray-900 text-right">
                                            <span v-if="field.value === null" class="text-gray-400 italic">
                                                Nulo
                                            </span>
                                            <span v-else-if="field.value === ''" class="text-gray-400 italic">
                                                Vacío
                                            </span>
                                            <span v-else>
                                                {{ field.value }}
                                            </span>
                                        </dd>
                                    </div>
                                </template>
                            </dl>
                            
                            <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                                <div 
                                    v-for="field in recordFields" 
                                    :key="field.field"
                                    class="bg-white p-4 rounded-lg border border-gray-200"
                                >
                                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">
                                        {{ field.field }}
                                    </div>
                                    <div class="text-gray-900 break-words">
                                        <span v-if="field.value === null" class="text-gray-400 italic">
                                            Nulo
                                        </span>
                                        <span v-else-if="field.value === ''" class="text-gray-400 italic">
                                            Vacío
                                        </span>
                                        <span v-else>
                                            {{ field.value }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex justify-between items-center">
                            <Link 
                                :href="route('import-records.index', importer.id)" 
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Volver a la Lista
                            </Link>
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>