<script setup>
const props = defineProps({
    targetTable: {
        type: String,
        default: ''
    }
});

// Define the required columns based on target table
const getRequiredColumns = () => {
    switch (props.targetTable) {
        case 'volunteerings':
        case 'beneficiaries':
            return ['name', 'surname', 'fechaNac'];
        default:
            return [];
    }
};

const requiredColumns = getRequiredColumns();
</script>

<template>
    <div class="">
        <h3 class="text-lg font-medium text-gray-900">
            Mapeo de Columnas
        </h3>
        <div class="mt-2 p-2 bg-yellow-50 rounded border-l-4 border-yellow-400">
            <p class="text-sm text-gray-500">
                Recuerda que para que un importador este configurado correctamente:
            </p>
            <ul class="list-disc list-inside text-sm text-gray-500 mt-2">
                <li>Debes indicar una columna como Llave primaria y una como Llave país.</li>
                <li>Debes indicar una columna como Llave única si es necesario.</li>
                <li v-if="requiredColumns.length > 0">
                    Cuando la tabla de destino es <span class="font-bold">{{ targetTable }}</span>, 
                    debes mapear las columnas: 
                    <span class="font-bold">{{ requiredColumns.join(', ') }}</span>.
                </li>
            </ul>
        </div>
    </div>
</template> 