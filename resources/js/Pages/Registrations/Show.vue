<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PersonInfo from '@/Components/PersonInfo.vue';

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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detalle de Persona
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
