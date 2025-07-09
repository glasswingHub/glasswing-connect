<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed, ref  } from 'vue';
import axios from 'axios';
import PageActions from '@/Components/PageActions.vue';
import PageHeader from '@/Components/PageHeader.vue';

const props = defineProps({
    importer: Object,
    record: Object,
    columns: Object,
    beneficiaryTypes: Array,
});

const recordFields = computed(() => {
    if (!props.record) return [];
    return props.columns.map((col) => {
        return {
            field: col.label,
            value: props.record[col.key]
        }
    });
});

const pageActions = [

    {
        href: route('import-records.index', props.importer.id),
        label: 'Volver a Registros',
    }
]

const message = ref('');
const loading = ref(false);
const selectedBeneficiaryType = ref('');

async function handleImport() {
    // Validación para evitar submit si no se ha seleccionado un tipo de beneficiario
    if (!selectedBeneficiaryType.value) {
        message.value = 'Debe seleccionar un tipo de beneficiario antes de importar.';
        return;
    }
    
    loading.value = true;
    try {
        const response = await axios.post(
            `/imports/${props.importer.id}/records/${props.record.id}/process_import`,
            {
                beneficiary_type: selectedBeneficiaryType.value
            }
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
            <PageHeader 
                :title="`Listado de ${importer.name}`"
                :subtitle="`Tabla ${importer.source_table} | ID: ${record.id}`"
            />
        </template>

        <PageActions 
            :title="`Registro de ${importer.name}`"
            :actions="pageActions"
        />
            
        <dl class="divide-y divide-gray-200">
            <template v-for="(field) in recordFields" :key="field">
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

        <!-- Actions -->
        <div class="mt-6">
            
            <form @submit.prevent="handleImport">
                
                <!-- Beneficiary Type Selection -->
                <div v-if="beneficiaryTypes && beneficiaryTypes.length > 0" class="mb-4">
                    <label for="beneficiary_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Beneficiario
                    </label>
                    <select 
                        id="beneficiary_type"
                        v-model="selectedBeneficiaryType"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                        <option value="">Seleccione un tipo de beneficiario</option>
                        <option 
                            v-for="type in beneficiaryTypes" 
                            :key="type.value" 
                            :value="type.value"
                        >
                            {{ type.label }}
                        </option>
                    </select>
                </div>

                <!-- Puedes agregar campos aquí si el import requiere datos adicionales -->
                <button 
                    type="submit" 
                    :disabled="loading"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center gap-2"
                >
                    <svg v-if="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ loading ? 'Importando...' : 'Importar' }}
                </button>
            </form>

            <div v-if="message" class="mt-4 p-2 bg-green-100 text-green-800 rounded">{{ message }}</div>

        </div>
    </AuthenticatedLayout>
</template>