<template>
    <!-- <v-row class="mt-10">
        <v-col cols="12" sm="8">
            <div class="text-h5 text-left ">Usuarios registrados
            </div>
        </v-col>
        <v-spacer></v-spacer>
        <v-col cols="12" sm="1">
            <v-btn color="primary" @click="openDialog('crear')" dark><v-icon>mdi-plus</v-icon></v-btn>
        </v-col>
    </v-row>
    <v-row class="mb-5">
        <v-col cols="12" sm="5">
            <v-text-field v-model="search" density="compact" @input="searchData" variant="solo" label="Buscar"
                append-inner-icon="mdi-magnify" single-line hide-details></v-text-field>
        </v-col>
    </v-row>
    <v-table density="compact">
        <thead>
            <tr>
                <th class="text-left">
                    Nro
                </th>
                <th class="text-left">
                    Sucursal
                </th>
                <th class="text-left">
                    Correo
                </th>
                <th class="text-left">
                    Nombres
                </th>
                <th class="text-left">
                    Paterno
                </th>
                <th class="text-left">
                    Materno
                </th>
                <th class="text-left">
                    Cedula
                </th>
                <th class="text-left">
                    Telefono
                </th>
                <th class="text-left">
                    Estado
                </th>
                <th class="text-left">
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, i) in usuarios " :key="i">
                <td>{{ i + 1 }}</td>
                <td>{{ item.relacion_sucursal.nombre_sucursal }}</td>
                <td>{{ item.relacion_user.email }}</td>
                <td>{{ item.nombre }}</td>
                <td>{{ item.paterno }}</td>
                <td>{{ item.materno }}</td>
                <td>{{ item.cedula }}</td>
                <td>{{ item.telefono }}</td>
                <td>
                    <v-btn class="ma-2" @click="desactivar(item.id)" v-if="item.estado === 1" variant="text"
                        icon="mdi-checkbox-marked" color="green"></v-btn>
                    <v-btn class="ma-2" v-else @click="activar(item.id)" variant="text" icon="mdi-close-circle"
                        color="red-lighten-2"></v-btn>
                </td>
                <td>
                    <v-btn @click="openDialog('editar', item)" class="ma-2" icon="mdi-pencil" color="yellow"></v-btn>
                </td>
            </tr>
        </tbody>
    </v-table>
    <div class="text-center">
        <v-row justify="center">
            <v-col cols="8">
                <v-container class="max-width">
                    <v-pagination color="green-darken-4
                                    " active-color="teal-darken-4" @click="onPageChange" v-model="current" class="my-4"
                        :length="total"></v-pagination>
                </v-container>
            </v-col>
        </v-row>
    </div> -->

    <v-container fluid>
        <v-card class="elevation-3 rounded-lg">
            <v-card-title class="text-h4 font-weight-bold pa-6 primary white--text">
                Usuarios registrados
                <v-spacer></v-spacer>
                <v-btn color="#5549C5" @click="openDialog('crear')" elevation="2" large rounded>
                    <v-icon left>mdi-plus</v-icon>
                    Agregar Personal
                </v-btn>
            </v-card-title>

            <v-card-text>
                <v-row align="center" class="mb-4">
                    <v-col cols="12" sm="6" md="4">
                        <v-text-field v-model="search" prepend-inner-icon="mdi-magnify"
                            label="Buscar en todas las columnas" outlined dense clearable></v-text-field>
                    </v-col>
                </v-row>
                <v-data-table :headers="headers" :items="usuarios" :search="search" :items-per-page="10"
                    class="elevation-2 rounded-lg custom-table" :no-data-text="'No hay datos disponibles'"
                    :no-results-text="'No se encontraron resultados'" :items-per-page-text="'Sucursal por página'"
                    :page-text="'{0}-{1} de {2}'" :footer-props="{
                        'items-per-page-options': [5, 10, 15, 20],
                        'items-per-page-text': 'Sucursales por página',
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
                    <template v-slot:item.foto="{ item }">
                        <img class="mx-auto" :height="120" :width="120" :src="detalleURL(item.foto)" alt=""
                            @click="openImageDialog(detalleURL(item.foto))" />
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
                            <v-col cols="12" sm="6">
                                <v-select :error-messages="errors.sucursal_id" v-model="form.sucursal_id"
                                    :items="sucursalesActivas" item-title="nombre_sucursal" item-value="id"
                                    density="comfortable" label="Sucursal" variant="outlined" class="rounded-lg"
                                    prepend-inner-icon="mdi-domain"></v-select>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-select :error-messages="errors.rol" v-model="form.rol" :items="roles"
                                    item-title="title" item-value="key" density="comfortable" label="Rol"
                                    variant="outlined" class="rounded-lg" prepend-inner-icon="mdi-domain"></v-select>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-avatar v-if="form.foto" class="mb-5" label="qwe" text="test" color="grey" size="150"
                                    rounded="0">
                                    <v-img v-if="form.foto" :src="imagenMin"></v-img>
                                </v-avatar>
                                <v-file-input label="Cargar Foto" @change="handleImageChange"
                                    @click:clear="deleteImagen"></v-file-input>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field :error-messages="errors.nombre" v-model="form.nombre" label="Nombres"
                                    variant="outlined" class="rounded-lg"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field :error-messages="errors.email" v-model="form.email"
                                    label="Correo electronico" variant="outlined" class="rounded-lg"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field :error-messages="errors.paterno" v-model="form.paterno" label="Paterno"
                                    variant="outlined" class="rounded-lg"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field :error-messages="errors.materno" v-model="form.materno" label="Materno"
                                    variant="outlined" class="rounded-lg"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field :error-messages="errors.cedula" v-model="form.cedula"
                                    label="Cedula de identidad" variant="outlined" class="rounded-lg"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field :error-messages="errors.telefono" v-model="form.telefono" label="Telefono"
                                    variant="outlined" class="rounded-lg"></v-text-field>
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
    <v-dialog v-model="dialogImage" max-width="800">
        <div class="flex justify-center">
            <img :src="imageURL" alt="Imagen" :width="400" />
        </div>
    </v-dialog>
