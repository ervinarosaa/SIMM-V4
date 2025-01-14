<template>
    <div class="mt-5 mx-5 lg:ml-10 max-w-[340px] sm:max-w-[660px] md:max-w-[750px] lg:max-w-full">
        <div class="w-full">
            <!-- Breadcrumbs -->
            <Breadcrumbs 
                class="hidden sm:flex sm:flex-col md:flex md:flex-col lg:flex lg:flex-col" 
                :title1="'Institusi Pendidikan'" 
                :route1="'Institusi Pendidikan'" 
                :title2="institusi.nama_institusi" 
                :route2="'DetailInstitusi'" 
                :id2="institusi.id" 
            />
        </div>
        <!-- Breadcrumbs untuk layar kecil -->
        <Breadcrumbs 
            class="flex flex-col sm:hidden md:hidden lg:hidden" 
            :title1="'Institusi Pendidikan'" 
            :route1="'Institusi Pendidikan'"  
            title2="..." 
            :route2="'DetailInstitusi'" 
            :id2="institusi.id" 
        />
        
        <div v-if="loading === true" class="my-4">
            <Loading :chart="true"/>
        </div>
        <div v-else class="card bg-base-200 border border-slate-300 shadow-lg my-4 p-2">
            <div class="grid lg:grid-cols-2 p-2 gap-12">
                <div class="flex gap-4">
                    <div class="flex flex-col gap-2">
                        <div>
                            <p class="text-lg font-bold">Institusi Pendidikan</p>
                            <p>{{ institusi.nama_institusi }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Tingkat Pendidikan</p>
                            <p>{{ institusi.tingkat_pendidikan?.nama_tingkat }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <section>
            <h1 class="text-3xl font-bold text-center pb-4">Peserta Magang</h1>
            <div v-if="loading === true">
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
const institusi = ref({});
const allPeserta = ref([]);

const InstitusiById = async () => {
    try {
        loading.value=true;
        const { data } = await customAPI.get(`/institusi/${route.params.id}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });
        institusi.value = data.data;
        allPeserta.value = data.data.list_peserta;
    } catch (error) {
        console.log("Data institusi tidak ditemukan!");
    } finally {
        loading.value = false;
    }
}

const handleCardClick  = (pesertaId) => {
    router.push({ name:'DetailDataMagang', params: {id: pesertaId} });
}

onMounted(() => {
    InstitusiById();
})
</script>