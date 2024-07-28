import { http } from "./httpService";
import { ref } from "vue";
export default function useProducto() {

    const productos = ref([]);


    const getProductos = async () => {
        let response = await http().get(`/productos`);
        productos.value = response.data;
    }
    const storeProducto = (data) => {
        return http().post('/productos', data);
    }
    const updateProducto = async (id, data) => {
        return http().put(`/productos/${id}`, data)
    }
    const desactiveProducto = async (id) => {
        return http().delete(`/productos/${id}`);
    }
    const activeProducto = async (id) => {
        return http().put(`/activeProducto/${id}`);
    }
    return {
        getProductos,
        productos,
        storeProducto,
        updateProducto,
        desactiveProducto,
        activeProducto,
    }
}