</template>
<script setup>
import { ref, onMounted, reactive } from 'vue';

import useUsuario from '@/services/usePersonal';
import useDialog from '@/services/useDialog';
import { showAlert, showAlertConfirmation } from '@/utils/SweetAlert';
import { useServerStore } from '@/stores/useService';
const dialog = ref(false);
const dialogTittle = ref();
const loading = ref(false);
const search = ref();
const imageURL = ref('')
const dialogImage = ref(false)
const serve = useServerStore();

const form = reactive({
    id: '',
    sucursal_id: '',
    nombre: '',
    paterno: '',
    materno: '',
    cedula: '',
    telefono: '',
    email: '',
    rol: '',
    foto: '',
});
const imagenMin = ref();

const accion = ref();
const errors = reactive({});

const headers = ref([
    { title: 'Nro', value: 'numero_secuencial' },
    { title: 'Sucursal', value: 'relacion_sucursal.nombre_sucursal' },
    { title: 'Correo', value: 'relacion_user.email' },
    { title: 'Nombres', value: 'nombre' },
    { title: 'Paterno', value: 'paterno' },
    { title: 'Materno', value: 'materno' },
    { title: 'C.I.', value: 'cedula' },
    { title: 'Telefono', value: 'telefono' },
    { title: 'Rol', value: 'relacion_user.rol' },
    { title: 'Foto', value: 'foto' },
    { title: 'Estado', value: 'estado' },
    { title: 'Acciones', value: 'actions', sortable: false },
]);

const roles = [
    { key: 'admin', title: 'Administrador' },
    { key: 'recep', title: 'Recepcionista' },
]

