<template>
    <v-container fluid>
        <v-card class="elevation-3 rounded-lg">
            <v-card-title class="text-h4 font-weight-bold pa-6 primary white--text">
                Tipos de Habitaciones registradas
                <v-spacer></v-spacer>
                <v-btn color="#5549C5" @click="openDialog('crear')" elevation="2" large rounded>
                    <v-icon left>mdi-plus</v-icon>
                    Agregar Tipo de habitación
                </v-btn>
            </v-card-title>

            <v-card-text>
                <v-row align="center" class="mb-4">
                    <v-col cols="12" sm="6" md="4">
                        <v-text-field v-model="search" prepend-inner-icon="mdi-magnify"
                            label="Buscar en todas las columnas" outlined dense clearable></v-text-field>
                    </v-col>
                </v-row>
                <v-data-table :headers="headers" :items="tipoHabitaciones" :search="search" :items-per-page="10"
                    class="elevation-2 rounded-lg custom-table" :no-data-text="'No hay datos disponibles'"
                    :no-results-text="'No se encontraron resultados'" :items-per-page-text="'Registros por página'"
                    :page-text="'{0}-{1} de {2}'" :footer-props="{
                        'items-per-page-options': [5, 10, 15, 20],
                        'items-per-page-text': 'Registros por página',
                        'show-current-page': true,
                        'show-first-last-page': true
                    }">
                    <template v-slot:header="{ props: { headers } }">
                        <thead>
                            <tr>
                                <th v-for="header in headers" :key="header.value" class="custom-header">
                                    {{ header.text }}
                                </th>
                            </tr>
                        </thead>
                    </template>
                    <template v-slot:item.numero_secuencial="{ index }">
                        <div class="font-weight-medium">{{ index + 1 }}</div>
                    </template>
                    <template v-slot:item.estado="{ item }">
                        <v-chip :color="item.estado === 1 ? 'success' : 'error'" small label text-color="white">
                            {{ item.estado === 1 ? 'Activo' : 'Inactivo' }}
                        </v-chip>
                    </template>
                    <template v-slot:item.precio_fijo="{ item }">
                        {{ formatCurrency(item.precio_fijo) }}
                    </template>
                    <template v-slot:item.precio_decremento="{ item }">
                        {{ formatCurrency(item.precio_decremento) }}
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn @click="desactivar(item.id)" v-if="item.estado === 1" icon small color="success"
                            class="mr-2">
                            <v-icon>mdi-checkbox-marked-circle</v-icon>
                        </v-btn>
                        <v-btn v-else @click="activar(item.id)" icon small color="error" class="mr-2">
                            <v-icon>mdi-close-circle</v-icon>
                        </v-btn>
                        <v-btn @click="openDialog('editar', item)" icon small color="info">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </v-container>
    <v-dialog v-model="dialog" max-width="600" transition="dialog-bottom-transition">
        <v-card class="rounded-lg">
            <v-toolbar dense class="gradient-header">
                <v-toolbar-title class="text-h6 font-weight-bold white--text">
                    {{ dialogTittle }}
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon @click="closeDialog" color="white">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-form @submit.prevent="actionButton" class="pa-6">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="form.nombre" :error-messages="errors.nombre"
                                label="Tipo de habitación" variant="outlined" density="comfortable"
                                prepend-inner-icon="mdi-bed" class="mb-2"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="form.descripcion" :error-messages="errors.descripcion"
                                label="Descripción" variant="outlined" density="comfortable"
                                prepend-inner-icon="mdi-text-box-outline" rows="3" auto-grow class="mb-2"></v-textarea>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field :error-messages="errors.precio_fijo" v-model="form.precio_fijo"
                                label="Precio fijo" variant="outlined" density="comfortable"
                                prepend-inner-icon="mdi-cash" type="number" prefix="$" class="mb-2"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field :error-messages="errors.precio_decremento" v-model="form.precio_decremento"
                                label="Precio decremento" variant="outlined" density="comfortable"
                                prepend-inner-icon="mdi-cash-minus" type="number" prefix="$"
                                class="mb-2"></v-text-field>
                        </v-col>
                    </v-row>
                </v-container>

                <v-card-actions class="pt-4">
                    <v-spacer></v-spacer>
                    <v-btn color="grey-darken-1" variant="text" @click="closeDialog" class="mr-2">
                        Cancelar
                    </v-btn>
                    <v-btn :loading="loading" :color="accion === 'editar' ? 'success' : 'primary'" type="submit">
                        {{ accion === 'crear' ? 'Guardar' : 'Editar' }}
                        <v-icon right>{{ accion === 'crear' ? 'mdi-content-save' : 'mdi-pencil' }}</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

</template>
<script setup>
import { ref, onMounted, reactive } from 'vue';

import useTipoHabitacion from '@/services/useTipoHabitacion';

import { showAlert, showAlertConfirmation } from '@/utils/SweetAlert';

const dialog = ref(false);
const dialogTittle = ref();
const loading = ref(false);
const search = ref();

const form = reactive({
    id: '',
    nombre: '',
    descripcion: '',
    precio_fijo: '',
    precio_decremento: '',
});

