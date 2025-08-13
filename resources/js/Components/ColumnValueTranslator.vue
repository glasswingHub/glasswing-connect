<template>
  <span v-if="translatedValue === null" class="text-gray-400 italic">
    Nulo
  </span>
  <span v-else-if="translatedValue === ''" class="text-gray-400 italic">
    Vacío
  </span>
  <span v-else>
    {{ translatedValue }}
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  column: {
    type: Object,
    required: true
  },
  record: {
    type: Object,
    required: true
  },
  beneficiaryTypes: {
    type: Array,
    default: () => []
  },
  shirtSizes: {
    type: Array,
    default: () => []
  },
  states: {
    type: Array,
    default: () => []
  },
  municipalities: {
    type: Array,
    default: () => []
  }
});

const translatedValue = computed(() => {
  return translateColumnValue(props.column, props.record);
});

const translateColumnValue = (col, record) => {
  switch (col.ref) { 
    case 'genre_id':
      return parseInt(record[col.key]) === 1 ? 'Masculino' : 'Femenino';
    case 'voice_image':
    case 'firmaConducta':
    case 'menorEdad':
    case 'permisoAdulto':
      return parseInt(record[col.key]) === 1 ? 'Si' : 'No';
    case 'origin':
      return parseInt(record[col.key]) === 1 ? 'Nacional' : 'Extranjero';
    case 'typeBeneficiary':
      return props.beneficiaryTypes.find(type => type.value === record[col.key])?.label;
    case 'volunteering_shirt_size_id':
      return props.shirtSizes.find(size => size.value === record[col.key])?.label;
    case 'departamento':
      return props.states.find(state => state.value === record[col.key])?.label;
    case 'municipio':
      return props.municipalities.find(municipality => municipality.value === record[col.key])?.label;
    default:
      return record[col.key];
  }
};
</script> 