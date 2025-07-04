<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputSelect from '@/Components/InputSelect.vue';

defineProps({
    targetTableOptions: Array,
});

const form = useForm({
    name: '',
    source_table: '',
    target_table: '',
    active: true,
});

const submit = () => {
    form.post(route('importers.store'));
};
</script>

<template>
    <Head title="Crear Importador" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear Importador
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre -->
                                <div>
                                    <InputLabel for="name" value="Nombre" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <!-- Tabla Origen -->
                                <div>
                                    <InputLabel for="source_table" value="Tabla Origen" />
                                    <TextInput
                                        id="source_table"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.source_table"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.source_table" />
                                </div>

                                <!-- Tabla Destino -->
                                <div>
                                    <InputLabel for="target_table" value="Tabla Destino" />
                                    <InputSelect
                                        id="target_table"
                                        class="mt-1 block w-full"
                                        v-model="form.target_table"
                                        required
                                        :options="targetTableOptions"
                                    />
                                    <InputError class="mt-2" :message="form.errors.target_table" />
                                </div>

                                <!-- Activo -->
                                <div class="flex items-center">
                                    <Checkbox
                                        id="active"
                                        v-model:checked="form.active"
                                    />
                                    <InputLabel for="active" value="Activo" class="ml-2" />
                                    <InputError class="mt-2" :message="form.errors.active" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton
                                    class="ml-4"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Crear Importador
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 