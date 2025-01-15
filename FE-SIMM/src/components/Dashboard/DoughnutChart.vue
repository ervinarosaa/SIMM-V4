<template>
    <div class="stats shadow border h-[380px] w-[340px] lg:w-[390px] justify-center">
        <div class="stat items-center">
            <div class="stat-title font-bold text-xl text-center">{{ props.title }}</div>
            <hr>
            <Doughnut :id="`doughnut-chart-${title}`" :options="chartOptions" :data="chartData" />
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Doughnut } from 'vue-chartjs'; 
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';

// Register necessary Chart.js components
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const props = defineProps({
    labels_data: {
        type: Array,
        required: true, 
        default: () => [],
    },
    total_peserta: {
        type: Array,
        required: true, 
        default: () => [],
    },
    title: {
        type: String,
        required: true, 
    },
    label_name: {
        type: String,
    }
});

const chartData = ref({
    labels: props.labels_data,
    datasets: [
        {
            label: props.label_name,
            data: props.total_peserta,
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)'
            ],
            hoverOffset: 4
        }
    ]
});

const chartOptions = ref({
    responsive: true,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
        }
    }
});

watch([props.labels_data, props.total_peserta], () => {
    chartData.value = {
        labels: props.labels_data,
        datasets: [
            {
                label: props.label_name,
                data: props.total_peserta,
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)'
                ],
                hoverOffset: 4
                }
        ]
    };
}, { immediate: true });

</script>