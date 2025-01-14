<template>
    <div class="mt-5 mx-5 lg:ml-10">
        <div v-if="loading === true">
            <Loading :all="true" />
        </div>

        <div v-else>
            <div role="alert" class="alert alert-default">
                <div class="flex items-center font-bold gap-4">
                    <span class="pi pi-verified"></span>
                    <h1>Selamat datang, {{ peserta.nama_peserta }}!</h1>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-4">
                <div class="card bg-base-100 border shadow-sm mt-4 lg:my-4 p-4">
                    <div class="flex gap-4">

                        <div class="flex flex-col gap-2">
                            <div>
                                <p class="text-lg font-bold">Nama</p>
                                <p>{{ peserta.nama_peserta }}</p>
                            </div>
                            <div>
                                <p class="text-lg font-bold">Nomor Induk Peserta</p>
                                <p>{{ peserta.nomor_induk }}</p>
                            </div>
                            <div>
                                <p class="text-lg font-bold">Nomor Telepon</p>
                                <p>{{ peserta.nomor_telepon }}</p>
                            </div>
                            <div>
                                <p class="text-lg font-bold">Periode Magang</p>
                                <p>{{ peserta.tanggal_mulai }} s/d {{ peserta.tanggal_selesai }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 border shadow-sm lg:my-4 p-4">
                    <div class="flex flex-col gap-2 px-1">
                        <div>
                            <p class="text-lg font-bold">Institusi Pendidikan</p>
                            <p>{{ peserta.institusi?.nama_institusi }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Jurusan</p>
                            <p>{{ peserta.jurusan }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Dosen Pembimbing</p>
                            <p>{{ peserta.nama_pembimbing }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Lokasi Magang</p>
                            <p>{{ peserta.lokasi?.nama_lokasi }}</p>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
        

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 sm:my-5 md:my-5 mt-5">
            <div>
                <div v-if="loading === true" class="my-5">
                    <Loading :chart="true"/>
                </div>

                <div v-else>
                    <DoughnutChart v-if="labels_presensi.length && total_presensi.length"
                        title="Presensi Magang"
                        :labels_data="labels_presensi"
                        :total_peserta="total_presensi"
                        label_name="Total Presensi"
                    />
                </div>
            </div>
            
            <div>
                <div v-if="loading === true" class="my-5">
                    <Loading :chart="true"/>
                </div>

                <div v-else>
                    <DoughnutChart v-if="labels_logbook.length && total_logbook.length"
                        title="Logbook Magang"
                        :labels_data="labels_logbook"
                        :total_peserta="total_logbook"
                        label_name="Total Logbook"
                    />
                </div>
            </div>

            <div>
                <div v-if="loading === true" class="my-5">
                    <Loading :chart="true"/>
                </div>

                <div v-else>
                    <DoughnutChart v-if="labels_administrasi.length && total_administrasi.length"
                        title="Administrasi Magang"
                        :labels_data="labels_administrasi"
                        :total_peserta="total_administrasi"
                        label_name="Total Dokumen"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import { ref, onMounted } from 'vue';
import Loading from '@/components/Loading.vue';
import DoughnutChart from '@/components/DoughnutChart.vue';

const AuthStore = useAuthStore();
const loading = ref(false);
const peserta = ref([]);
const peserta_id = ref("");

const getPesertaById = async () => {
    try {
        loading.value=true;
        const { data } = await customAPI.get('/me', {
            headers: { Authorization: `Bearer ${AuthStore.token}`},
        });
        peserta_id.value = data.user.peserta.id;
        
        const { data: dataPeserta } = await customAPI.get(`/peserta/${peserta_id.value}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });
        peserta.value = dataPeserta.data;
    } catch (error) {
        console.log("Data peserta tidak ditemukan.", error);
    } finally {
        loading.value = false;
    }
}

const presensiData = ref([]);
const labels_presensi = ref([]);
const total_presensi = ref([]);
const FetchPresensi = async () => {
    try {
        loading.value=true;
        const { data: presensiResponse } = await customAPI.get(`/dashboard/presensi-peserta/${peserta_id.value}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });

        presensiData.value = presensiResponse.data;
        labels_presensi.value = presensiResponse.data.map(item => item.keterangan_name);
        total_presensi.value = presensiResponse.data.map(item => item.total_presensi);
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
};

const logbookData = ref([]);
const labels_logbook = ref([]);
const total_logbook = ref([]);
const FetchLogbook = async () => {
    try {
        loading.value=true;
        const { data: logbookResponse } = await customAPI.get(`/dashboard/logbook-peserta/${peserta_id.value}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });

        logbookData.value = logbookResponse.data;
        labels_logbook.value = logbookResponse.data.map(item => item.name);
        total_logbook.value = logbookResponse.data.map(item => item.total_logbook);
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
};

const administrasiData = ref([]);
const labels_administrasi = ref([]);
const total_administrasi = ref([]);
const FetchAdministrasi = async () => {
    try {
        loading.value=true;
        const { data: administrasiResponse } = await customAPI.get(`/dashboard/administrasi-peserta/${peserta_id.value}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });

        administrasiData.value = administrasiResponse.data;
        labels_administrasi.value = administrasiResponse.data.map(item => item.name);
        total_administrasi.value = administrasiResponse.data.map(item => item.total_dokumen);
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    await getPesertaById();
    FetchPresensi();
    FetchLogbook();
    FetchAdministrasi();
})
</script>