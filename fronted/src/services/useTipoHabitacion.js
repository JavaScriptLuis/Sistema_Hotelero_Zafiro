import { http } from "./httpService";
import { ref } from "vue";
export default function useTipoHabitacion() {

    const tipoHabitaciones = ref([]);

    const getTipoHabitacion = async () => {
        let response = await http().get(`/tipo-habitaciones`);
        tipoHabitaciones.value = response.data;
    }
    const storeTipoHabitacion = (data) => {
        return http().post('/tipo-habitaciones', data);
    }
    const updateTipoHabitacion = async (id, data) => {
        return http().put(`/tipo-habitaciones/${id}`, data)
    }
    const desactiveTipoHabitacion = async (id) => {
        return http().delete(`/tipo-habitaciones/${id}`);
    }
    const activeTipoHabitacion = async (id) => {
        return http().put(`/tipo-habitaciones/${id}`);
    }
    return {
        getTipoHabitacion,
        tipoHabitaciones,
        storeTipoHabitacion,
        updateTipoHabitacion,
        desactiveTipoHabitacion,
        activeTipoHabitacion
    }
}