const { getSucursalesActivas, sucursalesActivas } = useDialog();
const { getUsuarios, usuarios, storeUsuario, updateUsuario, desactiveUsuario, activeUsuario } = useUsuario();
const openDialog = (modo, element = null) => {
    if (modo === 'crear') {
        dialogTittle.value = 'Registrar Personal';
        dialog.value = true;
        accion.value = 'crear';
    } else if (modo === 'editar') {
        dialogTittle.value = 'Editar Personal';
        dialog.value = true;
        accion.value = 'editar';
        if (element) {
            form.id = element.id
            form.sucursal_id = element.sucursal_id
            form.nombre = element.nombre
            form.paterno = element.paterno
            form.materno = element.materno
            form.cedula = element.cedula
            form.telefono = element.telefono
            form.email = element.relacion_user.email
            form.rol = element.rol
            form.foto = element.foto,
                imagenMin.value = detalleURL(element.foto)
        }
    }
}
const searchData = async () => {
    await getUsuarios(search.value)
}

const actionButton = async () => {
    try {
        loading.value = true;
        const formData = new FormData();
        formData.append('sucursal_id', form.sucursal_id);
        formData.append('nombre', form.nombre);
        formData.append('paterno', form.paterno);
        formData.append('materno', form.materno);
        formData.append('cedula', form.cedula);
        formData.append('telefono', form.telefono);
        formData.append('email', form.email);
        formData.append('rol', form.rol);
        if (form.foto instanceof File) {
            formData.append('foto', form.foto, form.foto.name);
        }
        if (accion.value === 'crear') {
            await storeUsuario(formData);

            showAlert('¡Registro exitoso!', 'Se ha registrado con éxito', 'success');
        } else {
            formData.append('_method', 'PUT');
            await updateUsuario(form.id, formData);
            showAlert('¡Actualización exitosa!', 'Se ha actualizado con éxito', 'success');
        }
        closeDialog();
        getUsuarios();
    } catch (error) {
        loading.value = false;
        if (error.response) {
            errors.sucursal_id = error.response.data.data.sucursal_id;
            errors.nombre = error.response.data.data.nombre;
            errors.paterno = error.response.data.data.paterno;
            errors.materno = error.response.data.data.materno;
            errors.cedula = error.response.data.data.cedula;
            errors.telefono = error.response.data.data.telefono;
            errors.email = error.response.data.data.email;
            errors.rol = error.response.data.data.rol;
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
        await desactiveUsuario(id);
        await getUsuarios();
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
        await activeUsuario(id)
        await getUsuarios();
        showAlert('Activado', 'El registro ha sido activado.', 'success');
    } else {
        showAlert('Cancelado', 'La activación ha sido cancelada.', 'error');
    }
}
const detalleURL = (archivo) => {
    return `${serve.serverPath}storage/fotos/${archivo}`;
}
const handleImageChange = (event) => {
    const img = event.target.files[0];
    form.foto = img
    loadImagen(img);
}
const loadImagen = (file) => {
    let reader = new FileReader();
    reader.onload = (e) => {
        imagenMin.value = e.target.result
    }
    reader.readAsDataURL(file);
}

const deleteImagen = () => {
    form.foto = '';
    imagenMin.value = '';
}
const openImageDialog = (url) => {
    imageURL.value = url
    dialogImage.value = true
}
const closeDialog = () => {
    dialog.value = false;
    limpiarDialog();
}
const limpiarDialog = () => {
    form.id = '';
    form.sucursal_id = '';
    form.nombre = '';
    form.paterno = '';
    form.materno = '';
    form.cedula = '';
    form.telefono = '';
    form.email = '';
    form.rol = '';
    errors.sucursal_id = '';
    errors.nombre = '';
    errors.paterno = '';
    errors.materno = '';
    errors.cedula = '';
    errors.telefono = '';
    errors.email = '';
    errors.rol = '';
    loading.value = false;
}

onMounted(() => {
    searchData();
    getUsuarios();
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