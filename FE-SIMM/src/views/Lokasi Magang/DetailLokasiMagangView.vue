<template>
    <div class="mt-5 mx-5 lg:ml-10 max-w-[340px] sm:max-w-[660px] md:max-w-[750px] lg:max-w-full">
        <div class="w-full">
            <!-- Breadcrumbs -->
            <Breadcrumbs 
                class="hidden sm:flex sm:flex-col md:flex md:flex-col lg:flex lg:flex-col" 
                :title1="'Lokasi Magang'" 
                :route1="'Lokasi Magang'" 
                :title2="lokasi.nama_lokasi" 
                :route2="'DetailLokasiMagang'" 
                :id2="lokasi.id" 
            />
        </div>
        <!-- Breadcrumbs untuk layar kecil -->
        <Breadcrumbs 
            class="flex flex-col sm:hidden md:hidden lg:hidden" 
            :title1="'Lokasi Magang'" 
            :route1="'Lokasi Magang'"  
            title2="..." 
            :route2="'DetailLokasiMagang'" 
            :id2="lokasi.id" 
        />
        
        <div v-if="loading === true" class="my-4">
            <Loading :chart="true"/>
        </div>
        <div v-else class="lg:flex min-w-full gap-3 items-center">
            <div class="lg:w-[70%] h-full card bg-base-200 border border-slate-300 shadow-lg my-4 p-4">
                <div class="grid lg:grid-rows-2 gap-4">
                    <div>
                        <p class="text-lg font-bold">Lokasi Magang</p>
                        <p>{{ lokasi.nama_lokasi }}</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Alamat Lokasi Magang</p>
                        <p>{{ lokasi.alamat_lokasi }}</p>
                    </div>
                </div>
            </div>
            <div class="lg:w-[70%] h-full card bg-base-200 border border-slate-300 shadow-lg my-4 p-4">
                <div class="grid lg:grid-rows-2 gap-4">
                    <div>
                        <p class="text-lg font-bold">Kepala Bagian</p>
                        <p v-if="lokasi.kepala_bagian !== null">{{ lokasi.kepala_bagian?.nama_kabag }}</p>
                        <p v-else>-</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Nomor Induk Pegawai</p>
                        <p v-if="lokasi.kepala_bagian !== null">{{ lokasi.kepala_bagian?.nip_kabag }}</p>
                        <p v-else>-</p>
                    </div>
                </div>
            </div>
            <div class="lg:w-[30%] h-full grid grid-rows-2">
                <div class="card bg-base-200 border border-slate-300 shadow-lg my-2 p-2 items-center">
                    <p class="text-sm font-bold">Kuota Magang</p>
                    <p class="text-2xl font-bold">{{ lokasi.kuota_magang }}</p>
                </div>
                <div class="card bg-base-200 border border-slate-300 shadow-lg my-2 p-2 items-center">
                    <p class="text-sm font-bold">Kuota Terisi</p>
                    <p class="text-2xl font-bold">{{ lokasi.kuota_terisi }}</p>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <section>
            <h1 class="text-3xl font-bold text-center pb-4">Peserta Magang</h1>
            <div v-if="loading === true" class="my-4">
                <Loading :all="true"/>
            </div>
            <div v-else>
                <div v-if="allPeserta == ''" class="text-center">
                    <div class="bg-base-200 py-2 rounded-lg max-h-[600px] min-h-[50px] overflow-hidden overflow-y-auto w-full">
                        <h1 class="mx-2 my-1 px-2 py-2 font-semibold">Belum ada peserta</h1>
                    </div>
                </div>
                <div v-else class="my-4" v-for="peserta in allPeserta" :key="peserta.id">
                    <div class="card bg-base-200 border border-slate-300 shadow-lg w-[340px] sm:w-auto md:w-[700px] lg:w-full overflow-x-auto"
                        @click="handleCardClick(peserta.id)">
                        <div class="flex h-[200px] lg:h-[140px] items-center p-2">
                            <div class="w-24 aspect-[3/4] rounded-xl overflow-hidden shrink-0">
                                <img v-if="peserta.foto_profil != null" :src="peserta.foto_profil" class="rounded-xl w-full h-full object-cover" />
                                <img v-else :src="profilDefault" class="rounded-xl w-full h-full object-cover" />
                            </div>
                            <div class="card-body py-1 px-5">
                                <div>
                                    <h1 class="font-bold text-xl"> {{ peserta.nomor_induk }} - {{ peserta.nama_peserta }}</h1>
                                </div>
                                <div class="flex gap-2">
                                    <table class="min-w-[600px]">
                                        <tbody>
                                            <tr>
                                                <td class="font-semibold">Institusi Pendidikan</td>
                                                <td>:</td>
                                                <td>{{ peserta.institusi?.nama_institusi }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-semibold">Lokasi Magang</td>
                                                <td>:</td>
                                                <td>{{ peserta.lokasi?.nama_lokasi }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-semibold">Periode Magang</td>
                                                <td>:</td>
                                                <td>{{ peserta.tanggal_mulai }} s/d {{ peserta.tanggal_selesai }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-semibold">Status</td>
                                                <td>:</td>
                                                <td>{{ peserta.status.nama_status }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script setup>
import { useRoute, useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import Breadcrumbs from '@/components/layouts/Breadcrumbs.vue';
import profilDefault from '@/assets/profil-default.png';
import Loading from '@/components/Loading.vue';

const AuthStore = useAuthStore();
const route = useRoute();
const router = useRouter();
const loading = ref(false);
const lokasi = ref({});
const allPeserta = ref([]);
const allMahasiswa = ref([]);
const allSiswa = ref([]);

const LokasiById = async () => {
    try {
        loading.value=true;
        const { data } = await customAPI.get(`/lokasi/${route.params.id}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });
        lokasi.value = data.data;
        allPeserta.value = data.data.list_peserta;
    } catch (error) {
        console.log("Data lokasi tidak ditemukan!");
    } finally {
        loading.value = false;
    }
}

const handleCardClick  = (pesertaId) => {
    router.push({ name:'DetailDataMagang', params: {id: pesertaId} });
}


onMounted(() => {
    LokasiById();
})
</script>