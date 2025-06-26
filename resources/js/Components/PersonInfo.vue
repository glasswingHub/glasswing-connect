<script setup>
import { defineProps } from 'vue';

defineProps({
    registration: {
        type: Object,
        required: true
    }
});

// Mapeo de etiquetas para los campos
const fieldLabels = {
    id: 'ID',
    idF: 'IDF',
    pais: 'País',
    fechaNac: 'Fecha de Nacimiento',
    nombres: 'Nombres',
    apellidos: 'Apellidos',
    sexo: 'Sexo',
    nacionalidad: 'Nacionalidad',
    identidad: 'Identidad',
    tipoParticipante: 'Tipo de Participante',
    personalinst: 'Personal Inst.',
    perfil: 'Perfil',
    subtipo: 'Subtipo',
    idSede: 'Sede',
    estudioAlcanzado: 'Estudio Alcanzado',
    autovozimg: 'Autorización Voz/Imagen',
    estudia: 'Estudia',
    grado: 'Grado',
    seccion: 'Sección',
    turno: 'Turno',
    discapacidad: 'Discapacidad',
    telefono: 'Teléfono',
    correo: 'Correo',
    departamento: 'Departamento',
    municipio: 'Municipio',
    nuevogw: 'Nuevo GW',
    userId: 'Usuario',
    deleted: 'Eliminado',
    Importado: 'Importado',
    fechaMedI: 'Fecha Med. Inicial',
    fechaMedF: 'Fecha Med. Final',
    created_at: 'Creado',
    updated_at: 'Actualizado'
};
</script>

<template>
    <dl class="divide-y divide-gray-200">
        <template v-for="(label, key) in fieldLabels" :key="key">
            <div class="py-3 flex justify-between text-sm">
                <dt class="font-medium text-gray-600">{{ label }}</dt>
                <dd class="text-gray-900 text-right">
                    {{ registration[key] ?? '—' }}
                </dd>
            </div>
        </template>
    </dl>
</template> 