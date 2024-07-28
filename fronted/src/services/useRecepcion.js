import { http } from "./httpService";
import { ref } from 'vue';

export default function useRecepcion() {
    const habitacionesRecepcion = ref([]);
    const habitacionesSalida = ref([]);
    const getHabitacionesRecepcion = async (data) => {
        try {
            let response = await http().post('/recepcion-habitaciones', data);
            habitacionesRecepcion.value = response.data;
        } catch (error) {
            console.error(error);
        }
    }
    const getHabitacionesRecepcionSalida = async (data) => {
        try {
            let response = await http().post('/recepcion-habitaciones-salida', data);
            habitacionesSalida.value = response.data;
        } catch (error) {
            console.error(error);
        }
    }
    const registrarEstadia = async (data) => {
        return await http().post('/estadias', data);
    }
    const updateEstadia = async (id, data) => {
        return await http().put(`/editar-estadia/${id}`, data);
    }
    const checkSalida = async (id, data) => {
        return await http().put(`/check-salida/${id}`, data);
    }
    const terminarLimpieza = async (id) => {
        return http().put(`/terminarLimpieza/${id}`);
    }
    const agregar_horas = async (id, data) => {
        return http().put(`/agregar-hora/${id}`, data);
    }
    return {
        getHabitacionesRecepcion,
        habitacionesRecepcion,
        registrarEstadia,
        checkSalida,
        getHabitacionesRecepcionSalida,
        habitacionesSalida,
        terminarLimpieza,
        updateEstadia,
        agregar_horas
    }
}