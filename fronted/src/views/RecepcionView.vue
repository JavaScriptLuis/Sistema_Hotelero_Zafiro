<template>
    <v-container fluid class="hotel-background">
        <v-row class="mb-6">
            <v-col cols="12" class="text-center">
                <h1 class="text-h2 font-weight-bold white--text mb-2">Gestión de Habitaciones</h1>
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
            <v-col v-for="(item, i) in habitacionesRecepcion" :key="i" cols="12" sm="6" md="4" lg="3">
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
                        <v-btn v-if="item.reserva_actual" color="success" class="ma-1" prepend-icon="mdi-plus"
                            size="small">
                            Agregar tiempo
                        </v-btn>
                        <v-btn v-if="item.proxima_reserva" color="primary" class="ma-1" @click="confirmarEstadia(item)"
                            prepend-icon="mdi-check" size="small">
                            Confirmar reserva
                        </v-btn>
                        <v-btn @click="verServicio(item)" v-if="item.servicio_actual" color="info" class="ma-1"
                            prepend-icon="mdi-eye" size="small">
                            Ver servicio
                        </v-btn>
                        <v-btn v-if="item.reserva_actual" color="error" class="ma-1" @click="checkOut(item)"
                            prepend-icon="mdi-exit-to-app" size="small">
                            Salir
                        </v-btn>
                        <v-btn v-if="item.estado_actual === 'desocupado'" color="white" :text-color="getColor(item)"
                            class="ma-1 text-center disponible-btn" @click="openDialog('crear', item)"
                            prepend-icon="mdi-bed" size="small">
                            <span>Disponible</span>
                            <v-icon end>mdi-chevron-right</v-icon>
                        </v-btn>
                        <v-btn v-if="item.estado_actual === 'limpieza'" color="white" :text-color="getColor(item)"
                            class="ma-1 text-center disponible-btn" @click="updateLimpieza(item)" prepend-icon="mdi-bed"
                            size="small">
                            <span>Teminar limpieza</span>
                            <v-icon end>mdi-chevron-right</v-icon>
                        </v-btn>
                        <v-btn @click="openDialog('editar', item)"
                            v-if="item.check_tipo === 'momento' && item.servicio_actual">
                            <v-icon size="small" color="white">mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn color="white" @click="openHora(item)" v-if="item.servicio_actual">
                            <v-icon size="small" color="white">mdi-plus</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
    <v-dialog v-model="dialog" max-width="1000">
        <v-card>
            <v-toolbar dense color="indigo white--text">
                <v-toolbar-title>{{ dialogTitle }}</v-toolbar-title>
                <v-btn @click="closeDialog" icon class="ml-auto" color="red">
                    <v-icon color="red">mdi-close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-form @submit.prevent="actionButton" lazy-validation>
                <v-card-text>
                    <v-container>
                        <v-row>

                        </v-row>

                    </v-container>
                </v-card-text>
                <v-card-actions class="justify-center">
                    <v-spacer></v-spacer>
                    <v-btn @click="closeDialog" dark>Cancelar</v-btn>
                    <v-btn :loading="loading" type="submit"
                        :color="accion === 'editar' ? 'light-green-darken-4' : 'light-green-darken-4'" dark>
                        {{ accion === 'crear' ? 'Guardar' : 'Editar' }}
                    </v-btn>
                    <v-btn color="red" @click="deleteItem">Eliminar</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
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
    <v-dialog v-model="dialogUsar" max-width="1000" scrollable>
        <v-card class="reservation-form-dialog">
            <v-toolbar color="indigo darken-2" prominent dark>
                <v-toolbar-title class="text-h4 font-weight-bold">
                    <v-icon large class="mr-2">mdi-calendar-check</v-icon>
                    Registrar Estadía
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon @click="closeDialog">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-form @submit.prevent="actionButton" lazy-validation>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-card outlined class="mb-4 pa-4">
                                    <v-card-title class="text-h6">Información del Cliente</v-card-title>
                                    <v-autocomplete :error-messages="erros.cliente_id" v-model="form.cliente_id"
                                        label="Seleccionar Cliente" :items="usuariosActivos" :item-title="getFullName"
                                        item-value="id" prepend-inner-icon="mdi-account" variant="outlined"
                                        color="indigo"></v-autocomplete>
                                    <div v-for="(v, k) in erros" :key="k" class="error-text">
                                        {{ v.cliente_id }}
                                    </div>
                                </v-card>
                            </v-col>
                            <v-col cols="12" sm="6" class="d-flex align-center">
                                <v-btn color="primary" @click="openDialogClient" prepend-icon="mdi-plus"
                                    class="rounded-pill">
                                    Agregar Cliente
                                </v-btn>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-card outlined class="mb-4 pa-4">
                                    <v-card-title class="text-h6">Detalles de Pago</v-card-title>
                                    <v-text-field :error-messages="erros.adelanto" v-model="form.adelanto"
                                        label="Adelanto" variant="outlined" prefix="Bs." prepend-inner-icon="mdi-cash"
                                        color="green"></v-text-field>
                                </v-card>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12">
                                <v-card outlined class="mb-4 pa-4">
                                    <v-card-title class="text-h6">Observaciones</v-card-title>
                                    <v-textarea v-model="form.observacion" label="Añadir observaciones" rows="3"
                                        variant="outlined" auto-grow prepend-inner-icon="mdi-comment-text"
                                        color="blue"></v-textarea>
                                </v-card>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="7">
                                <v-alert v-if="erros.fecha_inicio" type="error" density="compact" variant="tonal">
                                    {{ erros.fecha_inicio }}
                                </v-alert>
                                <v-card outlined class="mb-4">
                                    <v-card-title class="text-h6 pa-4">Selección de Fechas</v-card-title>
                                    <v-locale-provider locale="es">
                                        <v-date-picker v-model="selectedDate" multiple title="Seleccionar fecha"
                                            locale="es" width="100%" color="indigo" elevation="0"
                                            class="mx-auto"></v-date-picker>
                                    </v-locale-provider>
                                </v-card>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="5">
                                <v-alert v-if="erros.hora_inicio" type="error" density="compact" variant="tonal"
                                    class="mt-2">
                                    {{ erros.hora_inicio }}
                                </v-alert>
                                <v-card outlined class="mb-4 pa-4">
                                    <v-card-title class="text-h6">Hora de entrada</v-card-title>
                                    <v-time-picker v-model="form.hora_inicio" width="100%" title="Hora final"
                                        :hour="horaInicio" :minute="minutoInicio" @update:hour="actualizarHoraIni"
                                        @update:minute="actualizarMinutoIni" :allowed-minutes="allowedStep"
                                        format="24hr" color="indigo"></v-time-picker>

                                </v-card>
                            </v-col>
                            <v-col cols="12" md="5">
                                <v-alert v-if="erros.hora_fin" type="error" density="compact" variant="tonal"
                                    class="mt-2">
                                    {{ erros.hora_fin }}
                                </v-alert>
                                <v-card outlined class="mb-4 pa-4">
                                    <v-card-title class="text-h6">Hora de Salida</v-card-title>
                                    <v-time-picker v-model="form.hora_fin" width="100%" title="Hora final"
                                        :hour="horaFin" :minute="minutoFin" @update:hour="actualizarHora"
                                        @update:minute="actualizarMinuto" :allowed-minutes="allowedStep" format="24hr"
                                        color="indigo"></v-time-picker>

                                </v-card>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions class="pa-4">
                    <v-btn variant="outlined" color="grey" @click="closeDialog">
                        <v-icon left>mdi-close</v-icon>
                        Cancelar
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn :loading="loading" type="submit" color="success" variant="elevated">
                        <v-icon left>{{ accion === 'crear' ? 'mdi-content-save' : 'mdi-pencil' }}</v-icon>
                        {{ accion === 'crear' ? 'Guardar Reserva' : 'Actualizar Reserva' }}
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
    <v-dialog v-model="dialogClient" max-width="800" scrollable>
        <v-card class="rounded-lg elevation-12">
            <v-img
                src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                height="200" class="align-end" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)">
                <v-card-title class="text-h4 font-weight-bold text-white pb-4">
                    Registrar Cliente
                </v-card-title>
            </v-img>

            <v-form @submit.prevent="saveClient" lazy-validation>
                <v-card-text class="pa-6">
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="formClient.nombre" label="Nombres"
                                    :error-messages="errosClient.nombre" variant="outlined"
                                    prepend-inner-icon="mdi-account" class="rounded-lg"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="formClient.paterno" label="Apellido Paterno"
                                    :error-messages="errosClient.paterno" variant="outlined"
                                    prepend-inner-icon="mdi-account-tie" class="rounded-lg"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="formClient.materno" label="Apellido Materno"
                                    :error-messages="errosClient.materno" variant="outlined"
                                    prepend-inner-icon="mdi-account-tie-woman" class="rounded-lg"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="formClient.cedula" label="Cédula de Identidad"
                                    :error-messages="errosClient.cedula" variant="outlined"
                                    prepend-inner-icon="mdi-card-account-details" class="rounded-lg"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="formClient.telefono" label="Teléfono" variant="outlined"
                                    prepend-inner-icon="mdi-phone" class="rounded-lg"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="formClient.nit" label="NIT" variant="outlined"
                                    prepend-inner-icon="mdi-identifier" class="rounded-lg"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="formClient.placa_vehiculo" label="Placa movilidad"
                                    variant="outlined"></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions class="pa-6">
                    <v-spacer></v-spacer>
                    <v-btn color="error" variant="outlined" @click="closeDialogClient" class="rounded-pill mr-4"
                        prepend-icon="mdi-close">
                        Cancelar
                    </v-btn>
                    <v-btn :loading="loadingClient" type="submit" :color="accion === 'editar' ? 'warning' : 'success'"
                        variant="elevated" class="rounded-pill"
                        :prepend-icon="accion === 'editar' ? 'mdi-pencil' : 'mdi-content-save'">
                        {{ accion === 'crear' ? 'Guardar' : 'Editar' }}
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
    <v-dialog v-model="dialogHora" max-width="400" scrollable>
        <v-card class="rounded-lg elevation-12">
            <v-img
                src="https://images.unsplash.com/photo-1506784983877-45594efa4cbe?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2068&q=80"
                height="200" class="align-end" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)">
                <v-card-title class="text-h4 font-weight-bold text-white pb-4">
                    Aumentar Horas
                </v-card-title>
            </v-img>

            <v-form @submit.prevent="saveHora" lazy-validation>
                <v-card-text class="pa-6">
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="formHora.hora" type="number" min="1" max="24"
                                label="Horas a aumentar" variant="outlined" prepend-inner-icon="mdi-clock-plus"
                                class="rounded-lg"
                                :rules="[v => !!v || 'Este campo es requerido', v => (v && v > 0 && v <= 24) || 'El valor debe estar entre 1 y 24']"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <v-slider v-model="formHora.hora" min="1" max="24" step="1" thumb-label="always"
                                class="mt-4">
                                <template v-slot:append>
                                    <v-text-field v-model="formHora.hora" type="number" style="width: 70px"
                                        density="compact" hide-details single-line></v-text-field>
                                </template>
                            </v-slider>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions class="pa-6">
                    <v-spacer></v-spacer>
                    <v-btn color="error" variant="outlined" @click="dialogHora = false" class="rounded-pill mr-4"
                        prepend-icon="mdi-close">
                        Cancelar
                    </v-btn>
                    <v-btn :loading="loadingHora" type="submit" color="success" variant="elevated" class="rounded-pill"
                        prepend-icon="mdi-content-save">
                        Guardar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>
