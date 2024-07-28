import { http } from "./httpService";
import { ref } from "vue";
export default function usePiso() {

    const pisos = ref([]);
    const current = ref(0);
    const total = ref(0);

    const getPisos = async (search = '') => {
        let response = await http().get(`/pisos?search=${search}`);
        pisos.value = response.data;
    }
    const storePiso = (data) => {
        return http().post('/pisos', data);
    }
    const updatePiso = async (id, data) => {
        return http().put(`/pisos/${id}`, data)
    }
    const desactivePiso = async (id) => {
        return http().delete(`/pisos/${id}`);
    }
    const activePiso = async (id) => {
        return http().put(`/activePiso/${id}`);
    }
    return {
        getPisos,
        pisos,
        current,
        total,
        storePiso,
        updatePiso,
        desactivePiso,
        activePiso
    }
}