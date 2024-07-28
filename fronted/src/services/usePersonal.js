import { http } from "./httpService";
import { ref } from "vue";
export default function usePersonal() {

    const usuarios = ref([]);


    const getUsuarios = async () => {
        let response = await http().get(`/personales`);
        usuarios.value = response.data;

    }
    const storeUsuario = (data) => {
        return http().post('/personales', data);
    }
    const updateUsuario = async (id, data) => {
        return http().post(`/personales/${id}`, data)
    }
    const desactiveUsuario = async (id) => {
        return http().delete(`/personales/${id}`);
    }
    const activeUsuario = async (id) => {
        return http().put(`/activePersonal/${id}`);
    }
    return {
        getUsuarios,
        usuarios,
        storeUsuario,
        updateUsuario,
        desactiveUsuario,
        activeUsuario
    }
}