<script setup>
import { ref, watch } from 'vue'
import useRecepcion from '@/services/useRecepcion';
import { onMounted } from 'vue';
import { reactive } from 'vue';
import { VTimePicker } from 'vuetify/labs/VTimePicker'
import { showAlert, showAlertConfirmation } from '@/utils/SweetAlert';
import useDialog from '@/services/useDialog';
import useCliente from '@/services/useCliente';
const { storeCliente } = useCliente();

import { computed } from 'vue';

const { getUsuariosActivos, usuariosActivos, getPisosActivos, pisosActivos } = useDialog();

const { getHabitacionesRecepcion, habitacionesRecepcion, registrarEstadia, checkSalida, terminarLimpieza, updateEstadia, agregar_horas } = useRecepcion();

const loadingHora = ref(false);
const dialogServicioActual = ref(false);
const dialogUsar = ref(false);
const contenidoServicio = ref(null);
const dialogHora = ref(false);
const formEstadia = reactive({
    'habitacion_id': '',
    'cliente_id': '',
    'fecha_inicio': '',
    'fecha_fin': '',
    'hora_inicio': '',
    'hora_fin': '',
    'adelanto': '',
    'observacion': '',
    'check_tipo': '',
    'reserva_id': '',
})
const formHora = reactive({
    'id': '',
    'hora': 1,
})
const allSearch = ref(false);
const dialog = ref(false);
const dialogTitle = ref('');
const loading = ref(false);
const erros = reactive({});
const accion = ref('');
const selectedDate = ref([]);
const form = reactive({
    id: '',
    habitacion_id: '',
    cliente_id: '',
    fecha_inicio: '',
    fecha_fin: '',
    hora_inicio: '',
    hora_fin: '',
    observacion: '',
    adelanto: '',
    check_tipo: 'momento'
});
const formClient = reactive({
    nombre: '',
    paterno: '',
    materno: '',
    cedula: '',
    telefono: '',
    nit: '',
    placa_vehiculo: '',
})
const dialogClient = ref(false);
const loadingClient = ref(false);
const errosClient = reactive({});