const accion = ref();
const errors = reactive({});

const headers = ref([
    { title: 'Nro', value: 'numero_secuencial' },
    { title: 'Nombre', value: 'nombre' },
    { title: 'Descripcion', value: 'descripcion' },
    { title: 'Precio fijo', value: 'precio_fijo' },
    { title: 'Precio decremento', value: 'precio_decremento' },
    { title: 'Estado', value: 'estado' },
    { title: 'Acciones', value: 'actions', sortable: false },
]);

const { getTipoHabitacion,
    tipoHabitaciones,
    storeTipoHabitacion,
    updateTipoHabitacion,
    desactiveTipoHabitacion,
    activeTipoHabitacion } = useTipoHabitacion();
const openDialog = (modo, element = null) => {
    if (modo === 'crear') {
        dialogTittle.value = 'Registrar Tipo de Habitacion';
        dialog.value = true;
        accion.value = 'crear';
    } else if (modo === 'editar') {
        dialogTittle.value = 'Editar Tipo de Habitacion';
        dialog.value = true;
        accion.value = 'editar';
        if (element) {
            form.id = element.id
            form.nombre = element.nombre
            form.descripcion = element.descripcion
            form.precio_fijo = element.precio_fijo
            form.precio_decremento = element.precio_decremento
        }
    }
}


const actionButton = async () => {
    try {
        loading.value = true;
        if (accion.value === 'crear') {
            await storeTipoHabitacion(form);
            showAlert('¡Registro exitoso!', 'Se ha registrado con éxito', 'success');
        } else {
            await updateTipoHabitacion(form.id, form);
            showAlert('¡Actualización exitosa!', 'Se ha actualizado con éxito', 'success');
        }
        closeDialog();
        getTipoHabitacion();
    } catch (error) {
        loading.value = false;
        if (error.response) {
            errors.nombre = error.response.data.data.nombre;
            errors.descripcion = error.response.data.data.descripcion;
            errors.precio_fijo = error.response.data.data.precio_fijo;
            errors.precio_decremento = error.response.data.data.precio_decremento;
        }
    } finally {
        loading.value = false;
    }
}

const desactivar = async (id) => {
    const result = await showAlertConfirmation(
        'Confirmar desactivación',
        '¿Estás seguro de que deseas desactivar este registro?',
        'question',
        'success'
    );
    if (result.isConfirmed) {
        await desactiveTipoHabitacion(id);
        await getTipoHabitacion();
        showAlert('Desactivado', 'El registro ha sido desactivado.', 'success');
    } else {
        showAlert('Cancelado', 'La desactivación ha sido cancelada.', 'error');
    }
}
const activar = async (id) => {
    const result = await showAlertConfirmation(
        'Confirmar activación',
        '¿Estás seguro de que deseas activar este registro?',
        'question',
        'success'
    );
    if (result.isConfirmed) {
        await activeTipoHabitacion(id)
        await getTipoHabitacion();
        showAlert('Activado', 'El registro ha sido activado.', 'success');
    } else {
        showAlert('Cancelado', 'La activación ha sido cancelada.', 'error');
    }
}
const closeDialog = () => {
    dialog.value = false;
    limpiarDialog();
}
const limpiarDialog = () => {
    form.id = '';
    form.nombre = '';
    form.descripcion = '';
    form.precio_fijo = '';
    form.precio_decremento = '';
    errors.nombre = '';
    errors.descripcion = '';
    errors.precio_fijo = '';
    errors.precio_decremento = '';
    loading.value = false;
}
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(value);
};
onMounted(() => {
    getTipoHabitacion();
})
</script>
<style scoped>
.gradient-header {
    background: linear-gradient(45deg, #8478D3, #5549C5);
}

.v-btn {
    text-transform: none;
    font-weight: 500;
}

.v-text-field,
.v-select,
.v-textarea {
    margin-bottom: 12px;
}

.v-card {
    background-color: rgba(255, 255, 255, 0.799);
    overflow: hidden;
    border: 2px solid black;
}

.custom-table {
    border: 2px solid #000000;
}

.custom-table>>>.v-data-table__wrapper {
    overflow-x: auto;
}

.custom-table>>>table {
    border-collapse: separate;
    border-spacing: 0;

}

.custom-table>>>th {
    background-color: #494949 !important;
    color: white !important;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.custom-table>>>td {
    border-bottom: 1px solid #e0e0e0;
    padding: 12px 16px;
    border: 1px solid #B4BBE8;
    border-bottom: 1px solid black; /* Borde inferior de las celdas */
    border-top: 1px solid black; /* Borde superior específico para cada celda */
    border-right: 1px solid black; /* Borde derecho para cada celda */
}

.custom-table>>>tr:nth-child(even) {
    background-color: #fff9f9;
}

.custom-table>>>tr:hover {
    background-color: #E3F2FD;
}

.gradient-header {
    background: linear-gradient(45deg, #1e88e5, #1565c0);
}

.v-btn {
    text-transform: none;
    font-weight: 500;
}

.v-text-field,
.v-select,
.v-textarea {
    margin-bottom: 12px;
}

.v-card {
    overflow: hidden;
}
</style>