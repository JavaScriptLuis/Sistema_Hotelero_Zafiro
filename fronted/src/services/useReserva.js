import { http } from "./httpService";
import { ref, computed } from 'vue';

export default function useReserva() {
    const reservas = ref([]);

    const eventosCalendario = computed(() => {
        return reservas.value.map(reserva => {
            const fechaInicio = new Date(`${reserva.fecha_inicio}T${reserva.hora_inicio}`);
            const fechaFin = new Date(`${reserva.fecha_fin}T${reserva.hora_fin}`);
            const esMismoDia = reserva.fecha_inicio === reserva.fecha_fin;

            const fechaFinCalendario = new Date(fechaFin);
            if (!esMismoDia) {
                fechaFinCalendario.setDate(fechaFinCalendario.getDate() + 1);
            }

            return {
                title: `${reserva.cliente_identidad} - Hab. ${reserva.habitacion_numero}`,
                start: fechaInicio,
                end: fechaFinCalendario,
                extendedProps: {
                    fechaFinReal: fechaFin,
                    cliente_id: reserva.cliente_id,
                    habitacion_id: reserva.habitacion_id,
                    adelanto: reserva.adelanto,
                    observacion: reserva.observacion,
                    reserva_id: reserva.id
                },
                allDay: !esMismoDia,
                id: reserva.id,
                observacion: reserva.observacion,
                adelanto: reserva.adelanto,
                color: esMismoDia ? undefined : '#378006'
            };
        });
    });

    const getReservas = async () => {
        try {
            let response = await http().get('/reservas');
            reservas.value = response.data;
        } catch (error) {
            console.error(error);
        }
    }

    const storeReserva = async (data) => {
        return await http().post('/reservas', data);
    }

    const updateReserva = async (id, data) => {
        return await http().put(`/reservas/${id}`, data);
    }
    const deleteReserva = async (id) => {
        return await http().delete(`/reservas/${id}`);
    }
    return {
        getReservas,
        reservas,
        eventosCalendario,
        storeReserva,
        updateReserva,
        deleteReserva
    }
}