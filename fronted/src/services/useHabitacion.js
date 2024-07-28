import { http } from "./httpService";
import { ref } from "vue";
export default function useHabitacion() {

    const habitaciones = ref([]);
    const current = ref(0);
    const total = ref(0);

    const getHabitaciones = async () => {
        let response = await http().get(`/habitaciones`);
        habitaciones.value = response.data;

    }
    const storeHabitacion = (data) => {
        return http().post('/habitaciones', data);
    }
    const updateHabitacion = async (id, data) => {
        return http().put(`/habitaciones/${id}`, data)
    }
    const desactiveHabitacion = async (id) => {
        return http().delete(`/habitaciones/${id}`);
    }
    const activeHabitacion = async (id) => {
        return http().put(`/activeHabitacion/${id}`);
    }

    return {
        getHabitaciones,
        habitaciones,
        current,
        total,
        storeHabitacion,
        updateHabitacion,
        desactiveHabitacion,
        activeHabitacion,
    }
}