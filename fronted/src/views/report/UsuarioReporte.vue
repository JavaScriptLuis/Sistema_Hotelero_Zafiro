<template>
    <v-container fluid class="pa-4 ma-0 bg-grey-lighten-4">
        <v-card class="elevation-3 rounded-lg overflow-hidden">
            <v-card-title class="text-h5 font-weight-bold pa-4 primary white--text">
                Reporte de Personal
            </v-card-title>

            <v-card-text class="pa-4">
                <v-row justify="center" align="center" class="mb-4">
                    <v-col cols="12" sm="3">
                        <v-autocomplete v-model="form.personal_id" :items="personalActivos" :item-title="getFullName"
                            item-value="id" label="Personal" variant="outlined" prepend-inner-icon="mdi-account"
                            class="rounded-lg" :error-messages="errors.personal_id" hide-details="auto"
                            dense></v-autocomplete>
                    </v-col>
                    <v-col cols="12" sm="3">
                        <v-text-field v-model="form.fecha_inicio" type="date" label="Fecha inicio" variant="outlined"
                            :error-messages="errors.fecha_inicio" hide-details="auto" dense></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="3">
                        <v-text-field v-model="form.fecha_fin" type="date" label="Fecha fin" variant="outlined"
                            :error-messages="errors.fecha_fin" hide-details="auto" dense></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="3" class="d-flex align-center">
                        <v-btn color="primary" @click="getReportePersonal" prepend-icon="mdi-magnify"
                            class="rounded-pill" elevation="2">
                            Generar Reporte
                        </v-btn>
                    </v-col>
                </v-row>

                <v-divider class="mb-4"></v-divider>

                <v-row justify="space-between" align="center" class="mb-4">
                    <v-col cols="auto">
                        <v-chip color="primary" text-color="white" class="font-weight-bold">
                            Total Registros: {{ dataReporte.registros ? dataReporte.registros.length : 0 }}
                        </v-chip>
                    </v-col>
                    <v-col cols="auto">
                        <v-btn color="success" @click="generateExcel" prepend-icon="mdi-file-excel" class="rounded-pill"
                            elevation="2">
                            Exportar a Excel
                        </v-btn>
                    </v-col>
                </v-row>

                <v-data-table :headers="headers" :items="dataReporte.registros || []" :items-per-page="10"
                    class="elevation-1 rounded-lg" :no-data-text="'No hay datos disponibles'"
                    :no-results-text="'No se encontraron resultados'" :loading="loading"
                    loading-text="Cargando... Por favor, espere">
                    <template v-slot:item.cliente="{ item }">
                        {{ getFullName(item.cliente) }} - {{ item.cliente.cedula }}
                    </template>
                    <template v-slot:item.total_pago="{ item }">
                        <span class="font-weight-bold">{{ formatCurrency(item.total_pago) }}</span>
                    </template>
                    <template v-slot:item.total_consumos="{ item }">
                        <span class="font-weight-medium">{{ formatCurrency(calcularTotalConsumos(item.consumos))
                            }}</span>
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn @click="verDetallesConsumo(item)" color="info" variant="tonal" size="small"
                            class="rounded-pill">
                            Ver Consumos
                        </v-btn>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>

        <!-- Diálogo para mostrar detalles de consumo -->
        <v-dialog v-model="dialogConsumos" max-width="700px">
            <v-card>
                <v-card-title class="text-h5 grey lighten-2">
                    Detalles de Consumo
                </v-card-title>
                <v-card-text>
                    <v-data-table :headers="headersConsumos" :items="consumosSeleccionados" :items-per-page="-1"
                        hide-default-footer class="elevation-1">
                        <template v-slot:item.precio="{ item }">
                            {{ formatCurrency(item.precio) }}
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" text @click="dialogConsumos = false">Cerrar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup>
import { ref, watch, computed, reactive, onMounted } from 'vue'

import { showAlert, showAlertConfirmation } from '@/utils/SweetAlert';
import * as XLSX from 'xlsx';
import useDialog from '@/services/useDialog';
import useReporte from '@/services/useReporte';
const { getPersonalActivos, personalActivos } = useDialog();
const { getAtencionPersonal } = useReporte();

const headers = ref([
    { title: 'ID', value: 'id' },
    { title: 'Cliente', value: 'cliente' },
    { title: 'Habitación', value: 'habitacion' },
    { title: 'Check In', value: 'check_in' },
    { title: 'Check Out', value: 'check_out' },
    { title: 'Total Pago', value: 'total_pago' },
    { title: 'Total Consumos', value: 'total_consumos' },
    { title: 'Acciones', value: 'actions', sortable: false },
]);

const errors = reactive({});
const selectedDate = ref([]);
const form = reactive({
    personal_id: '',
    fecha_inicio: '',
    fecha_fin: '',
    hora_inicio: '',
    hora_fin: '',
});

const dataReporte = ref([])

const horaFin = ref(null);
const minutoFin = ref(null);
const horaInicio = ref(null);
const minutoInicio = ref(null);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(value);
};

const calcularTotalConsumos = (consumos) => {
    return consumos.reduce((total, consumo) => total + consumo.total, 0);
};
const getReportePersonal = async () => {
    try {
        const response = await getAtencionPersonal(form);
        dataReporte.value = response.data
    } catch (error) {
        if (error.response) {
            errors.personal_id = error.response.data.data.personal_id
            errors.fecha_inicio = error.response.data.data.fecha_inicio
            errors.fecha_fin = error.response.data.data.fecha_fin
        }
    }
}
const getFullName = (item) => {
    if (!item) return '';
    return `${item.nombre} ${item.paterno} ${item.materno}`;
}
watch(() => form.hora_fin, (newValue) => {
    if (newValue) {
        const [h, m] = newValue.split(':');
        horaFin.value = parseInt(h);
        minutoFin.value = parseInt(m);
    }
}, { immediate: true });
watch(() => form.hora_inicio, (newValue) => {
    if (newValue) {
        const [h, m] = newValue.split(':');
        horaInicio.value = parseInt(h);
        minutoInicio.value = parseInt(m);
    }
})


