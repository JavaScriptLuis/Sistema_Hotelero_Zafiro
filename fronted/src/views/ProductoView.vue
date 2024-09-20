<template>

    <v-container fluid>
        <v-card class="elevation-3 rounded-lg">
            <v-card-title class="text-h4 font-weight-bold pa-6 primary white--text">
                Productos registrados
                <v-spacer></v-spacer>
                <v-btn color="#5549C5" @click="openDialog('crear')" elevation="2" large rounded>
                    <v-icon left>mdi-plus</v-icon>
                    Agregar Producto
                </v-btn>
            </v-card-title>

            <v-card-text>
                <v-row align="center" class="mb-4">
                    <v-col cols="12" sm="6" md="4">
                        <v-text-field v-model="search" prepend-inner-icon="mdi-magnify"
                            label="Buscar en todas las columnas" outlined dense clearable></v-text-field>
                    </v-col>
                </v-row>
                <v-data-table :headers="headers" :items="productos" :search="search" :items-per-page="10"
                    class="elevation-2 rounded-lg custom-table" :no-data-text="'No hay datos disponibles'"
                    :no-results-text="'No se encontraron resultados'" :items-per-page-text="'Producto por página'"
                    :page-text="'{0}-{1} de {2}'" :footer-props="{
                        'items-per-page-options': [5, 10, 15, 20],
                        'items-per-page-text': 'Productos por página',
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
                    <template v-slot:item.precio_venta="{ item }">
                        {{ formatCurrency(item.precio_venta) }}
                    </template>
                    <template v-slot:item.precio_compra="{ item }">
                        {{ formatCurrency(item.precio_compra) }}
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

            <v-form @submit.prevent="actionButton" lazy-validation class="pa-6">
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12">
                                <v-select :error-messages="errors.sucursal_id" v-model="form.sucursal_id"
                                    :items="sucursalesActivas" item-title="nombre_sucursal" item-value="id"
                                    label="Sucursal" variant="outlined" class="rounded-lg"
                                    prepend-inner-icon="mdi-domain" hide-details="auto"></v-select>
                            </v-col>
                        </v-row>

                        <v-row class="mt-4">
                            <v-col cols="12" md="6">
                                <v-text-field :error-messages="errors.nombre" v-model="form.nombre"
                                    label="Nombre producto" variant="outlined" class="rounded-lg"
                                    prepend-inner-icon="mdi-package-variant" hide-details="auto"></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-textarea :error-messages="errors.descripcion" v-model="form.descripcion"
                                    label="Descripción" variant="outlined" class="rounded-lg"
                                    prepend-inner-icon="mdi-text-box-outline" rows="1" auto-grow
                                    hide-details="auto"></v-textarea>
                            </v-col>
                        </v-row>
                        <v-row class="mt-4">
                            <v-col cols="12" md="6">
                                <v-text-field :error-messages="errors.precio_venta" v-model="form.precio_venta"
                                    label="Precio Venta" variant="outlined" class="rounded-lg"
                                    prepend-inner-icon="mdi-cash-multiple" prefix="Bs." type="number"
                                    hide-details="auto"></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field :error-messages="errors.precio_compra" v-model="form.precio_compra"
                                    label="Precio Compra" variant="outlined" class="rounded-lg"
                                    prepend-inner-icon="mdi-cash" prefix="Bs." type="number"
                                    hide-details="auto"></v-text-field>
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

import useProducto from '@/services/useProducto';
import useDialog from '@/services/useDialog';
import { showAlert, showAlertConfirmation } from '@/utils/SweetAlert';

const headers = ref([
    { title: 'Nro', value: 'numero_secuencial' },
    { title: 'Sucursal', value: 'relacion_sucursal.nombre_sucursal' },
    { title: 'Nombre', value: 'nombre' },
    { title: 'Descripcion', value: 'descripcion' },
    { title: 'Precio compra', value: 'precio_compra' },
    { title: 'Precio venta', value: 'precio_venta' },
    { title: 'Estado', value: 'estado' },
    { title: 'Acciones', value: 'actions', sortable: false },
]);

const dialog = ref(false);
const dialogTittle = ref();
const loading = ref(false);
const search = ref();

const form = reactive({
    id: '',
    sucursal_id: '',
    nombre: '',
    descripcion: '',
    precio_compra: '',
    precio_venta: '',
});

const accion = ref();
const errors = reactive({});



const { getSucursalesActivas, sucursalesActivas } = useDialog();
const { getProductos, productos, storeProducto, updateProducto, desactiveProducto, activeProducto } = useProducto();
const openDialog = (modo, element = null) => {
    if (modo === 'crear') {
        dialogTittle.value = 'Registrar Producto';
        dialog.value = true;
        accion.value = 'crear';
    } else if (modo === 'editar') {
        dialogTittle.value = 'Editar Producto';
        dialog.value = true;
        accion.value = 'editar';
        if (element) {
            form.id = element.id
            form.sucursal_id = element.sucursal_id
            form.nombre = element.nombre
            form.descripcion = element.descripcion
            form.precio_compra = element.precio_compra
            form.precio_venta = element.precio_venta
        }
    }
}

const actionButton = async () => {
    try {
        loading.value = true;
        if (accion.value === 'crear') {
            await storeProducto(form);
            showAlert('¡Registro exitoso!', 'Se ha registrado con éxito', 'success');
        } else {
            await updateProducto(form.id, form);
            showAlert('¡Actualización exitosa!', 'Se ha actualizado con éxito', 'success');
        }
        closeDialog();
        getProductos();
    } catch (error) {
        loading.value = false;
        if (error.response) {
            errors.sucursal_id = error.response.data.data.sucursal_id;
            errors.nombre = error.response.data.data.nombre;
            errors.descripcion = error.response.data.data.descripcion;
            errors.precio_compra = error.response.data.data.precio_compra;
            errors.precio_venta = error.response.data.data.precio_venta;
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
        await desactiveProducto(id);
        await getProductos();
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
        await activeProducto(id)
        await getProductos();
        showAlert('Activado', 'El registro ha sido activado.', 'success');
    } else {
        showAlert('Cancelado', 'La activación ha sido cancelada.', 'error');
    }
}
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(value);
};
const closeDialog = () => {
    dialog.value = false;
    limpiarDialog();
}
const limpiarDialog = () => {
    form.id = '';
    form.sucursal_id = '';
    form.nombre = '';
    form.descripcion = '';
    form.precio_compra = '';
    form.precio_venta = '';
    errors.sucursal_id = '';
    errors.nombre = '';
    errors.descripcion = '';
    errors.precio_compra = '';
    errors.precio_venta = '';
    loading.value = false;
}

onMounted(() => {
    getProductos();
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