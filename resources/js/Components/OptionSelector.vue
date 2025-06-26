<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    options: {
        type: Array,
        default: () => []
    },
    selectedOption: {
        type: Object,
        default: null
    },
    label: {
        type: String,
        default: 'Seleccionar grupo'
    },
    placeholder: {
        type: String,
        default: 'Seleccione un grupo'
    },
    routeName: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['option-changed']);

const selectedOption = ref(props.selectedOption);

const handleOptionChange = async (option) => {
    selectedOption.value = option;
    
    try {
        await axios.post(route(props.routeName), {
            group: option
        });
        emit('option-changed', option);
    } catch (error) {
        console.error('Error saving selected option:', error);
    }
};

// Watch for prop changes
watch(() => props.selectedOption, (newValue) => {
    selectedOption.value = newValue;
});
</script>

<template>
    <div class="mb-6">
        <label for="options-selector" class="block text-sm font-medium text-gray-700 mb-2">
            {{ label }}
        </label>
        <select
            id="options-selector"
            v-model="selectedOption"
            @change="handleOptionChange(selectedOption)"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
            <option value="">{{ placeholder }}</option>
            <option
                v-for="option in options"
                :key="option.code"
                :value="option"
            >
                {{ option.value }}
            </option>
        </select>
        <div v-if="selectedOption" class="mt-2 text-sm text-gray-600">
            Opción seleccionada: <span class="font-medium">{{ selectedOption.value }}</span>
        </div>
    </div>
</template> 