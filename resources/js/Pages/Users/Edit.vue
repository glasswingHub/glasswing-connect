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
    user: Object,
    auth: Object,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    active: props.user.active,
});

const submit = () => {
    form.put(route('users.update', props.user.id));
};

const pageActions = [
    {
        href: route('users.show', props.user.id),
        label: 'Ver detalle'
    },
    {
        href: route('users.index'),
        label: 'Volver al listado',
    }
]
</script>

<template>
    <Head title="Editar Usuario" />

    <AuthenticatedLayout>

        <template #header>
            <PageHeader 
                title="Editar Usuario"
            />
        </template>

        <PageActions 
            title="Editar Usuario"
            :actions="pageActions"
        />

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="name" value="Nombre" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center">
                <Checkbox
                    id="active"
                    v-model:checked="form.active"
                />
                <InputLabel for="active" value="Usuario Activo" class="ml-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Actualizar Usuario
                </PrimaryButton>
            </div>
        </form>

    </AuthenticatedLayout>
</template> 