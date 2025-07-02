<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    registration: {
        type: Object,
        required: true
    }
});

// Mapeo de etiquetas para los campos
const fieldLabels = {
    // id: 'ID',
    // idF: 'IDF',
    pais: 'País',
    fechaNac: 'Fecha de nacimiento',
    nombres: 'Nombres',
    apellidos: 'Apellidos',
    sexo: 'Sexo',
    nacionalidad: 'Nacionalidad',
    identidad: 'Identidad',
    // tipoParticipante: 'Tipo de Participante',
    // personalinst: 'Personal Inst.',
    // perfil: 'Perfil',
    // subtipo: 'Subtipo',
    // idSede: 'Sede',
    // estudioAlcanzado: 'Estudio Alcanzado',
    autovozimg: 'Autorización Voz/Imagen',
    // estudia: 'Estudia',
    // grado: 'Grado',
    // seccion: 'Sección',
    // turno: 'Turno',
    discapacidad: 'Discapacidad',
    telefono: 'Teléfono',
    correo: 'Correo',
    // departamento: 'Departamento',
    // municipio: 'Municipio',
    // nuevogw: 'Nuevo GW',
    // userId: 'Usuario',
    // deleted: 'Eliminado',
    // Importado: 'Importado',
    // fechaMedI: 'Fecha Med. Inicial',
    // fechaMedF: 'Fecha Med. Final',
    created_at: 'Fecha creación',
    // updated_at: 'Actualizado'
};

const formatValueByKey = (key) => {
    const value = props.registration[key];
    // Formateo especial para ciertos campos
    switch (key) {
        case 'fechaNac':
        case 'created_at':
            return value ? new Date(value).toLocaleDateString('es-ES') : '—';
        case 'sexo':
            switch (value) {
                case '1':
                    return 'Masculino';
                case '2':
                    return 'Femenino';
                default:
                    return 'Otro';
            }
        case 'discapacidad':
        case 'autovozimg':
            return value == 1 ? 'Sí' : 'No';
        case 'nacionalidad':
            switch (value) {
                case 1:
                    return 'Salvadoreña';
                case '2':
                    return 'Extranjera';
                default:
                    return '—';
            }
        case 'pais':
            switch (value) {
                case '1':
                    return 'Panamá';
                case '2':
                    return 'Nicaragua';
                case '3':
                    return 'Honduras';
                case '4':
                    return 'Guatemala';
                case '5':
                    return 'República Dominicana';
                case '6':
                    return 'Costa Rica';
                case '7':
                    return 'El Salvador';
                default:
                    return '—';
            }
    }
    return value ?? '—';
};
</script>

<template>
    <dl class="divide-y divide-gray-200">
        <template v-for="(label, key) in fieldLabels" :key="key">
            <div class="py-3 flex justify-between text-sm">
                <dt class="font-medium text-gray-600">{{ label }}</dt>
                <dd class="text-gray-900 text-right">
                    {{ formatValueByKey(key) }}
                </dd>
            </div>
        </template>
    </dl>
</template> 