const fechaInicial = computed(() => {
    if (selectedDate.value.length > 0) {
        return selectedDate.value[0]
    } else {
        return null
    }
})
const fechaFinal = computed(() => {
    if (selectedDate.value.length > 1) {
        return selectedDate.value[1]
    } else {
        return selectedDate.value[0] || null
    }
})

function obtenerSoloFecha(fechaHora) {
    if (!fechaHora) return null;
    return fechaHora.split('T')[0];
}

onMounted(() => {
    getPersonalActivos();
})


const dialogConsumos = ref(false);
const consumosSeleccionados = ref([]);
const headersConsumos = [
    { title: 'Producto', value: 'producto' },
    { title: 'Cantidad', value: 'cantidad' },
    { title: 'Precio', value: 'precio' },
    { title: 'Estado', value: 'estado' },
    { title: 'Método de Pago', value: 'metodo_pago' },
];

const verDetallesConsumo = (item) => {
    consumosSeleccionados.value = item.consumos.flatMap(consumo => consumo.detalles);
    dialogConsumos.value = true;
};
const generateExcel = () => {
    if (!dataReporte.value || !dataReporte.value.registros || dataReporte.value.registros.length === 0) {
        showAlert('Error', 'No hay datos para exportar', 'error');
        return;
    }
    const wb = XLSX.utils.book_new();

    const mainData = dataReporte.value.registros.map(registro => {
        const totalConsumos = registro.consumos.reduce((sum, consumo) => sum + consumo.total, 0);
        return {
            'ID': registro.id,
            'Cliente': registro.cliente.paterno + ' ' + registro.cliente.materno + ' ' + registro.cliente.nombre + 'CI: ' + registro.cliente.cedula,
            'Habitación': registro.habitacion,
            'Check In': registro.check_in,
            'Check Out': registro.check_out,
            'Total Pago': registro.total_pago,
            'Total Consumos': totalConsumos
        };
    });
    const wsMain = XLSX.utils.json_to_sheet(mainData);
    XLSX.utils.book_append_sheet(wb, wsMain, "Resumen");

    const consumoData = [];
    dataReporte.value.registros.forEach(registro => {
        consumoData.push({
            'Registro ID': registro.id,
            'Cliente': registro.cliente.paterno + ' ' + registro.cliente.materno + ' ' + registro.cliente.nombre + 'CI: ' + registro.cliente.cedula,
            'Habitación': registro.habitacion,
            'Check In': registro.check_in,
            'Check Out': registro.check_out,
        });
        consumoData.push({});
        consumoData.push({
            'Producto': 'Producto',
            'Cantidad': 'Cantidad',
            'Precio': 'Precio',
            'Estado': 'Estado',
            'Método de Pago': 'Método de Pago'
        });
        if (registro.consumos && registro.consumos.length > 0) {
            registro.consumos.forEach(consumo => {
                consumo.detalles.forEach(detalle => {
                    consumoData.push({
                        'Producto': detalle.producto,
                        'Cantidad': detalle.cantidad,
                        'Precio': detalle.precio,
                        'Estado': detalle.estado,
                        'Método de Pago': detalle.metodo_pago
                    });
                });
            });
        }
        consumoData.push({});
        consumoData.push({});
    });

    const wsConsumo = XLSX.utils.json_to_sheet(consumoData);
    XLSX.utils.book_append_sheet(wb, wsConsumo, "Detalles de Consumo");
    XLSX.writeFile(wb, "Reporte_Personal_Detallado.xlsx");
};
watch(selectedDate, newVal => {
    if (newVal.length > 2) {
        selectedDate.value = newVal.slice(-2)
    }
    selectedDate.value.sort((a, b) => new Date(a) - new Date(b))
})
watch([fechaInicial, fechaFinal], ([newFechaInicial, newFechaFinal]) => {
    if (obtenerSoloFecha(new Date(newFechaInicial).toISOString()) !== obtenerSoloFecha(new Date(newFechaFinal).toISOString()) && obtenerSoloFecha(new Date(fechaInicial.value).toISOString()) !== obtenerSoloFecha(new Date(fechaFinal.value).toISOString())) {
        form.hora_inicio = '00:00:00';
        form.hora_fin = '23:59:59';
    }
});
</script>
<style scoped>
.v-card {
    overflow: hidden;
    border: 2px solid black; /* Agrega un borde negro */
    border-radius: 8px; /* Opcional: añade un radio de borde para suavizar los bordes */
}

.v-data-table {
    background-color: rgba(255, 255, 255, 0.799);
    overflow: hidden;
    font-family: 'Roboto', sans-serif;
}

.v-data-table>>>th {
    background-color: #494949 !important;
    font-weight: bold !important;
    color: #ffffff !important;
    font-size: 0.95rem !important;
}

.v-data-table>>>td {
    font-size: 0.9rem !important;
}

.v-btn {
    text-transform: none !important;
}

.v-card-title {
    font-size: 1.5rem !important;
    background: linear-gradient(45deg, #494949, #494949);
    color: white;
}
</style>