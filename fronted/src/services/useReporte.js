import { ref } from "vue";
import { http } from "./httpService";
export default function useReporte() {

    const estadisticas = ref([]);
    const estadiaYear = ref();
    const getAtencionPersonal = async (data) => {
        return await http().post('/getAtencionPersonal', data);
    }
    const getEstadisticas = async () => {
        let response = await http().get('/getEstadisticas');
        estadisticas.value = response.data;
    }
    const getYear = async () => {
        let response = await http().get('/totalYear');
        estadiaYear.value = response.data;

    }
    return {
        getAtencionPersonal,
        getEstadisticas,
        estadisticas,
        getYear,
        estadiaYear
    }
}