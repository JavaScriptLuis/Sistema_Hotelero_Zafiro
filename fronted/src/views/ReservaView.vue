<template>
    <div class="calendar-container">
        <FullCalendar :options="calendarOptions" />
    </div>
    <v-dialog v-model="dialog" max-width="1000" scrollable>
        <v-card class="rounded-lg elevation-10">
            <v-img
                src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                height="200" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)" cover class="align-end">
                <v-card-title class="text-h4 font-weight-bold text-white">
                    {{ dialogTitle }}
                </v-card-title>
            </v-img>
            <v-form @submit.prevent="actionButton" lazy-validation>
                <v-card-text class="pa-6">
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-autocomplete :error-messages="erros.cliente_id" v-model="form.cliente_id"
                                    label="Cliente" :items="usuariosActivos" :item-title="getFullName" item-value="id"
                                    variant="outlined" prepend-inner-icon="mdi-account"
                                    class="rounded-lg"></v-autocomplete>

                            </v-col>
                            <v-col cols="12" sm="6" class="d-flex align-center">
                                <v-btn color="primary" @click="openDialogClient" prepend-icon="mdi-plus"
                                    class="rounded-pill">
                                    Agregar Cliente
                                </v-btn>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-autocomplete :error-messages="erros.habitacion_id" v-model="form.habitacion_id"
                                    label="Habitación" :items="habitacionesActivas" item-value="id"
                                    :item-title="getFullHabitacion" variant="outlined" prepend-inner-icon="mdi-bed"
                                    class="rounded-lg"></v-autocomplete>

                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field :error-messages="erros.adelanto" v-model="form.adelanto" label="Adelanto"
                                    variant="outlined" prefix="Bs." prepend-inner-icon="mdi-cash"
                                    class="rounded-lg"></v-text-field>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="12">
                                <v-textarea v-model="form.observacion" label="Observaciones" rows="3" variant="outlined"
                                    auto-grow prepend-inner-icon="mdi-comment-text" class="rounded-lg"></v-textarea>
                            </v-col>
                        </v-row>

                        <v-row justify="center">
                            <v-col cols="12">

                                <v-alert v-if="erros.fecha_inicio" type="error" density="compact" variant="tonal">
                                    {{ erros.fecha_inicio }}
                                </v-alert>
                            </v-col>
                        </v-row>

                        <v-row justify="center" class="mt-4">
                            <v-col cols="12" sm="8">
                                <v-card class="elevation-2 rounded-lg">
                                    <v-card-text>
                                        <v-locale-provider locale="es">
                                            <v-date-picker v-model="selectedDate" multiple title="Seleccionar fecha"
                                                locale="es" color="primary" class="mx-auto"></v-date-picker>
                                        </v-locale-provider>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>

                        <v-row justify="space-around" class="mt-6">
                            <v-col cols="12" sm="6">
                                <v-alert v-if="erros.hora_inicio" type="error" density="compact" variant="tonal"
                                    class="mt-2">
                                    {{ erros.hora_inicio }}
                                </v-alert>
                                <v-card class="elevation-2 rounded-lg">
                                    <v-card-title class="text-h6 font-weight-regular">Hora de inicio</v-card-title>
                                    <v-card-text>
                                        <v-time-picker v-model="form.hora_inicio" :hour="horaInicio"
                                            :minute="minutoInicio" @update:hour="actualizarHoraIni"
                                            @update:minute="actualizarMinutoIni" :allowed-minutes="allowedStep"
                                            format="24hr" class="mx-auto"></v-time-picker>
                                    </v-card-text>
                                </v-card>

                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-alert v-if="erros.hora_fin" type="error" density="compact" variant="tonal"
                                    class="mt-2">
                                    {{ erros.hora_fin }}
                                </v-alert>
                                <v-card class="elevation-2 rounded-lg">
                                    <v-card-title class="text-h6 font-weight-regular">Hora de fin</v-card-title>
                                    <v-card-text>
                                        <v-time-picker v-model="form.hora_fin" :hour="horaFin" :minute="minutoFin"
                                            @update:hour="actualizarHora" @update:minute="actualizarMinuto"
                                            :allowed-minutes="allowedStep" format="24hr"
                                            class="mx-auto"></v-time-picker>
                                    </v-card-text>
                                </v-card>

                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions class="pa-4">
                    <v-spacer></v-spacer>
                    <v-btn color="error" variant="outlined" @click="closeDialog" class="rounded-pill"
                        prepend-icon="mdi-close">
                        Cancelar
                    </v-btn>
                    <v-btn :loading="loading" type="submit" :color="accion === 'editar' ? 'warning' : 'success'"
                        variant="elevated" class="rounded-pill"
                        :prepend-icon="accion === 'editar' ? 'mdi-pencil' : 'mdi-content-save'">
                        {{ accion === 'crear' ? 'Guardar' : 'Editar' }}
                    </v-btn>
                    <v-btn color="error" variant="tonal" @click="deleteItem" class="rounded-pill"
                        prepend-icon="mdi-delete">
                        Eliminar
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

