<template>
    <v-container fluid>
        <v-row class="mb-6">
            <v-col cols="12" class="text-center">
                <h1 class="text-h2 font-weight-bold white--text mb-2">Venta de Productos</h1>
                <v-divider class="mx-auto" width="100" color="white"></v-divider>
            </v-col>
        </v-row>
        <v-row class="mb-6">
            <v-col cols="12" sm="6" md="4" class="mx-auto">
                <v-card elevation="5" class="pa-3">
                    <v-select @update:modelValue="changePiso" v-model="searchForm.search" :items="pisosActivos"
                        item-title="nombre" item-value="id" density="comfortable" label="Seleccionar Piso"
                        prepend-inner-icon="mdi-floor-plan" variant="outlined" class="mb-2"></v-select>
                    <v-checkbox @update:modelValue="changeSelectAll" v-model="allSearch" label="Mostrar todos los pisos"
                        color="indigo"></v-checkbox>
                </v-card>
            </v-col>
        </v-row>
        <v-row dense>
            <v-col v-for="(item, i) in habitacionesSalida" :key="i" cols="12" sm="6" md="4" lg="3">
                <v-card class="room-card" :color="getColor(item)" elevation="3">
                    <v-card-item>
                        <v-row no-gutters align="center">
                            <v-col cols="auto" class="mr-auto">
                                <v-card-title class="text-h3 font-weight-bold white--text">
                                    {{ item.numero }}
                                </v-card-title>
                            </v-col>
                            <v-col cols="auto">
                                <v-chip size="small" color="white" :text-color="getColor(item)"
                                    class="font-weight-bold">
                                    24hr
                                </v-chip>
                            </v-col>
                        </v-row>
                        <v-card-subtitle class="text-subtitle-1 white--text pt-2">
                            {{ item.relacion_tipo.nombre }}
                        </v-card-subtitle>
                    </v-card-item>

                    <v-card-text class="white--text">
                        <v-row no-gutters align="center" class="mb-2">
                            <v-col cols="auto" class="mr-2">
                                <v-icon color="white" size="small">mdi-bed</v-icon>
                            </v-col>
                            <v-col>
                                <span class="font-weight-medium">Estado:</span> {{ item.estado_actual }}
                            </v-col>
                        </v-row>
                        <v-row no-gutters align="center" v-if="item.reserva_actual" class="mb-2">
                            <v-col cols="auto" class="mr-2">
                                <v-icon color="white" size="small">mdi-clock-outline</v-icon>
                            </v-col>
                            <v-col>
                                {{ item.reserva_actual.hora_inicio }} - {{ item.reserva_actual.hora_fin }}
                            </v-col>
                        </v-row>
                        <v-row no-gutters align="center" v-if="item.proxima_reserva" class="mb-2">
                            <v-col cols="auto" class="mr-2">
                                <v-icon color="white" size="small">mdi-account</v-icon>
                            </v-col>
                            <v-col>
                                {{ item.proxima_reserva.cliente.nombre }}
                            </v-col>
                        </v-row>
                    </v-card-text>

                    <v-card-actions class="d-flex flex-wrap justify-center pa-2"
                        :style="{ backgroundColor: getColorAction(item) }" data-custom-actions>
                        <v-btn class="ma-1" @click="venderProducto(item)" color="black" prepend-icon="mdi-exit-to-app">
                            Realizar venta
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>

    <v-dialog v-model="dialogVenderProducto" max-width="900" scrollable>
        <v-card class="hotel-sales-dialog">
            <v-form @submit.prevent="registrarConsumo">
                <v-toolbar color="deep-purple darken-3" dark prominent>
                    <v-toolbar-title class="text-h4 font-weight-bold">
                        Realizar Venta
                    </v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="dialogVenderProducto = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar>

                <v-card-text class="pa-6">
                    <v-row class="mb-6">
                        <v-col cols="12" sm="6" md="4">
                            <v-card outlined class="pa-4 info-card">
                                <v-icon large color="indigo">mdi-account</v-icon>
                                <div class="text-subtitle-1 font-weight-bold mt-2">Cliente</div>
                                <div class="text-body-2">{{ contenidoVenderProducto.servicio_actual ?
                                    contenidoVenderProducto.servicio_actual.cliente.paterno : '' }}
                                    {{
                                        contenidoVenderProducto.servicio_actual ?
                                            contenidoVenderProducto.servicio_actual.cliente.materno : '' }}</div>
                            </v-card>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-card outlined class="pa-4 info-card">
                                <v-icon large color="teal">mdi-card-account-details</v-icon>
                                <div class="text-subtitle-1 font-weight-bold mt-2">Cédula de Identidad</div>
                                <div class="text-body-2">{{ contenidoVenderProducto.servicio_actual.cliente.cedula }}
                                </div>
                            </v-card>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-card outlined class="pa-4 info-card">
                                <v-icon large color="amber darken-2">mdi-bed</v-icon>
                                <div class="text-subtitle-1 font-weight-bold mt-2">Número de Habitación</div>
                                <div class="text-body-2">{{ contenidoVenderProducto.numero }}</div>
                            </v-card>
                        </v-col>
                    </v-row>

                    <v-row class="mb-6">
                        <v-col cols="12" sm="6">
                            <v-btn color="success" prepend-icon="mdi-plus-circle" @click="agregarProductos" x-large
                                block elevation="2">
                                Agregar Productos
                            </v-btn>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <p>Seleccionar la forma de pago:</p>
                            <v-radio-group :error-messages="errors.estado" v-model="statePay">
                                <v-radio label="Pagar ahora" value="pagado"></v-radio>
                                <v-radio label="Pagar después" value="pendiente"></v-radio>
                            </v-radio-group>
                            <v-select :error-messages="errors.metodo_pago" v-if="statePay === 'pagado'"
                                v-model="form.metodo_pago" :items="tiposPagos" item-title="text" item-value="value"
                                label="Tipo de Pago" outlined dense></v-select>
                        </v-col>
                    </v-row>
                    <v-row justify="center" align="center" class="text-center">
                        <v-col cols="12">
                            <h1>Lista de Productos</h1>
                            <v-alert v-if="errors.productos" type="error" density="compact" variant="tonal"
                                class="mt-2">
                                {{ errors.productos }}
                            </v-alert>
                        </v-col>
                    </v-row>
                    <v-data-table :headers="headers" :items="productosSeleccionados" :search="search"
                        :items-per-page="5" class="elevation-2 rounded-lg">
                        <template v-slot:item.cantidad="{ item }">
                            <v-text-field v-model.number="item.cantidad" type="number" min="1" dense hide-details
                                outlined class="mt-1 mb-1 quantity-input"></v-text-field>
                        </template>
                        <template v-slot:item.total="{ item }">
                            {{ formatCurrency(item.cantidad * item.precio_venta) }}
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-btn color="error" icon @click.stop="toggleSelection(item)">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                        <template v-slot:body.append>
                            <tr class="total-row">
                                <td colspan="2" class="text-right font-weight-bold">Total</td>
                                <td class="text-center">{{ totalCantidad }}</td>
                                <td class="text-right font-weight-bold">{{ formatCurrency(totalPrecio) }}</td>
                                <td></td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions class="pa-4 bg-grey-lighten-3">
                    <v-spacer></v-spacer>
                    <v-btn :loading="loadingVenta" type="submit" color="deep-purple darken-1"
                        prepend-icon="mdi-cash-register" x-large elevation="2">
                        Procesar Venta
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialogProductos" max-width="700" scrollable>
        <v-card class="hotel-products-dialog">
            <v-toolbar color="secondary" dark flat>
                <v-toolbar-title class="text-h5 font-weight-medium">
                    Productos Disponibles
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon @click="dialogProductos = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-card-text class="pa-4">
                <v-text-field v-model="searchProductos" prepend-inner-icon="mdi-magnify"
                    label="Buscar productos disponibles" single-line hide-details outlined dense
                    class="mb-4"></v-text-field>

                <v-data-table :headers="headersProductos" :items="productosActivos" :search="searchProductos"
                    :items-per-page="5" class="elevation-2">
                    <template v-slot:item.actions="{ item }">
                        <v-btn :color="isSelected(item) ? 'error' : 'success'" @click.stop="toggleSelection(item)"
                            small>
                            {{ isSelected(item) ? 'Quitar' : 'Agregar' }}
                        </v-btn>
                    </template>
                </v-data-table>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions class="pa-4">
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="dialogProductos = false">
                    Confirmar Selección
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { onMounted, ref, watch, reactive, computed } from 'vue';
import useRecepcion from '@/services/useRecepcion';
import useDialog from '@/services/useDialog';
import useConsumo from '@/services/useConsumo';
import { showAlert } from '@/utils/SweetAlert';
const { getHabitacionesRecepcion, getHabitacionesRecepcionSalida, habitacionesSalida } = useRecepcion();
const { agregarConsumo } = useConsumo();
const { getProductosActivos, productosActivos, getPisosActivos, pisosActivos } = useDialog();
const updateTrigger = ref(0);
const dialogVenderProducto = ref(false);
const dialogProductos = ref(false);
const statePay = ref();
const loadingVenta = ref(false);
const errors = reactive({});
const tiposPagos = [
    { value: 'efectivo', text: 'Efectivo' },
    { value: 'tarjeta', text: 'Tarjeta' },
    { value: 'transferencia', text: 'Transferencia' },
    { value: 'qr', text: 'QR' },
]

