import { http } from "./httpService";
import { ref } from "vue";
export default function useSucursal() {

    const sucursales = ref([]);


    const getSucursales = async () => {
        let response = await http().get(`/sucursales`);
        sucursales.value = response.data;
    }
    const storeSucursal = (data) => {
        return http().post('/sucursales', data);
    }
    const updateSucursal = async (id, data) => {
        return http().put(`/sucursales/${id}`, data)
    }
    const desactiveSucursal = async (id) => {
        return http().delete(`/sucursales/${id}`);
    }
    const activeSucursal = async (id) => {
        return http().put(`/activeSucursal/${id}`);
    }
    return {
        getSucursales,
        sucursales,
        storeSucursal,
        updateSucursal,
        desactiveSucursal,
        activeSucursal
    }
}