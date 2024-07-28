<template>
    <v-container fluid>
        <v-card class="elevation-3 rounded-lg">
            <v-card-title class="text-h4 font-weight-bold pa-6 primary white--text">
                Clientes registrados
                <v-spacer></v-spacer>
                <v-btn color="#5549C5" @click="openDialog('crear')" elevation="2" large rounded>
                    <v-icon left>mdi-plus</v-icon>
                    Agregar Cliente
                </v-btn>
            </v-card-title>

            <v-card-text>
                <v-row align="center" class="mb-4">
                    <v-col cols="12" sm="6" md="4">
                        <v-text-field v-model="search" prepend-inner-icon="mdi-magnify"
                            label="Buscar en todas las columnas" outlined dense clearable></v-text-field>
                    </v-col>
                </v-row>
                <v-data-table :headers="headers" :items="clientes" :search="search" :items-per-page="10"
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
            <v-form @submit.prevent="actionButton" lazy-validation>
                <v-card-text class="pa-6">
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.nombre" label="Nombres" :error-messages="erros.nombre"
                                    variant="outlined" prepend-inner-icon="mdi-account"
                                    class="rounded-lg"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.paterno" label="Apellido Paterno"
                                    :error-messages="erros.paterno" variant="outlined"
                                    prepend-inner-icon="mdi-account-tie" class="rounded-lg"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.materno" label="Apellido Materno"
                                    :error-messages="erros.materno" variant="outlined"
                                    prepend-inner-icon="mdi-account-tie-woman" class="rounded-lg"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.cedula" label="Cédula de Identidad"
                                    :error-messages="erros.cedula" variant="outlined"
                                    prepend-inner-icon="mdi-card-account-details" class="rounded-lg"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.telefono" label="Teléfono" variant="outlined"
                                    prepend-inner-icon="mdi-phone" class="rounded-lg"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.nit" label="NIT" variant="outlined"
                                    prepend-inner-icon="mdi-identifier" class="rounded-lg"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.placa_vehiculo" label="Placa movilidad"
                                    variant="outlined"></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions class="pa-6">
                    <v-spacer></v-spacer>
                    <v-btn color="error" variant="outlined" @click="closeDialog" class="rounded-pill mr-4"
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
import { ref, onMounted, reactive } from 'vue';
import useCliente from '@/services/useCliente';
import { showAlert, showAlertConfirmation } from '@/utils/SweetAlert';

const dialog = ref(false);
const dialogTittle = ref();
const loading = ref(false);
const search = ref();

const form = reactive({
    id: '',
    nombre: '',
    paterno: '',
    materno: '',
    cedula: '',
    telefono: '',
    nit: '',
    placa_vehiculo: '',
});

const accion = ref();
const erros = reactive({});

const headers = ref([
    { title: 'Nro', value: 'numero_secuencial' },
    { title: 'Nombre', value: 'nombre' },
    { title: 'Paterno', value: 'paterno' },
    { title: 'Materno', value: 'materno' },
    { title: 'Cedula', value: 'cedula' },
    { title: 'Telefono', value: 'telefono' },
    { title: 'NIT', value: 'nit' },
    { title: 'Movilidad', value: 'relacion_movilidad.placa_vehiculo' },
    { title: 'Estado', value: 'estado' },
    { title: 'Acciones', value: 'actions', sortable: false },
]);

const { getClientes,
    clientes,
    storeCliente,
    updateCliente,
    desactiveCliente,
    activeCliente } = useCliente();
const openDialog = (modo, element = null) => {
    if (modo === 'crear') {
        dialogTittle.value = 'Registrar sucursal';
        dialog.value = true;
        accion.value = 'crear';
    } else if (modo === 'editar') {
        dialogTittle.value = 'Editar sucursal';
        dialog.value = true;
        accion.value = 'editar';
        if (element) {
            form.id = element.id
            form.nombre = element.nombre
            form.paterno = element.paterno
            form.materno = element.materno
            form.cedula = element.cedula
            form.telefono = element.telefono
            form.nit = element.nit
            form.placa_vehiculo = element.placa_vehiculo
        }
    }
}

const actionButton = async () => {
    try {
        loading.value = true;
        if (accion.value === 'crear') {
            await storeCliente(form);
            showAlert('¡Registro exitoso!', 'Se ha registrado con éxito', 'success');
        } else {
            await updateCliente(form.id, form);
            showAlert('¡Actualización exitosa!', 'Se ha actualizado con éxito', 'success');
        }
        closeDialog();
        getClientes();
    } catch (error) {
        loading.value = false;
        if (error.response) {
            erros.nombre = error.response.data.data.nombre_sucursal;
            erros.direccion = error.response.data.data.direccion;
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
        await desactiveCliente(id);
        await getClientes();
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
        await activeCliente(id)
        await getClientes();
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
    form.direccion = '';
    form.nombre_sucursal = '';
    erros.direccion = '';
    erros.nombre = '';
    loading.value = false;
}

onMounted(() => {
    getClientes();
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
    border: 2px solid #000000;
}

.custom-table {
    border: 2px solid #020202;
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