const form = reactive({
    registro_id: '',
    metodo_pago: '',
    estado: '',
    productos: ''
});
const search = ref('')
const allSearch = ref(false);
const contenidoVenderProducto = ref(null);
const headers = [
    { title: 'Producto', key: 'nombre', align: 'start', sortable: true },
    { title: 'Precio', key: 'precio_venta', align: 'end', sortable: true },
    { title: 'Cantidad', key: 'cantidad', align: 'center', sortable: false },
    { title: 'Total', key: 'total', align: 'end', sortable: false },
    { title: 'Acciones', key: 'actions', align: 'center', sortable: false },
];

const headersProductos = [
    { title: 'Producto', key: 'nombre', align: 'start', sortable: true },
    { title: 'Precio', key: 'precio_venta', align: 'end', sortable: true },
    { title: 'Acciones', key: 'actions', align: 'center', sortable: false },
];
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(value);
};
const totalCantidad = computed(() => {
    return productosSeleccionados.value.reduce((sum, item) => sum + (item.cantidad || 0), 0);
});

const totalPrecio = computed(() => {
    return productosSeleccionados.value.reduce((sum, item) => sum + (item.cantidad || 0) * item.precio_venta, 0);
});

const productosSeleccionados = ref([])

const registrarConsumo = async () => {
    loadingVenta.value = true
    try {
        form.registro_id = contenidoVenderProducto.value.servicio_actual.id
        form.estado = statePay.value
        form.productos = productosSeleccionados.value
        form.total = totalPrecio.value
        await agregarConsumo(form)
        limpiarDialogRegistro()
        dialogVenderProducto.value = false
        showAlert('Confirmado', 'El registro ha sido confirmado.', 'success');
    }
    catch (error) {
        if (error.response) {
            errors.metodo_pago = error.response.data.data.metodo_pago
            errors.total = error.response.data.data.total
            errors.estado = error.response.data.data.estado
            errors.productos = error.response.data.data.productos
        }
    }
    finally {
        loadingVenta.value = false
    }
}
const limpiarDialogRegistro = () => {
    form.registro_id = ''
    form.metodo_pago = ''
    form.total = ''
    form.productos = ''
    form.estado = ''
    errors.metodo_pago = ''
    errors.total = ''
    errors.productos = ''
    errors.estado = ''
    statePay.value = null
    productosSeleccionados.value = []

}
const agregarProductos = () => {
    dialogProductos.value = true
}
function isSelected(item) {
    return productosSeleccionados.value.some(p => p.id === item.id)
}

