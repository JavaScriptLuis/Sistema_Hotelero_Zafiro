<template>
    <Bar :data="chartData" :options="chartOptions" />
</template>

<script setup>
import { ref, computed } from 'vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    estadiaYear: Object
});

const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

const chartData = computed(() => {
    if (!props.estadiaYear) {
        return {
            labels: [],
            datasets: [{
                label: 'Total Ingresos',
                backgroundColor: '#4CAF50',
                data: []
            }]
        };
    }

    const labels = [];
    const data = [];
    Object.entries(props.estadiaYear).forEach(([mes, total]) => {
        labels.push(meses[parseInt(mes) - 1]);
        data.push(total);
    });

    return {
        labels: labels,
        datasets: [{
            label: 'Total Ingresos',
            backgroundColor: '#4CAF50',
            data: data
        }]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Total (BOB)'
            }
        }
    },
    plugins: {
        legend: {
            display: true
        },
        title: {
            display: true,
            text: `Ingresos Mensuales ${new Date().getFullYear()} (Registros + Consumos)`
        }
    }
};
</script>