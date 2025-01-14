<template>
    <div class="mt-5 mx-5 lg:ml-10  md:max-w-[700px] lg:max-w-full">
        <Breadcrumbs 
            :title1="arsipMode ? 'Arsip Data Magang' : 'Data Peserta Aktif'" 
            :route1="arsipMode ? 'Arsip Data Magang' : 'Data Magang'" 
            :title2="peserta.nama_peserta" 
            :route2="arsipMode ? 'DetailArsipMagang' : 'DetailDataMagang'" 
            :id2=" peserta.id"
        />

        <div v-if="loading === true" class="my-4">
            <Loading :all="true"/>
        </div>
        <div v-else class="card bg-base-200 border border-slate-300 shadow-lg my-4 p-2">
            <div class="grid md:grid-cols-2 lg:grid-cols-2 p-2 gap-12">
                <div class="flex flex-col md:flex-row lg:flex-row gap-4">
                    <div class="md:h-48 lg:w-48 lg:h-full aspect-[3/4] rounded-xl overflow-hidden">
                        <img v-if="peserta.foto_profil != null" :src="peserta.foto_profil" class="rounded-xl w-full h-full object-cover" />
                        <img v-else :src="profilDefault" class="rounded-xl w-full h-full object-cover" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <div>
                            <p class="text-lg font-bold">Nama</p>
                            <p>{{ peserta.nama_peserta }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Nomor Induk</p>
                            <p>{{ peserta.nomor_induk }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Lokasi Magang</p>
                            <p>{{ peserta.lokasi?.nama_lokasi }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Periode Magang</p>
                            <p>{{ peserta.tanggal_mulai }} s/d {{ peserta.tanggal_selesai }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2 px-1">
                    <div>
                        <p class="text-lg font-bold">Nomor Telepon</p>
                        <p>{{ peserta.nomor_telepon }}</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Email</p>
                        <p>{{ peserta.user?.email }}</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Alamat</p>
                        <p>{{ peserta.alamat_peserta }}</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Status Magang</p>
                        <p>{{ peserta.status?.nama_status }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="loading === true" class="my-5">
            <Loading :text="true"/>
        </div>
        <div v-else class="flex flex-col">
            <div class="grid md:grid-cols-2 lg:grid-cols-2 md:gap-5 lg:gap-5">
                <div class="card bg-base-200 border border-slate-300 shadow-lg my-4 p-4">
                    <div class="flex flex-col gap-2">
                        <div>
                            <p class="text-lg font-bold">Institusi Pendidikan Asal</p>
                            <p>{{ peserta.institusi?.nama_institusi }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Fakultas</p>
                            <p>{{ peserta.fakultas }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Jurusan</p>
                            <p>{{ peserta.jurusan }}</p>
                        </div>
                    </div>
                </div>
                <div class="card bg-base-200 border border-slate-300 shadow-lg my-4 p-4">
                    <div class="flex flex-col gap-2">
                        <div>
                            <p class="text-lg font-bold">Dosen Pembimbing</p>
                            <p>{{ peserta.nama_pembimbing }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Kontak Dosen Pembimbing</p>
                            <p>{{ peserta.kontak_pembimbing }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-2 md:gap-5 lg:gap-5">
                <div v-if="arsipMode" class="card bg-base-200 border border-slate-300 shadow-lg my-4 p-4">
                    <div class="flex flex-col gap-2">
                        <div>
                            <p class="text-lg font-bold">Nomor Sertifikat</p>
                            <p v-if="peserta.sertifikat">{{ peserta.sertifikat?.nomor_sertifikat }}</p>
                            <p v-else>Belum tersedia</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Predikat Nilai Magang</p>
                            <p v-if="peserta.nilai">{{ peserta.nilai?.predikat_nilai }}</p>
                            <p v-else>-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <section>
            <h1 class="text-3xl font-bold text-center pb-4">Logbook Magang</h1>
            
            <div v-if="loading === true">
                <Loading :chart="true"/>
            </div>
            <div v-else class="bg-base-200 py-2 rounded-lg max-h-[600px] min-h-[50px] overflow-hidden overflow-y-auto w-full">
                <div v-if="detailLogbook == ''" class="text-center">
                    <h1 class="mx-2 my-1 px-2 py-2 font-semibold">Logbook belum tersedia</h1>
                </div>
                <div v-else>
                    <div v-for="logbook in detailLogbook" :key="logbook.id">
                        <div 
                            @click="handleCardClickLogbook(logbook.id)"
                            class="card bg-base-100 border mx-2 my-1 px-2 py-2 rounded-lg"
                        >
                            <div class="card-body p-2">
                                <div class="flex flex-col gap-1">
                                    <h2 v-if="logbook.title.length >39" class="text-lg font-semibold">
                                        {{ logbook.title.substring(0,38) }} ... ({{ logbook.tanggal_logbook }})
                                    </h2>
                                    <h2 v-else class="text-lg font-semibold">
                                        {{ logbook.title }} ({{ logbook.tanggal_logbook }})
                                    </h2>
                                    <div class="flex">
                                        <p class="text-xs w-0">
                                            <span class="pi pi-clock ml-1"></span> {{ formatDate(logbook.created_at) }}
                                        </p>
                                    </div>
                                    
                                </div>

                                <hr>

                                <p v-if="logbook.deskripsi.length >300" class="text-sm leading-relaxed text-justify">
                                    {{ logbook.deskripsi.substring(0,300) }} ...
                                </p>
                                <p v-else class="text-sm leading-relaxed text-justify">
                                    {{ logbook.deskripsi }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="divider"></div>

        <section>
            <h1 class="text-3xl font-bold text-center pb-4">Presensi Magang</h1>
            
            <div v-if="loading === true">
                <Loading :chart="true"/>
            </div>
            <div v-else class="bg-base-200 py-2 px-2 rounded-lg">
                <div v-if="detailPresensi == ''" class="text-center">
                    <h1 class="mx-2 my-1 px-2 py-2 font-semibold">Presensi belum tersedia</h1>
                </div>
                <div v-else>
                    <div class="overflow-x-auto">
                        <div class="max-h-[600px] min-h-[100px] rounded-lg overflow-y-auto">
                            <table class="bg-base-100 table table-xs table-pin-rows">
                                <thead>
                                    <tr class="text-center text-lg">
                                        <td>Tanggal</td>
                                        <td>Keterangan</td>
                                        <td>Lokasi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="presensi in detailPresensi" :key="presensi.id" class="text-center">
                                        <td class="text-lg">{{ formatPresensiDate(presensi.created_at) }}</td>
                                        <td class="w-[150px] text-lg">{{ presensi.keterangan.nama_keterangan }}</td>
                                        <td class="min-w-[250px]">
                                            <div class="lg:max-w-6xl lg:mx-auto p-4">
                                                <div class="flex justify-center">
                                                    <iframe 
                                                        v-if="presensi.latitude && presensi.longitude" 
                                                        :src="`https://www.google.com/maps?q=${presensi.latitude},${presensi.longitude}&hl=es;z=14&output=embed`" 
                                                        width="80%" 
                                                        height="175" 
                                                        style="border:0;" 
                                                        allowfullscreen 
                                                        loading="lazy"
                                                    ></iframe>
                                                    <div v-else>
                                                        <p>Koordinat tidak valid</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="divider"></div>
        
        <section>
            <h1 class="text-3xl font-bold text-center pb-4">Administrasi Magang</h1>
            <div v-if="loading === true">
                <Loading :chart="true"/>
            </div>
            <div v-else>
                <div v-if="detailDokumen == ''" class="text-center">
                    <div class="bg-base-200 py-2 rounded-lg max-h-[600px] min-h-[50px] overflow-hidden overflow-y-auto w-full">
                        <h1 class="mx-2 my-1 px-2 py-2 font-semibold">Dokumen belum tersedia</h1>
                    </div>
                </div>
                <div v-else>
                    <div class="grid sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="dokumen in detailDokumen" :key="dokumen.id">
                            <div class="card bg-base-200 border border-slate-300 shadow-lg mt-4 p-4">
                                <div class="flex flex-col gap-2 items-center">
                                    <p class="text-lg font-bold">{{ dokumen.jenis.nama_jenis }}</p>
                                    <a :href="dokumen.file" target="_blank" rel="noopener noreferrer" 
                                        class="btn btn-sm btn-primary text-white px-5"> 
                                        View
                                    </a>
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
import { format } from 'date-fns';
import Breadcrumbs from '@/components/layouts/Breadcrumbs.vue';
import profilDefault from '@/assets/profil-default.png';
import Loading from '../Loading.vue';

const AuthStore = useAuthStore();
const route = useRoute();
const router = useRouter();
const loading = ref(false);
const peserta = ref("");
const detailInstitusi = ref("");
const detailLogbook = ref("");
const detailPresensi = ref("");
const detailDokumen = ref("");

const props = defineProps({
    arsipMode: {
        type: Boolean,
        required: true,
    }
});

const PesertaById = async () => {
    try {
        loading.value=true;
        const { data } = await customAPI.get(`/peserta/${route.params.id}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });
        peserta.value = data.data;
        detailInstitusi.value = data.data.institusi;
        detailLogbook.value = data.data.logbook;
        detailPresensi.value = data.data.presensi;
        detailDokumen.value = data.data.dokumen;
    } catch (error) {
        console.log("Data peserta tidak ditemukan.", error);
    } finally {
        loading.value = false;
    }
};

const formatDate = (date) => {
    return format(new Date(date), 'dd-MM-yyyy HH:mm:ss');
};

const formatPresensiDate = (date) => {
    return format(new Date(date), 'dd-MM-yyyy');
};

const handleCardClickLogbook  = (LogbookID) => {
    router.push({ name:'DetailLogbookMagang', params: {id: LogbookID} });
};

onMounted(() => {
    PesertaById();
});
</script>