</template>

<script setup>
import { ref, watch, computed, reactive, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import esLocale from '@fullcalendar/core/locales/es'
import { VTimePicker } from 'vuetify/labs/VTimePicker'
import { showAlert, showAlertConfirmation } from '@/utils/SweetAlert';

import useDialog from '@/services/useDialog';
import useReserva from '@/services/useReserva';
import useCliente from '@/services/useCliente';
const { getUsuariosActivos, usuariosActivos, getHabitacionesActivas, habitacionesActivas } = useDialog();
const { getReservas, eventosCalendario, storeReserva, updateReserva, deleteReserva } = useReserva();
const { storeCliente } = useCliente();
const dialog = ref(false);
const dialogTitle = ref('');
const loading = ref(false);
const erros = reactive({});
const accion = ref('');
const selectedDate = ref([]);
const form = reactive({
    habitacion_id: '',
    cliente_id: '',
    fecha_inicio: '',
    fecha_fin: '',
    hora_inicio: '',
    hora_fin: '',
    observacion: '',
    adelanto: ''
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

const horaFin = ref(null);
const minutoFin = ref(null);
const horaInicio = ref(null);
const minutoInicio = ref(null);

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
const actualizarHoraFin = () => {
    if (horaFin.value !== null && minutoFin.value !== null) {
        const horaFormateada = horaFin.value.toString().padStart(2, '0');
        const minutoFormateado = minutoFin.value.toString().padStart(2, '0');
        form.hora_fin = `${horaFormateada}:${minutoFormateado}`;
    }
};
const actualizarHoraInicio = () => {
    if (horaInicio.value !== null && minutoInicio.value !== null) {
        const horaFormateada = horaInicio.value.toString().padStart(2, '0');
        const minutoFormateado = minutoInicio.value.toString().padStart(2, '0');
        form.hora_inicio = `${horaFormateada}:${minutoFormateado}`;
    }
};

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
});

const getFullName = (item) => {
    if (!item) return '';
    return `${item.nombre} ${item.paterno} ${item.materno}`;
}
const getFullHabitacion = (item) => {
    if (!item) return '';
    return `Nro.${item.numero} - ${item.nombre}`;
}
const editForm = (element1, element2, time1, time2) => {
    form.hora_inicio = time1;
    form.hora_fin = time2;

    const adjustedEnd = new Date(element2);
    if (element1.toDateString() !== element2.toDateString()) {
        adjustedEnd.setDate(adjustedEnd.getDate() - 1);
    }

    selectedDate.value = [new Date(element1), adjustedEnd];
};

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
const deleteItem = async () => {
    dialog.value = false;
    const result = await showAlertConfirmation(
        'Confirmar desactivación',
        '¿Estás seguro de que deseas eliminar este registro?',
        'question',
        'success'
    );

    if (result.isConfirmed) {
        await deleteReserva(form.id);
        getReservas();
        showAlert('Eliminado', 'El registro ha sido eliminado.', 'success');
    } else {
        showAlert('Cancelado', 'La eliminación ha sido cancelada.', 'error');
    }
}
onMounted(() => {
    getUsuariosActivos();
    getHabitacionesActivas();
    getReservas();
})

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
const openDialog = (modo, item = null) => {

    if (modo === 'crear') {
        dialogTitle.value = 'Registrar reserva';
        dialog.value = true;
        accion.value = 'crear';
        setHoraInicioActual(); // Establecer la hora de inicio actual automáticamente
    } else if (modo === 'editar') {
        dialogTitle.value = 'Editar reserva';
        dialog.value = true;
        accion.value = 'editar';
    }
}

const setHoraInicioActual = () => {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    form.hora_inicio = `${hours}:${minutes}`;
    horaInicio.value = parseInt(hours);
    minutoInicio.value = parseInt(minutes);
}

const actionButton = async () => {
    if (accion.value === 'crear') {
        try {
            form.fecha_inicio = fechaInicial.value ? obtenerSoloFecha(new Date(fechaInicial.value).toISOString()) : null;
            form.fecha_fin = fechaFinal.value ? obtenerSoloFecha(new Date(fechaFinal.value).toISOString()) : null;
            await storeReserva(form);
            showAlert('¡Registro exitoso!', 'Se ha registrado con éxito', 'success');

            getReservas();
            closeDialog();
        } catch (error) {

            if (error.response) {
                erros.cliente_id = error.response.data.data.cliente_id
                erros.habitacion_id = error.response.data.data.habitacion_id
                erros.fecha_inicio = error.response.data.data.fecha_inicio
                erros.hora_inicio = error.response.data.data.hora_inicio
                erros.hora_fin = error.response.data.data.hora_fin
                erros.adelanto = error.response.data.data.adelanto
            }
        }

    } else {
        try {
            form.fecha_inicio = fechaInicial.value ? obtenerSoloFecha(new Date(fechaInicial.value).toISOString()) : null;
            form.fecha_fin = fechaFinal.value ? obtenerSoloFecha(new Date(fechaFinal.value).toISOString()) : null;
            await updateReserva(form.id, form);
            getReservas();
            closeDialog();
        } catch (error) {
            if (error.response) {
                erros.cliente_id = error.response.data.data.cliente_id
                erros.habitacion_id = error.response.data.data.habitacion_id
                erros.fecha_inicio = error.response.data.data.fecha_inicio
                erros.hora_inicio = error.response.data.data.hora_inicio
                erros.hora_fin = error.response.data.data.hora_fin
                erros.adelanto = error.response.data.data.adelanto
            }
        }

    }
}

const closeDialog = () => {
    dialog.value = false;
    limpiarDialog();
}
const closeDialogClient = () => {
    dialogClient.value = false
    formClient.nombre = ''
    formClient.paterno = ''
    formClient.materno = ''
    formClient.cedula = '',
        formClient.telefono = '',
        formClient.nit = '',
        formClient.placa_vehiculo = ''
    errosClient.nombre = ''
    errosClient.paterno = ''
    errosClient.materno = ''
    errosClient.cedula = ''
    errosClient.telefono = ''
    errosClient.nit = ''
}
const limpiarDialog = () => {
    form.id = '';
    form.cliente_id = '';
    form.habitacion_id = '';
    form.hora_inicio = '';
    form.hora_fin = '';
    form.adelanto = '';
    form.observacion = '';
    form.fecha_inicio = '';
    form.fecha_fin = '';
    selectedDate.value = [];
    erros.cliente_id = '';
    erros.habitacion_id = '';
    erros.hora_inicio = '';
    erros.hora_fin = '';
    erros.adelanto = '';
    erros.fecha_inicio = '';
    erros.fecha_fin = '';
    loading.value = false;
}

const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    locale: esLocale,
    headerToolbar: {
        left: 'prev,next today registrarReserva',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    customButtons: {
        registrarReserva: {
            text: 'Registrar Reserva',
            click: () => openDialog('crear')
        }
    },
    slotMinTime: '00:00:00',
    slotMaxTime: '23:00:00',
    events: eventosCalendario,
    slotEventOverlap: false,
    allDaySlot: false,
    slotLabelFormat: {
        hour: 'numeric',
        minute: '2-digit',
        omitZeroMinute: false,
        meridiem: 'short'
    },
    editable: true,
    selectable: true,
    eventDisplay: 'block',
    displayEventTime: true,
    displayEventEnd: true,
    eventOverlap: false,
    eventColor: '#378006',
    eventTimeFormat: {
        hour: 'numeric',
        minute: '2-digit',
        meridiem: 'short'
    },
    eventContent: function (arg) {
        let timeText = '';
        if (!arg.event.allDay) {
            const start = arg.event.start;
            const end = arg.event.end;

            if (start) {
                const startTime = start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const endTime = end ? end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                timeText = `${startTime} - ${endTime}`;
            }
        }
        return {
            html: `
                <div class="fc-content">
                    <div class="fc-title">${arg.event.title}</div>
                    ${timeText ? `<div class="fc-time">${timeText}</div>` : ''}
                </div>
            `
        };
    },
    eventClick: function (info) {
        const start = info.event.start;
        const end = info.event.end;
        form.cliente_id = info.event.extendedProps.cliente_id;
        form.habitacion_id = info.event.extendedProps.habitacion_id;
        form.adelanto = info.event.extendedProps.adelanto;
        form.observacion = info.event.extendedProps.observacion;
        form.id = info.event.extendedProps.reserva_id;
        const formatDateTime = (date) => {
            if (!date) return { day: 'No especificado', time: 'No especificado' };

            const day = date.toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            const time = date.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });

            return { day, time };
        };

        const startFormatted = formatDateTime(start);
        let endFormatted = formatDateTime(end);

        if (!end) {
            const calculatedEnd = new Date(start);
            calculatedEnd.setHours(start.getHours() + 1);
            endFormatted = formatDateTime(calculatedEnd);
        }

        editForm(info.event.start, info.event.end, startFormatted.time, endFormatted.time);

        openDialog('editar', info.event);
    },
    select: function (info) {
        alert('Seleccionado desde: ' + info.startStr + ' hasta: ' + info.endStr);
    }
});
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
.calendar-container {
    padding: 20px;
    background-color: #f5f5f5;
    border-radius: 10px;
    border: 2px solid black;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.fc .fc-toolbar {
    background-color: #3f51b5;
    color: white;
    padding: 15px;
    border-radius: 5px 5px 0 0;
}

.fc .fc-toolbar-title {
    font-size: 1.5em;
    font-weight: bold;
}

.fc .fc-button {
    background-color: #4CAF50;
    border-color: #4CAF50;
    color: white;
    transition: all 0.3s ease;
}

.fc .fc-button:hover {
    background-color: #45a049;
    border-color: #45a049;
}

.fc-event {
    border-radius: 4px;
    border: none;
    padding: 2px 5px;
    font-size: 0.85em;
    transition: all 0.3s ease;
}

.fc-event:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.fc-event .fc-event-main {
    padding: 2px 4px;
}

.fc td,
.fc th {
    border: 1px solid #ddd;
}

.fc .fc-day-today {
    background-color: rgba(79, 195, 247, 0.1) !important;
}

.fc .fc-daygrid-day-number {
    font-weight: bold;
    color: #333;
}

.fc .fc-timegrid-slot-label {
    font-weight: bold;
    color: #666;
}

.fc .fc-view-harness {
    transition: height 0.3s ease;
}

.v-card {
    overflow: hidden;
}

.v-text-field {
    transition: all 0.3s ease;
}

.v-text-field:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.v-btn {
    text-transform: none;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.v-card-title {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}
</style>