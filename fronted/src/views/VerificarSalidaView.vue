<template>
    <v-row class="mb-6">
        <v-col cols="12" class="text-center">
            <h1 class="text-h2 font-weight-bold white--text mb-2">Gestión de Salidas</h1>
            <v-divider class="mx-auto" width="100" color="white"></v-divider>
        </v-col>
    </v-row>
    <v-row dense>
        <v-col v-for="(item, i) in habitacionesSalida" :key="i" cols="12" sm="6" md="4" lg="3">
            <v-card class="room-card" :color="getColor(item)" elevation="8">
                <div class="room-status-banner">{{ item.estado_actual }}</div>
                <v-card-item>
                    <v-card-title class="text-h3 font-weight-bold text-center mb-2">
                        {{ item.numero }}
                    </v-card-title>
                    <v-card-subtitle class="text-subtitle-1 text-center">
                        {{ item.relacion_tipo.nombre }}
                    </v-card-subtitle>
                </v-card-item>

                <v-card-text>
                    <v-row no-gutters align="center" v-if="item.reserva_actual" class="mb-2">
                        <v-col cols="auto" class="mr-2">
                            <v-icon color="primary">mdi-clock-outline</v-icon>
                        </v-col>
                        <v-col>
                            {{ item.reserva_actual.hora_inicio }} - {{ item.reserva_actual.hora_fin }}
                        </v-col>
                    </v-row>

                    <v-row no-gutters align="center" v-if="item.proxima_reserva" class="mb-2">
                        <v-col cols="auto" class="mr-2">
                            <v-icon color="primary">mdi-account</v-icon>
                        </v-col>
                        <v-col>
                            {{ item.proxima_reserva.cliente.nombre }}
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-card-actions class="d-flex flex-wrap justify-center">
                    <v-btn @click="verServicio(item)" color="info" class="ma-1" prepend-icon="mdi-eye">
                        Ver servicio
                    </v-btn>
                    <v-btn class="ma-1" @click="checkOut(item)" color="black" prepend-icon="mdi-exit-to-app">
                        Salir
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-col>
    </v-row>
    <v-dialog v-model="dialogServicioActual" max-width="600" scrollable>
        <v-card class="room-info-dialog">
            <v-toolbar color="indigo" prominent dark>
                <v-toolbar-title class="text-h5">
                    Información del Servicio
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon @click="closeDialog">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-chip color="indigo" text-color="white" size="x-large" class="mb-4">
                                Habitación {{ contenidoServicio.numero }}
                            </v-chip>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-list-item prepend-icon="mdi-clock-start">
                                <v-list-item-title>Hora inicio</v-list-item-title>
                                <v-list-item-subtitle>{{ contenidoServicio.servicio_actual.hora_inicio
                                    }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-list-item prepend-icon="mdi-clock-end">
                                <v-list-item-title>Hora final</v-list-item-title>
                                <v-list-item-subtitle>{{ contenidoServicio.servicio_actual.hora_fin
                                    }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-col>

                        <v-col cols="12">
                            <v-list-item prepend-icon="mdi-comment-text">
                                <v-list-item-title>Observaciones</v-list-item-title>
                                <v-list-item-subtitle>{{ contenidoServicio.servicio_actual.observacion ?
                                    contenidoServicio.servicio_actual.observacion : 'Sin observaciones'
                                    }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-col>

                        <v-col cols="12">
                            <v-list-item prepend-icon="mdi-account">
                                <v-list-item-title>Cliente</v-list-item-title>
                                <v-list-item-subtitle>
                                    {{ `${contenidoServicio.servicio_actual.cliente.paterno}
                                    ${contenidoServicio.servicio_actual.cliente.materno}
                                    ${contenidoServicio.servicio_actual.cliente.nombre}` }}
                                </v-list-item-subtitle>
                            </v-list-item>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-list-item prepend-icon="mdi-card-account-details">
                                <v-list-item-title>Cédula de identidad</v-list-item-title>
                                <v-list-item-subtitle>{{ contenidoServicio.servicio_actual.cliente.cedula
                                    }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-list-item prepend-icon="mdi-room-service">
                                <v-list-item-title>Tipo de servicio</v-list-item-title>
                                <v-list-item-subtitle>{{ contenidoServicio.check_tipo }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-col>

                        <v-col cols="12">
                            <v-chip :color="getStatusColor(contenidoServicio)" text-color="white" size="large">
                                {{ contenidoServicio.estado_actual }}
                            </v-chip>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>
        </v-card>
    </v-dialog>
    <v-dialog v-model="dialogServicioSalida" max-width="1000" scrollable>
        <v-form @submit.prevent="procesarSalida">
            <v-card class="room-info-dialog">
                <v-toolbar color="indigo" prominent dark>
                    <v-toolbar-title class="text-h5">
                        Información del Servicio
                    </v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="closeDialog">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar>

                <v-card-text>
                    <v-container>
                        <v-row class="mb-6">
                            <v-col cols="12" sm="4">
                                <v-card outlined class="pa-4 info-card" height="100%">
                                    <v-icon large color="indigo" class="mb-2">mdi-account</v-icon>
                                    <div class="text-subtitle-1 font-weight-bold">Cliente</div>
                                    <div class="text-body-2">
                                        {{ contenidoServicio.servicio_actual?.cliente.paterno ?? '' }}
                                        {{ contenidoServicio.servicio_actual?.cliente.materno ?? '' }}
                                    </div>
                                </v-card>
                            </v-col>
                            <v-col cols="12" sm="4">
                                <v-card outlined class="pa-4 info-card" height="100%">
                                    <v-icon large color="teal" class="mb-2">mdi-card-account-details</v-icon>
                                    <div class="text-subtitle-1 font-weight-bold">Cédula de Identidad</div>
                                    <div class="text-body-2">
                                        {{ contenidoServicio.servicio_actual?.cliente.cedula ?? 'No disponible' }}
                                    </div>
                                </v-card>
                            </v-col>
                            <v-col cols="12" sm="4">
                                <v-card outlined class="pa-4 info-card" height="100%">
                                    <v-icon large color="amber darken-2" class="mb-2">mdi-bed</v-icon>
                                    <div class="text-subtitle-1 font-weight-bold">Número de Habitación</div>
                                    <div class="text-body-2">{{ contenidoServicio.numero }}</div>
                                </v-card>
                            </v-col>
                        </v-row>
                        <v-row class="mb-6">
                            <v-col cols="12">
                                <v-card outlined>
                                    <v-card-title class="text-h6 indigo--text">
                                        Resumen de Costos
                                    </v-card-title>
                                    <v-card-text>
                                        <v-row>
                                            <v-col cols="12" sm="6">
                                                <v-list-item two-line>
                                                    <v-list-item-content>
                                                        <v-list-item-title>Total servicio habitación</v-list-item-title>
                                                        <v-list-item-subtitle class="text-h5 font-weight-bold">
                                                            {{ formatPrice(totalServicio) }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-col>
                                            <v-col cols="12" sm="6">
                                                <v-list-item two-line>
                                                    <v-list-item-content>
                                                        <v-list-item-title>Costos extras</v-list-item-title>
                                                        <v-list-item-subtitle>
                                                            <v-text-field type="number" min="0"
                                                                v-model.number="costosExtras" prefix="Bs."
                                                                dense></v-text-field>
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-col>
                                        </v-row>
                                        <v-divider class="my-3"></v-divider>
                                        <v-row>
                                            <v-col cols="12" sm="4">
                                                <v-list-item two-line>
                                                    <v-list-item-content>
                                                        <v-list-item-title>Horas consumidas</v-list-item-title>
                                                        <v-list-item-subtitle>{{ horasConsumidas
                                                            }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-list-item two-line>
                                                    <v-list-item-content>
                                                        <v-list-item-title>Precio primera hora</v-list-item-title>
                                                        <v-list-item-subtitle>{{ formatPrice(precioFijo)
                                                            }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-list-item two-line>
                                                    <v-list-item-content>
                                                        <v-list-item-title>Precio horas adicionales</v-list-item-title>
                                                        <v-list-item-subtitle>{{ formatPrice(precioDecremento)
                                                            }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-col>
                                        </v-row>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                        <v-row class="mb-6">
                            <v-col cols="12">
                                <v-card color="indigo" dark>
                                    <v-card-text class="text-center">
                                        <div class="text-h5 font-weight-bold">
                                            Total de Consumo
                                        </div>
                                        <div class="text-h3 mt-2">
                                            {{ formatPrice(calcularTotalGeneral) }}
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12">
                                <v-card outlined>
                                    <v-card-title class="text-h6 indigo--text">
                                        Detalles de Consumo
                                        <v-alert v-if="errors.consumo" type="error" density="compact" variant="tonal"
                                            class="mt-2">
                                            {{ errors.consumo }}
                                        </v-alert>
                                    </v-card-title>
                                    <v-card-text>
                                        <v-data-table :headers="[
                                            { title: 'Producto', value: 'producto' },
                                            { title: 'Cantidad', value: 'cantidad' },
                                            { title: 'Precio', value: 'precio' },
                                            { title: 'Estado', value: 'estado' },
                                            { title: 'Método de Pago', value: 'metodo_pago' }
                                        ]" :items="contenidoServicio.servicio_actual?.consumo?.detalles ?? []"
                                            :items-per-page="5" class="elevation-1">
                                            <template v-slot:item.precio="{ item }">
                                                {{ formatCurrency(item.precio) }}
                                            </template>
                                            <template v-slot:item.estado="{ item }">
                                                <v-chip :color="item.estado === 'pagado' ? 'success' : 'warning'"
                                                    text-color="white" small class="mb-2">
                                                    {{ item.estado }}
                                                </v-chip>
                                                <template v-if="item.estado === 'pendiente'">
                                                    <v-select v-model="item.metodo_pago" :items="tiposPagos"
                                                        item-title="text" item-value="value" label="Método de Pago"
                                                        outlined dense class="mt-2"
                                                        @update:modelValue="actualizarMetodoPago(item)"></v-select>
                                                </template>
                                            </template>
                                            <template v-slot:item.metodo_pago="{ item }">
                                                <v-icon :color="getPaymentMethodColor(item.metodo_pago)" small
                                                    class="mr-1">
                                                    {{ getPaymentMethodIcon(item.metodo_pago) }}
                                                </v-icon>
                                                {{ item.metodo_pago }}
                                            </template>
                                        </v-data-table>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions class="pa-4 bg-grey-lighten-3">
                    <v-spacer></v-spacer>
                    <v-btn :loading="loadingSalida" type="submit" color="deep-purple darken-1"
                        prepend-icon="mdi-cash-register" x-large elevation="2">
                        Procesar salida y limpiar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script setup>
import { onMounted, ref, watch, computed } from 'vue';
import useRecepcion from '@/services/useRecepcion';
import useConsumo from '@/services/useConsumo';
import { showAlert, showAlertConfirmation } from '@/utils/SweetAlert';
import { reactive } from 'vue';
const { getHabitacionesRecepcion, habitacionesRecepcion, checkSalida, getHabitacionesRecepcionSalida, habitacionesSalida } = useRecepcion();
const { pagarDetalle } = useConsumo();
const updateTrigger = ref(0);
const horaActual = ref('');
const dialogServicioActual = ref(false);
const contenidoServicio = ref(null);
const dialogServicioSalida = ref(false);
const costosExtras = ref(0);
const loadingSalida = ref(false);
const horasConsumidas = computed(() => contenidoServicio.value.servicio_actual.total_horas);
const precioFijo = computed(() => contenidoServicio.value.relacion_tipo.precio_fijo);
const precioDecremento = computed(() => contenidoServicio.value.relacion_tipo.precio_decremento);

const tiposPagos = [
    { value: 'efectivo', text: 'Efectivo' },
    { value: 'tarjeta', text: 'Tarjeta' },
    { value: 'transferencia', text: 'Transferencia' },
    { value: 'qr', text: 'QR' },
]
const errors = reactive({});
const form = reactive({
    total_pago: 0,
})
const detalleForm = reactive({
    metodo_pago: '',
})
const totalServicio = computed(() => {
    const extras = Number(costosExtras.value) || 0;
    if (horasConsumidas.value <= 1) {
        return precioFijo.value + extras;
    } else {
        return (precioFijo.value + (horasConsumidas.value - 1) * precioDecremento.value) + extras;
    }
});
const calcularTotalGeneral = computed(() => {
    const consumoTotal = Number(contenidoServicio.value.servicio_actual?.consumo?.total) || 0;
    const servicioTotal = Number(totalServicio.value) || 0;
    const extras = Number(costosExtras.value) || 0;

    return consumoTotal + servicioTotal + extras;
});
const formatPrice = (price) => {
    return `Bs.${parseFloat(price).toFixed(2)}`;
};

const obtenerHoraActual = () => {
    const ahora = new Date();
    const horas = ahora.getHours().toString().padStart(2, '0');
    const minutos = ahora.getMinutes().toString().padStart(2, '0');
    horaActual.value = `${horas}:${minutos}`;
};
const getPaymentMethodIcon = (method) => {
    switch (method) {
        case 'efectivo': return 'mdi-cash';
        case 'tarjeta': return 'mdi-credit-card';
        case 'qr': return 'mdi-qrcode';
        default: return 'mdi-help-circle';
    }
}
const getPaymentMethodColor = (method) => {
    switch (method) {
        case 'efectivo': return 'green';
        case 'tarjeta': return 'blue';
        case 'qr': return 'purple';
        default: return 'grey';
    }
}
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(value);
};
const checkOut = async (item) => {
    contenidoServicio.value = item;
    dialogServicioSalida.value = true;

}
const procesarSalida = async () => {

    try {
        form.total_pago = totalServicio.value
        await checkSalida(contenidoServicio.value.servicio_actual.id, form)
        dialogServicioSalida.value = false
        showAlert('Confirmado', 'El registro ha sido confirmado.', 'success');
    } catch (error) {
        if (error.response) {
            errors.consumo = error.response.data.data.consumo
        }
    }

}
const actualizarMetodoPago = async (item) => {
    detalleForm.metodo_pago = item.metodo_pago
    try {
        await pagarDetalle(item.id, detalleForm)
    } catch (error) {
        console.log(error)
    }
}
onMounted(() => {
    obtenerHoraActual();
    getHabitacionesRecepcionSalida();
    setInterval(obtenerHoraActual, 60000);
    setInterval(() => {
        updateTrigger.value++;
    }, 5000);
});

watch(() => updateTrigger.value, async () => {
    await getHabitacionesRecepcionSalida();
})
const getColor = (item) => {
    switch (item.estado_actual) {
        case 'reservada':
            return 'red';
        case 'limpieza':
            return '#31BFE9';
        case 'desocupado':
            if (item.relacion_tipo.nombre === 'doble') {
                return '#138505';
            }
            return '#74F365';
        case 'preparado para reserva':
            return '#F7D125';
        case 'reserva con retraso':
            return '#E59710';
        case 'ocupado':
            return '#F75E57';
        case 'tiempo desalojo':
            return '#F19B56';
        case 'Sobrepasado el Tiempo':
            return '#FD0000';
        default:
            break;
    }
}
const verServicio = (item) => {
    dialogServicioActual.value = true
    contenidoServicio.value = item
}
const getStatusColor = (item) => {
    switch (item.estado_actual.toLowerCase()) {
        case 'ocupado': return 'red';
        case 'disponible': return 'green';
        case 'mantenimiento': return 'orange';
        default: return 'grey';
    }
};

</script>

<style scoped>
.room-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    border: 2px solid #000000;
}

.room-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2) !important;
}

.room-status-banner {
    position: absolute;
    top: 20px;
    right: -35px;
    transform: rotate(45deg);
    padding: 5px 40px;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.8rem;
    color: white;
    z-index: 1;
}

.room-card.ocupado .room-status-banner {
    background-color: #ff5252;
}

.room-card.disponible .room-status-banner {
    background-color: #4caf50;
}

.room-card.mantenimiento .room-status-banner {
    background-color: #ff9800;
}

.room-card .v-card-title {
    font-size: 3rem !important;
    color: #1a237e;
}

.room-card .v-card-subtitle {
    font-weight: 500;
    color: #3f51b5;
}

.room-card .v-card-actions {
    background-color: #f5f5f5;
    padding: 16px;
}

.room-info-dialog {
    border-radius: 16px;
    overflow: hidden;
}

.room-info-dialog .v-list-item {
    padding: 16px;
    background-color: #f5f5f5;
    border-radius: 8px;
    margin-bottom: 8px;
    transition: background-color 0.3s;
}

.room-info-dialog .v-list-item:hover {
    background-color: #e0e0e0;
}

.room-info-dialog .v-list-item-title {
    font-weight: bold;
    color: #1a237e;
}

.reservation-form-dialog {
    border-radius: 16px;
    overflow: hidden;
}

.reservation-form-dialog .v-card-title {
    background-color: #f5f5f5;
    border-bottom: 2px solid #3f51b5;
    padding: 16px;
    font-size: 1.25rem;
    font-weight: 500;
}

.reservation-form-dialog .error-text {
    color: #ff5252;
    font-size: 0.875rem;
    margin-top: 4px;
}

.v-btn {
    text-transform: none;
    font-weight: 500;
    letter-spacing: 0.5px;
    border-radius: 20px;
}

.v-card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.v-text-field,
.v-select,
.v-autocomplete {
    margin-bottom: 16px;
}

.v-date-picker,
.v-time-picker {
    border-radius: 8px;
    overflow: hidden;
}

.v-icon {
    font-size: 1.2rem;
}
</style>