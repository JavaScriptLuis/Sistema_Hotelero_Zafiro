import { http } from "./httpService";
import { ref } from "vue";

export default function useConsumo() {


    const agregarConsumo = async (data) => {
        return await http().post('/add-consumo', data);
    }
    const pagarDetalle = async (id, data) => {
        return await http().put(`/pagarDetalle/${id}`, data);
    }
    return {
        agregarConsumo,
        pagarDetalle
    }
}