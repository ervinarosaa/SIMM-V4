<template>
    <div class="mt-5 mx-5 lg:ml-10">
        <!-- Breadcrumbs untuk layar besar -->
        <div v-if="AuthStore.user.role.nama_role !== 'Kepala Bagian'">
            <div class="w-full">
                <!-- Breadcrumbs -->
                <Breadcrumbs 
                    class="hidden sm:flex sm:flex-col md:flex md:flex-col lg:flex lg:flex-col" 
                    :title1="'Logbook Magang'" 
                    :route1="'Logbook Magang'" 
                    :title2="logbook.title" 
                    :route2="'DetailLogbookMagang'" 
                    :id2="logbook.id" 
                />
            </div>

            <!-- Breadcrumbs untuk layar kecil -->
            <Breadcrumbs 
                class="flex flex-col sm:hidden md:hidden lg:hidden" 
                :title1="'Logbook Magang'" 
                :route1="'Logbook Magang'" 
                title2="..." 
                :route2="'DetailLogbookMagang'" 
                :id2="logbook.id" 
            />
        </div>
        
        <div v-else>
            <div class="w-full">
                <!-- Breadcrumbs -->
                <Breadcrumbs 
                    class="hidden sm:flex sm:flex-col md:flex md:flex-col lg:flex lg:flex-col" 
                    :title1="'Data Peserta Aktif'" 
                    :route1="'Data Magang'" 
                    :title2="logbook.title" 
                    :route2="'DetailLogbookMagang'" 
                    :id2="logbook.id" 
                />
            </div>

            <!-- Breadcrumbs untuk layar kecil -->
            <Breadcrumbs 
                class="flex flex-col sm:hidden md:hidden lg:hidden" 
                :title1="'Data Peserta Aktif'" 
                :route1="'Data Magang'" 
                title2="..." 
                :route2="'DetailLogbookMagang'" 
                :id2="logbook.id" 
            />
        </div>

        <div v-if="loading === true" class="my-5">
            <Loading :all="true"/>
        </div>

        <div v-else class="bg-base-200 rounded-xl my-5 p-4">
            <h1 v-if="logbook.tanggal_logbook" class="text-xl font-bold">{{ logbook.title }} - {{ formatDate(logbook.tanggal_logbook) }}</h1>
            <div class="flex flex-col md:flex-row lg:flex-row lg:items-center md:gap-3 lg:gap-3 text-sm">
                <p v-if="logbook.created_at">
                    <span class="pi pi-clock"></span> {{ formatTimestap(logbook.created_at) }}
                </p>
                <p v-if="logbook.peserta?.nama_peserta">
                    <span class="pi pi-user"></span> {{ logbook.peserta.nama_peserta }}
                </p>
                <p v-if="logbook.peserta?.lokasi?.nama_lokasi">
                    <span class="pi pi-map-marker"></span> {{ logbook.peserta.lokasi.nama_lokasi }}
                </p>
            </div>

            <hr class="my-2">
            <p class="text-sm leading-relaxed text-justify my-2">
                {{ logbook.deskripsi }}
            </p>
            <div class="flex justify-center items-center mt-3">
                <img :src="logbook.dokumentasi" alt="Dokumentasi Kegiatan" class="w-[500px] rounded-xl overflow-hidden ">
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { onMounted, ref } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';
import Breadcrumbs from '@/components/layouts/Breadcrumbs.vue';
import Loading from '@/components/Loading.vue';

const AuthStore = useAuthStore();
const route = useRoute();
const loading = ref(false);
const logbook = ref({}); 

const LogbookById = async () => {
    try {
        loading.value=true;
        const { data } = await customAPI.get(`/logbook/${route.params.id}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });
        logbook.value = data.data;
    } catch (error) {
        console.error("Error fetching logbook data:", error);
    } finally {
        loading.value = false;
    }
}

const formatTimestap = (date) => {
    return format(new Date(date), 'dd MMMM yyyy HH:mm:ss', { locale: id });
};

const formatDate = (date) => {
    return format(new Date(date), 'dd/MM/yyyy');
};

onMounted(() => {
    LogbookById();
});
</script>
