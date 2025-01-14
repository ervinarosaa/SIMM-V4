<template>
    <div class="mt-5 mx-5 lg:ml-10">
        <div class="grid sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 items-center justify-center">
            <div v-if="loadingScorecard" class="stats shadow border lg:w-full">
                <Loading :scorecard="true"/>
            </div>
            <div v-else class="stats shadow border lg:w-full">
                <div class="stat">
                    <div class="stat-figure text-primary text-4xl">
                        <span class="pi pi-users"></span>
                    </div>
                    <div class="stat-title">Total Peserta Aktif</div>
                    <div class="stat-value text-primary">{{ AllScorecard.total_peserta_aktif }}</div>
                </div>
            </div>

            <div v-if="loadingScorecard" class="stats shadow border lg:w-full">
                <Loading :scorecard="true"/>
            </div>
            <div v-else class="stats shadow border lg:w-full">
                <div class="stat">
                    <div class="stat-figure text-primary text-4xl">
                        <span class="pi pi-building"></span>
                    </div>
                    <div class="stat-title">Total Institusi</div>
                    <div class="stat-value text-primary">{{ AllScorecard.total_institusi }}</div>
                </div>
            </div>

            <div v-if="loadingScorecard" class="stats shadow border lg:w-full">
                <Loading :scorecard="true"/>
            </div>
            <div v-else class="stats shadow border lg:w-full">
                <div class="stat">
                    <div class="stat-figure text-primary text-4xl">
                        <span class="pi pi-map"></span>
                    </div>
                    <div class="stat-title">Total Lokasi</div>
                    <div class="stat-value text-primary">{{ AllScorecard.total_lokasi }}</div>
                </div>
            </div>

            <div v-if="loadingScorecard" class="stats shadow border lg:w-full">
                <Loading :scorecard="true"/>
            </div>
            <div v-else class="stats shadow border lg:w-full">
                <div class="stat">
                    <div class="stat-figure text-primary text-4xl">
                        <span class="pi pi-verified"></span>
                    </div>
                    <div class="stat-title">Total Kuota Magang</div>
                    <div class="stat-value text-primary">{{ AllScorecard.total_kuota_magang }}</div>
                </div>
            </div>
        </div>

        <div class="divider"></div>
        
        <div v-if="loading === true">
            <Loading :all="true"/>
        </div>
        <div v-else class="flex flex-col lg:flex-row gap-4 items-center justify-items-center">
            <div class="flex flex-col items-start gap-4">
                <BarChart v-if="labels_institusi.length && peserta_institusi.length"
                    title="Distribusi Peserta Berdasarkan Institusi"
                    :labels_data="labels_institusi"
                    :total_peserta="peserta_institusi"
                />
                <BarChart v-if="labels_lokasi.length && peserta_lokasi.length"
                    title="Distribusi Peserta Berdasarkan Bidang Penempatan"
                    :labels_data="labels_lokasi"
                    :total_peserta="peserta_lokasi"
                />
            </div>

            <div class="flex flex-col lg:flex-col md:flex-row items-center gap-4">
                <DoughnutChart v-if="labels_status.length && peserta_status.length"
                    title="Distribusi Status Magang"
                    :labels_data="labels_status"
                    :total_peserta="peserta_status"
                    label_name="Total Peserta"
                />

                <DoughnutChart v-if="labels_tingPend.length && peserta_tingPend.length"
                    title="Distribusi Tingkat Pendidikan"
                    :labels_data="labels_tingPend"
                    :total_peserta="peserta_tingPend"
                    label_name="Total Institusi"
                />
            </div>
        </div>
    </div>
</template>


<script setup>
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import { ref, onMounted } from 'vue';
import Loading from '@/components/Loading.vue';
import BarChart from '@/components/BarChart.vue';
import DoughnutChart from '@/components/DoughnutChart.vue';

const AuthStore = useAuthStore();
const loadingScorecard = ref(false);
const loading = ref(false);
const AllScorecard = ref({});

const FetchScorecard = async () => {
    try {
        loadingScorecard.value=true;
        const { data } = await customAPI.get('/dashboard/scorecard', {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });
        AllScorecard.value = data.data; 
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loadingScorecard.value = false;
    }
}

const institusiData = ref([]);
const labels_institusi = ref([]);
const peserta_institusi = ref([]);
const FetchInstitusi = async () => {
    try {
        loading.value=true;
        const { data: institusiResponse } = await customAPI.get('/dashboard/peserta-by-institusi', {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });

        institusiData.value = institusiResponse.data;
        
        labels_institusi.value = institusiResponse.data.map(item => item.name);
        peserta_institusi.value = institusiResponse.data.map(item => item.total_peserta);
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
};

const lokasiData = ref([]);
const labels_lokasi = ref([]);
const peserta_lokasi = ref([]);
const FetchLokasi = async () => {
    try {
        loading.value=true;
        const { data: lokasiResponse } = await customAPI.get('/dashboard/peserta-by-lokasi', {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });

        lokasiData.value = lokasiResponse.data;

        labels_lokasi.value = lokasiResponse.data.map(item => item.nama_lokasi);
        peserta_lokasi.value = lokasiResponse.data.map(item => item.total_peserta);
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}

const statusData = ref([]);
const labels_status = ref([]);
const peserta_status = ref([]);
const FetchStatus = async() => {
    try {
        loading.value=true;
        const { data: statusResponse } = await customAPI.get('/dashboard/peserta-by-status', {
            headers: { Authorization: `Bearer ${AuthStore.token}`}
        });

        statusData.value = statusResponse.data;

        labels_status.value = statusResponse.data.map(item => item.nama_status);
        peserta_status.value = statusResponse.data.map(item => item.total_peserta);
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}

const tingkatPendData = ref([]);
const labels_tingPend = ref([]);
const peserta_tingPend = ref([]);
const FetchTingPend = async () => {
    try {
        loading.value=true;
        const { data: tingPendResponse } = await customAPI.get('/dashboard/tingkat-pendidikan', {
            headers: { Authorization: `Bearer ${AuthStore.token}`}
        });

        tingkatPendData.value = tingPendResponse.data;
        labels_tingPend.value = tingPendResponse.data.map(item => item.name);
        peserta_tingPend.value = tingPendResponse.data.map(item => item.total_peserta);
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    FetchScorecard();
    FetchInstitusi();
    FetchLokasi();
    FetchStatus();
    FetchTingPend();
});
</script>
