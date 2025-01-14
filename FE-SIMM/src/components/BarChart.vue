<template>
    <div class="stats shadow border w-[340px] sm:w-[600px] md:w-[780px] lg:w-[820px] lg:h-[380px]">
        <div class="stat sm:h-[250px] md:h-[300px] lg:h-[340px]">
            <div class="stat-title font-bold text-xl">{{ props.title }}</div>
            <hr>
            <div class="lg:w-full lg:h-[310px]">
                <Bar id="my-chart-id" :options="chartOptions" :data="chartData" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Bar } from 'vue-chartjs'; 
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';

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
});

const chartData = ref({
    labels: props.labels_data,
    datasets: [
        {
            label: 'Total Peserta',
            data: props.total_peserta,
            backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)',
                    'rgb(201, 203, 207)'
                ],
            borderWidth: 1
        }
    ]
});

const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            // skala sumbu y
            ticks: {
                stepSize: 5,
                callback: function(value) {
                    return value;
                },
            },
            suggestedMin: 0,
        },
        x: {
            display: false
        }
    },
    plugins: {
        legend: {
            display: true,
            position: 'right', 
            labels: {
                boxWidth: 12, 
                padding: 5, 
                generateLabels: function(chart) {
                    var data = chart.data;
                    if (data.labels.length) {
                        return data.labels.map(function(label, i) {
                            return {
                                text: label,
                                fillStyle: data.datasets[0].backgroundColor[i],
                                hidden: false
                            };
                        });
                    } else {
                        return [];
                    }
                }
            }
        }
    }
});

watch([props.labels_data, props.total_peserta], () => {
    chartData.value = {
        labels: props.labels_data,
        datasets: [
            {
                label: 'Total Peserta',
                data: props.total_peserta,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)',
                    'rgb(201, 203, 207)'
                ],
                borderWidth: 1
            }
        ]
    };
}, { immediate: true });

</script>