const openDialogClient = () => {
    dialogClient.value = true
}
const saveClient = async () => {
    try {
        await storeCliente(formClient);
        closeDialogClient();
        getUsuariosActivos();
    } catch (error) {
        if (error.response) {
            errosClient.nombre = error.response.data.data.nombre
            errosClient.paterno = error.response.data.data.paterno
            errosClient.materno = error.response.data.data.materno
            errosClient.cedula = error.response.data.data.cedula
        }
    } finally {
        loadingClient.value = false
    }
}
const closeDialogClient = () => {
    dialogClient.value = false
    formClient.nombre = ''
    formClient.paterno = ''
    formClient.materno = ''
    formClient.cedula = '',
    formClient.telefono = '',
    formClient.nit = ''
    formClient.placa_vehiculo = ''
    errosClient.nombre = ''
    errosClient.paterno = ''
    errosClient.materno = ''
    errosClient.cedula = ''
    errosClient.telefono = ''
    errosClient.nit = ''
}

const horaFin = ref(null);
const minutoFin = ref(null);

const horaInicio = ref(null);
const minutoInicio = ref(null);

// Permitir cualquier minuto
const allowedStep = m => true;

const actualizarHora = (nuevaHora) => {
    horaFin.value = nuevaHora;
    actualizarHoraFin();
};

