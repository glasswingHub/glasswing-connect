<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PersonInfo from '@/Components/PersonInfo.vue';

import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  registration: Object
});

const message = ref('');
const loading = ref(false);
const page = usePage();

async function handleImport() {
  message.value = '';
  loading.value = true;
  try {
    const response = await axios.post(
      `/registrations/${props.registration.id}/process_import`,
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
  <Head title="Detalle de Persona" />
  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto p-4">
      <form @submit.prevent="handleImport">
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

      <div v-if="registration" class="mt-8">
        <h2 class="text-xl font-bold mb-2">Información del Registro</h2>
        <PersonInfo :registration="registration" />
        <div class="mt-8">
              <Link :href="route('registrations.index')"
                      class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                  ← Volver al listado
              </Link>
          </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>


