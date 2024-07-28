<template>
    <v-container fluid>
        <v-card class="elevation-3 rounded-lg">
            <v-card-title class="text-h4 font-weight-bold pa-6 primary white--text">
                Habitaciones Registradas
                <v-spacer></v-spacer>
                <v-btn color="#5549C5" @click="openDialog('crear')" elevation="2" large rounded>
                    <v-icon left>mdi-plus</v-icon>
                    Agregar Habitación
                </v-btn>
            </v-card-title>

            <v-card-text>
                <v-row align="center" class="mb-4">
                    <v-col cols="12" sm="6" md="4">
                        <v-text-field v-model="search" prepend-inner-icon="mdi-magnify"
                            label="Buscar en todas las columnas" outlined dense clearable></v-text-field>
                    </v-col>
                </v-row>

                <v-data-table :headers="headers" :items="habitaciones" :search="search" :items-per-page="10"
                    class="elevation-2 rounded-lg custom-table" :no-data-text="'No hay datos disponibles'"
                    :no-results-text="'No se encontraron resultados'" :items-per-page-text="'Habitaciones por página'"
                    :page-text="'{0}-{1} de {2}'" :footer-props="{
                        'items-per-page-options': [5, 10, 15, 20],
                        'items-per-page-text': 'Habitaciones por página',
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
    <v-dialog v-model="dialog" max-width="800" transition="dialog-bottom-transition">
        <v-card class="rounded-lg elevation-8">
            <v-toolbar dense class="gradient-header white--text">
                <v-toolbar-title class="text-h6 font-weight-bold">{{ dialogTittle }}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn @click="closeDialog" icon class="ml-auto" color="white">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-form @submit.prevent="actionButton" lazy-validation class="pa-4">
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="4">
                                <v-select :error-messages="errors.piso_id" v-model="form.piso_id" :items="pisosActivos"
                                    item-title="nombre" item-value="id" density="comfortable" label="Piso"
                                    variant="outlined" class="rounded-lg"
                                    prepend-inner-icon="mdi-floor-plan"></v-select>
                            </v-col>
                            <v-col cols="12" sm="4">
                                <v-select :error-messages="errors.tipo_id" v-model="form.tipo_id"
                                    :items="tipoHabitacions" item-title="nombre" item-value="id" density="comfortable"
                                    label="Tipo de Habitación" variant="outlined" class="rounded-lg"
                                    prepend-inner-icon="mdi-bed"></v-select>
                            </v-col>
                            <v-col cols="12" sm="4">
                                <v-select :error-messages="errors.sucursal_id" v-model="form.sucursal_id"
                                    :items="sucursalesActivas" item-title="nombre_sucursal" item-value="id"
                                    density="comfortable" label="Sucursal" variant="outlined" class="rounded-lg"
                                    prepend-inner-icon="mdi-domain"></v-select>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field :error-messages="errors.numero" v-model="form.numero"
                                    label="Número de habitación" variant="outlined" class="rounded-lg"
                                    prepend-inner-icon="mdi-numeric"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12">
                                <v-textarea :error-messages="errors.descripcion" v-model="form.descripcion"
                                    label="Descripción" rows="3" variant="outlined" auto-grow
                                    prepend-inner-icon="mdi-comment-text" class="rounded-lg"></v-textarea>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions class="justify-center pa-4">
                    <v-btn @click="closeDialog" color="grey" variant="outlined" class="rounded-pill mr-4">
                        Cancelar
                    </v-btn>
                    <v-btn :loading="loading" type="submit" :color="accion === 'editar' ? 'success' : 'primary'"
                        variant="elevated" class="rounded-pill">
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

import useHabitacion from '@/services/useHabitacion';
import useDialog from '@/services/useDialog';
import { showAlert, showAlertConfirmation } from '@/utils/SweetAlert';