const actualizarHoraIni = (nuevaHora) => {
    horaInicio.value = nuevaHora;
    actualizarHoraInicio();
}

const actualizarMinuto = (nuevoMinuto) => {
    minutoFin.value = nuevoMinuto;
    actualizarHoraFin();
};

const actualizarMinutoIni = (nuevoMinuto) => {
    minutoInicio.value = nuevoMinuto;
    actualizarHoraInicio();
}

const actualizarHoraInicio = () => {
    if (horaInicio.value !== null && minutoInicio.value !== null) {
        const horaFormateada = horaInicio.value.toString().padStart(2, '0');
        const minutoFormateado = minutoInicio.value.toString().padStart(2, '0');
        form.hora_inicio = `${horaFormateada}:${minutoFormateado}`;
    }
};

const actualizarHoraFin = () => {
    if (horaFin.value !== null && minutoFin.value !== null) {
        const horaFormateada = horaFin.value.toString().padStart(2, '0');
        const minutoFormateado = minutoFin.value.toString().padStart(2, '0');
        form.hora_fin = `${horaFormateada}:${minutoFormateado}`;
    }
};

const setHoraInicioActual = () => {
    const now = new Date();
    const horaFormateada = now.getHours().toString().padStart(2, '0');
    const minutoFormateado = now.getMinutes().toString().padStart(2, '0');
    form.hora_inicio = `${horaFormateada}:${minutoFormateado}`;
    horaInicio.value = parseInt(horaFormateada);
    minutoInicio.value = parseInt(minutoFormateado);
};

