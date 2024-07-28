import { http } from "./httpService";
import { ref } from 'vue';
export default function useDialog() {

    const usuariosActivos = ref();
    const habitacionesActivas = ref();
    const pisosActivos = ref();
    const sucursalesActivas = ref();
    const productosActivos = ref();
    const personalActivos = ref();
    const getUsuariosActivos = async () => {
        let response = await http().get('/clientes-activos');
        usuariosActivos.value = response.data;
    }
    const getHabitacionesActivas = async () => {
        let response = await http().get('/habitaciones-activas');
        habitacionesActivas.value = response.data;
    }
    const getPisosActivos = async () => {
        let response = await http().get('/pisos-activos');
        pisosActivos.value = response.data;
    }
    const getSucursalesActivas = async () => {
        let response = await http().get('/sucursales-activas');
        sucursalesActivas.value = response.data;
    }
    const getProductosActivos = async () => {
        let response = await http().get('/productos-activos');
        productosActivos.value = response.data;
    }
    const getPersonalActivos = async () => {
        let response = await http().get('/personal-activos');
        personalActivos.value = response.data;
    }
    return {
        getUsuariosActivos,
        usuariosActivos,
        getHabitacionesActivas,
        habitacionesActivas,
        getPisosActivos,
        pisosActivos,
        getSucursalesActivas,
        sucursalesActivas,
        getProductosActivos,
        productosActivos,
        getPersonalActivos,
        personalActivos
    }
}