const dialog = ref(false);
const dialogTittle = ref();
const loading = ref(false);
const search = ref();
const headers = ref([
    { title: 'Nro', value: 'numero_secuencial' },
    { title: 'Sucursal', value: 'relacion_sucursal.nombre_sucursal' },
    { title: 'Piso', value: 'relacion_piso.nombre' },
    { title: 'Número', value: 'numero' },
    { title: 'Tipo de Habitación', value: 'relacion_tipo.nombre' }, // Nuevo encabezado
    { title: 'Descripción', value: 'descripcion' },
    { title: 'Estado', value: 'estado' },
    { title: 'Acciones', value: 'actions', sortable: false },
]);

const form = reactive({
    id: '',
    piso_id: '',
    sucursal_id: '',
    tipo_id: '',
    numero: '',
    descripcion: '',
});

const accion = ref();
const errors = reactive({});

const tipoHabitacions = [
    {
        id: 1,
        nombre: 'Simple'
    },
    {
        id: 2,
        nombre: 'Doble'
    },
    {
        id: 3,
        nombre: 'Vip'
    },
]

const { habitaciones, getHabitaciones, storeHabitacion, updateHabitacion, desactiveHabitacion, activeHabitacion } = useHabitacion();
const { getPisosActivos, pisosActivos, getSucursalesActivas, sucursalesActivas } = useDialog();
const openDialog = (modo, element = null) => {
    if (modo === 'crear') {
        dialogTittle.value = 'Registrar habitación';
        dialog.value = true;
        accion.value = 'crear';
    } else if (modo === 'editar') {
        dialogTittle.value = 'Editar habitación';
        dialog.value = true;
        accion.value = 'editar';
        if (element) {
            form.id = element.id
            form.piso_id = element.piso_id
            form.numero = element.numero
            form.descripcion = element.descripcion
            form.sucursal_id = element.sucursal_id
            form.tipo_id = element.tipo_id
        }
    }
}
const searchData = async () => {
    await getHabitaciones(search.value)
}

const actionButton = async () => {
    try {
        loading.value = true;
        if (accion.value === 'crear') {
            await storeHabitacion(form);
            showAlert('¡Registro exitoso!', 'Se ha registrado con éxito', 'success');
        } else {
            await updateHabitacion(form.id, form);
            showAlert('¡Actualización exitosa!', 'Se ha actualizado con éxito', 'success');
        }
        closeDialog();
        getHabitaciones();
    } catch (error) {
        loading.value = false;
        if (error.response) {
            errors.piso_id = error.response.data.data.piso_id;
            errors.sucursal_id = error.response.data.data.sucursal_id;
            errors.numero = error.response.data.data.numero;
            errors.descripcion = error.response.data.data.descripcion;
            errors.tipo_id = error.response.data.data.tipo_id;
        }
    } finally {
        loading.value = false;
    }
}

const onPageChange = () => {
    getHabitaciones();
}
const desactivar = async (id) => {
    const result = await showAlertConfirmation(
        'Confirmar desactivación',
        '¿Estás seguro de que deseas desactivar este registro?',
        'question',
        'success'
    );
    if (result.isConfirmed) {
        await desactiveHabitacion(id);
        await getHabitaciones();
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
        await activeHabitacion(id)
        await getHabitaciones();
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
    form.piso_id = '';
    form.sucursal_id = '';
    form.numero = '';
    form.descripcion = '';
    form.tipo_id = '';
    errors.piso_id = '';
    errors.sucursal_id = '';
    errors.numero = '';
    errors.descripcion = '';
    errors.tipo_id = '';
    loading.value = false;
}

onMounted(() => {
    searchData();
    getPisosActivos();
    getSucursalesActivas();
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
    background-color: #8478D3 !important;
    color: white !important;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.custom-table>>>td {
    border-bottom: 1px solid #e0e0e0;
    padding: 12px 16px;
    border: 1px solid #B4BBE8;
}

.custom-table>>>tr:nth-child(even) {
    background-color: #fff9f9;
}

.custom-table>>>tr:hover {
    background-color: #E3F2FD;
}

.v-chip {
    font-weight: bold;
}

.v-btn {
    margin-right: 8px;
}
</style>