const openHora = (item) => {
    formHora.id = item.servicio_actual.id
    dialogHora.value = true
}

watch(() => form.hora_fin, (newValue) => {
    if (newValue) {
        const [h, m] = newValue.split(':');
        horaFin.value = parseInt(h);
        minutoFin.value = parseInt(m);
    }
}, { immediate: true });

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

const getFullName = (item) => {
    if (!item) return '';
    return `${item.nombre} ${item.paterno} ${item.materno}`;
}

const getFullHabitacion = (item) => {
    if (!item) return '';
    return `Nro.${item.numero} - ${item.nombre}`;
}

const confirmarEstadia = async (item) => {
    formEstadia.habitacion_id = item.id
    formEstadia.reserva_id = item.proxima_reserva.id
    formEstadia.cliente_id = item.proxima_reserva.cliente.id
    formEstadia.observacion = item.proxima_reserva.observacion
    formEstadia.adelanto = item.adelanto
    formEstadia.hora_inicio = item.proxima_reserva.hora_inicio
    formEstadia.hora_fin = item.proxima_reserva.hora_fin
    formEstadia.fecha_inicio = item.proxima_reserva.fecha_inicio
    formEstadia.fecha_fin = item.proxima_reserva.fecha_fin
    formEstadia.check_tipo = item.check_tipo

    const result = await showAlertConfirmation(
        'Confirmar reserva',
        '¿Estás seguro de que deseas confirmar esta reserva?',
        'question',
        'success'
    );
    if (result.isConfirmed) {
        try {
            await registrarEstadia(formEstadia)
        } catch (error) {
            console.log(error)
        }
        showAlert('Confirmado', 'El registro ha sido confirmado.', 'success');
    } else {
        showAlert('Cancelado', 'Se ha cancelado la confirmacion', 'error');
    }
}

const checkOut = async (item) => {
    const result = await showAlertConfirmation(
        'Confirmar salida',
        '¿Estás seguro de que deseas confirmar la salida?',
        'question',
        'success'
    );

    if (result.isConfirmed) {
        try {
            await checkSalida(item.servicio_actual.id)
        } catch (error) {
            console.log(error)
        }
        showAlert('Confirmado', 'La salida ha sido confirmada.', 'success');
    } else {
        showAlert('Cancelado', 'Se ha cancelado la salida', 'error');
    }
}

const saveHora = async () => {
    try {
        await agregar_horas(formHora.id, formHora)
        dialogHora.value = false
        formHora.hora = 1
        showAlert('Confirmado', 'Se ha agregado la hora.', 'success');
    } catch (error) {
        console.log(error)
    }
}

const verServicio = (item) => {
    dialogServicioActual.value = true
    contenidoServicio.value = item
}

const actionButton = async () => {
    if (accion.value === 'crear') {
        try {
            form.fecha_inicio = fechaInicial.value ? obtenerSoloFecha(new Date(fechaInicial.value).toISOString()) : null;
            form.fecha_fin = fechaFinal.value ? obtenerSoloFecha(new Date(fechaFinal.value).toISOString()) : null;
            await registrarEstadia(form);
            showAlert('¡Registro exitoso!', 'Se ha registrado con éxito', 'success');
            closeDialog();
        } catch (error) {
            if (error.response) {
                if (error.response.status === 400) {
                    showAlert('Error', error.response.data.message, 'error'); // Aquí se muestra el mensaje de error
                } else {
                    erros.cliente_id = error.response.data.data.cliente_id
                    erros.habitacion_id = error.response.data.data.habitacion_id
                    erros.fecha_inicio = error.response.data.data.fecha_inicio
                    erros.hora_fin = error.response.data.data.hora_fin
                    erros.hora_inicio = error.response.data.data.hora_inicio
                    erros.adelanto = error.response.data.data.adelanto
                }
            }
        }
    } else {
        try {
            await updateEstadia(form.id, form);
            closeDialog();
        } catch (error) {
            console.log('Error:', error);
        }
    }
}

