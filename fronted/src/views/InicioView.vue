<template>
     
    <v-container fluid class="dashboard pa-6">
        <h1><v-icon>mdi-home</v-icon> PRINCIPAL</h1>
        <v-row class="mb-6">
            <v-col v-for="(stat, index) in statsCards" :key="index" cols="12" sm="6" md="3">
                <v-card :color="stat.color" dark elevation="4" class="rounded-lg">
                    <v-card-text>
                        <div class="text-h4 font-weight-bold mb-2">{{ estadisticas[stat.value] }}</div>
                        <div>{{ stat.title }}</div>
                        <v-icon :icon="stat.icon" size="80" class="card-icon"></v-icon> <!-- Cambiado aquí -->
                    </v-card-text>
                </v-card>

            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="6">
                <v-card elevation="4" class="rounded-lg">
                    <v-card-title class="text-h6 font-weight-bold">Estadisticas</v-card-title>
                    <v-card-text style="height: 300px;">
                        <BarChart :totalesMensuales="estadiaYear" />
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" md="6">
                <v-card elevation="4" class="rounded-lg">
                    <v-card-title class="text-h6 font-weight-bold">Habitaciones Ocupadas</v-card-title>
                    <v-card-text>
                        <v-data-table :headers="headers" :items="estadisticas.inactives || []" :items-per-page="5"
                            :no-data-text="'No hay datos disponibles'" :no-results-text="'No se encontraron resultados'"
                            :loading="loading" loading-text="Cargando... Por favor, espere" class="elevation-1">
                            <template v-slot:item.servicio_actual="{ item }">
                                {{ getFullName(item.servicio_actual.cliente) }}
                            </template>
                            <template v-slot:item.servicio_actual.fecha_inicio="{ item }">
                                {{ formatDate(item.servicio_actual.fecha_inicio) }}
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import useReporte from '@/services/useReporte';
import BarChart from '@/components/BarChart.vue';
const { getEstadisticas, estadisticas, getYear, estadiaYear } = useReporte();
const updateTrigger = ref(0);
const loading = ref(false);

const statsCards = [
    { title: 'Habitaciones Disponibles', value: 'actives', color: 'success', icon: 'mdi-hotel' },
    { title: 'Habitaciones Ocupadas', value: 'countInactives', color: 'error', icon: 'mdi-door-closed-lock' },
    { title: 'Habitaciones en Limpieza', value: 'limpiezas', color: 'warning', icon: 'mdi-broom' },
    { title: 'Reservas del Día', value: 'reservas', color: 'info', icon: 'mdi-calendar-check' },
];

const headers = [
    { title: 'ID', align: 'start', key: 'id' },
    { title: 'Cliente', key: 'servicio_actual' },
    { title: 'Habitación', key: 'numero' },
    { title: 'Tipo', key: 'relacion_tipo.nombre' },
    { title: 'Fecha ingreso', key: 'servicio_actual.fecha_inicio' },
];

const getFullName = (item) => {
    if (!item) return '';
    return `${item.nombre} ${item.paterno} ${item.materno}`;
};

const formatDate = (date) => {
    const dateObj = new Date(date);
    return new Date(dateObj.getUTCFullYear(), dateObj.getUTCMonth(), dateObj.getUTCDate()).toLocaleDateString();
};


onMounted(async () => {
    loading.value = true;
    await getEstadisticas();
    await getYear();
    loading.value = false;
    setInterval(() => {
        updateTrigger.value++;
    }, 5000);
});

watch(() => updateTrigger.value, async () => {
    loading.value = true;
    await getEstadisticas();
    loading.value = false;
});
</script>

<style scoped>
h1 {
    display: flex;
    align-items: center; /* Alinea verticalmente el ícono con el texto */
    color: rgb(0, 0, 0);
}

.card-icon {
    position: absolute;
    top: 16px; /* Ajusta según la alineación vertical deseada */
    right: 16px; /* Ajusta para alinear a la derecha */
    opacity: 0.6; /* Si deseas controlar la opacidad */
}

.dashboard {
    background-color: rgba(255, 255, 255, 0.799); /* Cambia la opacidad al 50% para hacerlo semi-transparente */
    border-radius: 15px; /* Esto redondeará los bordes con un radio de 15px */
}

.v-card {
    transition: all 0.3s ease;
    border: 2px solid black; /* Agrega un borde negro de 2px */
}

.v-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 25px 0 rgba(0, 0, 0, .1);
}


.opacity-6 {
    opacity: 0.6;
}
</style>