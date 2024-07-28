import { http } from "./httpService";
import { ref } from "vue";
export default function useCliente() {

    const clientes = ref([]);
    const getClientes = async () => {
        try {
            let response = await http().get('/clientes');
            clientes.value = response.data;
        } catch (error) {
            console.error(error);
        }
    }
    const storeCliente = async (data) => {
        return await http().post('/clientes', data);
    }
    const updateCliente = async (id, data) => {
        return await http().put(`/clientes/${id}`, data)
    }
    const desactiveCliente = async (id) => {
        return await http().delete(`/clientes/${id}`);
    }
    const activeCliente = async (id) => {
        return await http().put(`/activateCliente/${id}`);
    }
    return {
        getClientes,
        clientes,
        storeCliente,
        updateCliente,
        desactiveCliente,
        activeCliente
    }
}