const closeDialog = () => {
    dialogUsar.value = false;
    form.cliente_id = null
    form.habitacion_id = null
    form.observacion = null
    form.adelanto = null
    form.hora_inicio = null
    form.hora_fin = null
    form.fecha_inicio = null
    form.fecha_fin = null
}

function obtenerSoloFecha(fechaHora) {
    if (!fechaHora) return null;
    return fechaHora.split('T')[0];
}

const openDialog = (modo, item = null) => {
    if (modo === 'crear') {
        dialogTitle.value = 'Registrar estadia';
        dialogUsar.value = true;
        form.habitacion_id = item.id
        setHoraInicioActual(); // Set the current time as the start time
        accion.value = 'crear';
    } else if (modo === 'editar') {
        dialogTitle.value = 'Editar estadia';
        dialogUsar.value = true;
        accion.value = 'editar';

        form.id = item.servicio_actual.id
        form.habitacion_id = item.id
        form.cliente_id = item.servicio_actual.cliente.id
        form.observacion = item.servicio_actual.observacion
        form.adelanto = item.servicio_actual.adelanto
        form.hora_inicio = item.servicio_actual.hora_inicio
        form.hora_fin = item.servicio_actual.hora_fin
        form.fecha_inicio = item.servicio_actual.fecha_inicio
        form.fecha_fin = item.servicio_actual.fecha_fin
    }
}

const updateTrigger = ref(0);
const changePiso = async () => {
    allSearch.value = false
    await getHabitacionesRecepcion(searchForm);
}

const searchForm = reactive({
    search: 1,
})

onMounted(() => {
    getPisosActivos();
    getUsuariosActivos();
    getHabitacionesRecepcion(searchForm);
    setInterval(() => {
        updateTrigger.value++;
    }, 5000);
});

watch(() => updateTrigger.value, async () => {
    await getHabitacionesRecepcion(searchForm);
})

const updateLimpieza = async (item) => {
    try {
        const result = await showAlertConfirmation(
            'Terminar limpieza',
            '¿Estás seguro de que deseas terminar esta limpieza?',
            'question',
            'success'
        );
        if (result.isConfirmed) {
            try {
                await terminarLimpieza(item.id)
                await getHabitacionesRecepcion(searchForm)
            } catch (error) {
                console.log(error)
            }
            showAlert('Confirmado', 'El registro ha sido confirmado.', 'success');
        } else {
            showAlert('Cancelado', 'Se ha cancelado la confirmacion', 'error');
        }
    } catch (error) {
        console.log(error)
    }
}

const getColorAction = (item) => {
    switch (item.estado_actual) {
        case 'reservada':
            return 'red';
        case 'limpieza':
            return '#0892AB';
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

const getColor = (item) => {
    switch (item.estado_actual) {
        case 'reservada':
            return 'red';
        case 'limpieza':
            return '#31BFE9';
        case 'desocupado':
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
            return '#F43E2C';
        default:
            break;
    }
}

const getStatusColor = (item) => {
    switch (item.estado_actual.toLowerCase()) {
        case 'ocupado': return 'red';
        case 'disponible': return 'green';
        case 'mantenimiento': return 'orange';
        default: return 'grey';
    }
};

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
.hotel-background {
    background-image: url('ruta/a/tu/imagen/de/fondo.jpg');
    background-size: cover;
    background-attachment: fixed;
    min-height: 100vh;
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.room-card {
    border: 2px solid black;
    border-radius: 12px;
    overflow: hidden;
}

.v-card-item {
    padding-bottom: 0;
}

.v-card-text {
    padding-top: 8px;
    padding-bottom: 8px;
}

.v-card-actions {
    padding: 8px;
    transition: background-color 0.3s ease;
}

.disponible-btn {
    text-transform: none !important;
    letter-spacing: normal !important;
}

.disponible-btn :deep(.v-btn__content) {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

.disponible-btn :deep(.v-icon) {
    margin-left: auto;
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