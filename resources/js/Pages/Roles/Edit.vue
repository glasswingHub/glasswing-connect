<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PageActions from '@/Components/PageActions.vue';
import PageHeader from '@/Components/PageHeader.vue';

const props = defineProps({
    role: Object,
    permissions: Array,
    selectedPermissions: Array,
    auth: Object,
});

const form = useForm({
    name: props.role.name,
    permission_ids: props.selectedPermissions || [],
});

const submit = () => {
    form.put(route('roles.update', props.role.id));
};

const pageActions = [
    {
        href: route('roles.show', props.role.id),
        label: 'Ver detalle'
    },
    {
        href: route('roles.index'),
        label: 'Volver al listado',
    }
]
</script>

<template>
    <Head title="Editar Rol" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader 
                title="Editar Rol"
            />
        </template>

        <PageActions 
            title="Editar Rol"
            :actions="pageActions"
        />

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="name" value="Nombre del Rol" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    placeholder="Ej: editor, supervisor, etc."
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel value="Permisos" />
                <div class="mt-2 space-y-2">
                    <div v-for="permission in permissions" :key="permission.id" class="flex items-center">
                        <Checkbox
                            :id="'permission_' + permission.id"
                            :value="permission.id"
                            v-model:checked="form.permission_ids"
                        />
                        <InputLabel :for="'permission_' + permission.id" :value="permission.label" class="ml-2" />
                    </div>
                </div>
                <InputError class="mt-2" :message="form.errors.permissions" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Actualizar Rol
                </PrimaryButton>
            </div>
        </form>

    </AuthenticatedLayout>
</template> 