const toggleSelection = (item) => {

    const index = productosSeleccionados.value.findIndex(producto => producto.id === item.id);
    if (index === -1) {
        productosSeleccionados.value.push({
            ...item,
            producto_id: item.id,
            cantidad: 1
        });
    } else {
        productosSeleccionados.value.splice(index, 1);
    }
}
onMounted(() => {
    getProductosActivos();
    getPisosActivos();
    getHabitacionesRecepcionSalida(searchForm);
    setInterval(() => {
        updateTrigger.value++;
    }, 5000);
    productosSeleccionados.value.forEach(item => {
        if (!item.cantidad) {
            item.cantidad = 1;
        }
    });
});
const searchForm = reactive({
    search: 1,
})
watch(() => updateTrigger.value, async () => {
    await getHabitacionesRecepcionSalida(searchForm);
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

const getColorAction = (item) => {
    switch (item.estado_actual) {
        case 'reservada':
            return 'red';
        case 'limpieza':
            return '#31BFE9';
        case 'desocupado':
            return '#047A11';
        case 'preparado para reserva':
            return '#C5C200';
        case 'reserva con retraso':
            return '#D18903';
        case 'ocupado':
            return '#A42A14';
        case 'tiempo desalojo':
            return '#D2822E';
        case 'Sobrepasado el Tiempo':
            return '#F62C04';
        case 'confirmar reserva':
            return '#C5C200';
        default:
            break;
    }
}
const venderProducto = (item) => {
    contenidoVenderProducto.value = item
    dialogVenderProducto.value = true
}
const changeSelectAll = async () => {
    if (allSearch.value === true) {
        searchForm.search = 1
        await getHabitacionesRecepcion(searchForm);
    } else {

        searchForm.search = ''
        await getHabitacionesRecepcion(searchForm);
    }
}
</script>

<style scoped>
.hotel-sales-dialog {
    border-radius: 16px;
    overflow: hidden;
}

.info-card {
    transition: all 0.3s;
    border-radius: 12px;
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.quantity-input {
    max-width: 80px !important;
}

.total-row {
    font-weight: bold;
    background-color: #f0f4f8;
}

.total-row td {
    border-top: 2px solid #7e57c2;
}

.v-data-table {
    border-radius: 12px;
    overflow: hidden;
}

.quantity-input {
    max-width: 80px !important;
}

.room-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    border: 2px solid black;
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