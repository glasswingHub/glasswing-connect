<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PersonInfo from '@/Components/PersonInfo.vue';
import PageHeader from '@/Components/PageHeader.vue';

defineProps({
    registration: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <Head title="Detalle de Persona" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader 
                title="Detalle de Persona"
            />
        </template>

        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Persona</h3>
            <PersonInfo :registration="registration" />
        </div>
        
        <div class="mt-8">
            <Link :href="route('registrations.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ← Volver al listado
            </Link>
        </div>

    </AuthenticatedLayout>
</template>
