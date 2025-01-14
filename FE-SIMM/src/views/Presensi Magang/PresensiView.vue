<template>
    <div class="mt-5 mx-5 lg:ml-10 max-w-[340px] sm:max-w-[660px] md:max-w-[750px] lg:max-w-full">
        <h1 class="text-2xl lg:text-3xl font-bold text-center mb-4 pb-4">Presensi Magang</h1>

        <div class="flex flex-col lg:flex-row  gap-2 lg:items-center">
            <div class="flex gap-2">
                <button class="btn btn-sm btn-primary text-white" @click="openModalAdd()">
                    <span class="pi pi-id-card"></span> Tambah
                </button>

                <div class="modal" :class="{ 'modal-open': showModalAdd }">
                    <div class="modal-box">
                        <DialogTambahPresensi @closeModal="closeModal" @saved="handleSaved" />
                    </div>
                </div>

                <!-- Export Logbook For Admin -->
                <button class="btn btn-sm btn-primary text-white px-5" @click="openModalExport()" v-if="AuthStore.user && AuthStore.user.role?.nama_role === 'Admin'">
                    <span class="pi pi-file-excel"></span> Export
                </button>

                <div class="modal" :class="{ 'modal-open': showModalExport }">
                    <div class="modal-box">
                        <DialogEksporPresensi @closeModal="closeModalExport" :logbookMode="false" @saved="handleSavedExport" />
                    </div>
                </div>

                <!-- Export Logbook For Peserta -->
            <button 
                class="btn btn-sm btn-primary text-white px-5" 
                @click="openModalPeserta()" 
                v-if="AuthStore.user && AuthStore.user.role?.nama_role === 'Peserta'">
                <span class="pi pi-file-excel"></span> Export
            </button>

            <div class="modal" :class="{ 'modal-open': showModalPeserta }">
                <div class="modal-box">
                    <ExportFromPeserta @closeModal="closeModalPeserta" :logbookMode="false" :peserta="selectedPeserta" @saved="handleSavedPeserta" />
                </div>
            </div>
            </div>
            
            <div class="flex flex-col md:flex-row lg:flex-row gap-2 ">
                <!-- Search -->
                <label class="input input-bordered flex items-center h-[33px] lg:w-full" 
                    v-if="AuthStore.user.role?.nama_role !== 'Peserta'">
                    <input type="text" v-model="searchQuery" placeholder="Cari Presensi..." class="input input-sm w-full justify-between" />
                    <span class="pi pi-search"></span>
                </label>

                <!-- Date Filter -->
                <label class="input input-bordered flex items-center h-[33px] lg:w-full">
                    <input type="date" v-model="selectedDate" class="input input-sm w-full" />
                </label>
            </div>
        </div>

        <div v-if="loading === true" class="my-5">
            <Loading :all="true"/>
        </div>
        <div v-else class="my-5">
            <div class="bg-base-200 py-2 px-2 md:max-w-[500px] lg:max-w-full rounded-lg overflow-x-auto">
                <div class="max-h-[800px] rounded-lg overflow-y-auto">
                    <table class="bg-base-100 table table-auto table-xs table-pin-rows">
                        <thead>
                            <tr class="text-center text-lg">
                                <td>TANGGAL</td>
                                <td>DATA PRESENSI</td>
                                <td>GEOLOKASI</td>
                                <td v-if="AuthStore.user && AuthStore.user.role?.nama_role === 'Admin'">
                                    AKSI
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="presensi in paginatedData" :key="presensi.id" class="text-center">
                                <td class="w-[150px] text-sm">
                                    <div class="flex flex-col">
                                        <p>{{ formatTimestap(presensi.tanggal_presensi) }}</p>
                                        <p>[{{ presensi.keterangan.nama_keterangan }}]</p>
                                    </div>
                                </td>
                                <td class="lg:w-[400px] text-sm">
                                    <div class="flex flex-col items-start text-start">
                                        <p><span class="pi pi-user pr-2"></span>{{ presensi.peserta.nama_peserta }}</p>
                                        <p><span class="pi pi-building pr-2"></span>{{ presensi.peserta.institusi.nama_institusi }}</p>
                                        <p><span class="pi pi-map pr-2"></span>{{ presensi.peserta.lokasi.nama_lokasi }}</p>
                                    </div>
                                </td>
                                <td class="text-sm min-w-[200px]">
                                    <div class="flex justify-center">
                                        <iframe 
                                            v-if="presensi.latitude && presensi.longitude" 
                                            :src="`https://www.google.com/maps?q=${presensi.latitude},${presensi.longitude}&hl=es;z=14&output=embed`" 
                                            width="80%" 
                                            height="175" 
                                            style="border:0;" 
                                            allowfullscreen 
                                        ></iframe>
                                        <div v-else>
                                            <p>Koordinat tidak valid</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="lg:w-[90px]"
                                    v-if="AuthStore.user && AuthStore.user.role?.nama_role === 'Admin'"
                                >
                                    <div class="tooltip tooltip-bottom" data-tip="Hapus">
                                        <button 
                                            @click="handleDeletePresensi(presensi.id)" 
                                            class="btn btn-sm btn-error text-white py-2 px-4 rounded"
                                        >
                                            <span class="pi pi-trash"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-center my-3">
                    <div class="join">
                        <!-- Radio Button untuk Halaman -->
                        <input
                            v-for="page in paginatedPages"
                            :key="page"
                            class="join-item btn btn-square"
                            type="radio"
                            :name="'page-' + page"
                            :aria-label="page.toString()"
                            :checked="currentPage === page"
                            @change="goToPage(page)"
                            :value="page"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <DeleteConfirm 
            v-if="showDeleteDialog" 
            :presensi="PresensiToDelete"
            @confirmDelete="confirmDelete"
            @cancelDelete="showDeleteDialog = false"
        />
        
        <!-- Success and Failed Alert -->
        <SuccessAlert v-if="isSuccess" :message="successMessage" @alertClosed="isSuccess = false" />
        <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false"  />
        <WarningAlert v-if="isWarning" :message="warningMessage" @alertClosed="isWarning = false"  />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/AuthStore';
import { customAPI } from '@/api';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';
import Loading from '@/components/Loading.vue';
import DialogEksporPresensi from '@/components/Dialog/DialogEksporByKategori.vue';
import DialogTambahPresensi from '@/components/Dialog/DialogPresensi.vue';
import ExportFromPeserta from '@/components/ExportFromPeserta.vue';
import DeleteConfirm from '@/components/Alerts/DeleteConfirm.vue'; 
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';
import WarningAlert from '@/components/Alerts/WarningAlert.vue';

// State untuk mengontrol alert
const showDeleteDialog = ref(false);
const PresensiToDelete = ref(null);
const isSuccess = ref(false);
const isFailed = ref(false);
const isWarning = ref(false);
const successMessage = ref('');
const failedMessage = ref('');
const warningMessage = ref('');

const AuthStore = useAuthStore();
const loading = ref(false);
const searchQuery = ref('');
const selectedDate = ref(null);
const allPresensi = ref([]);
const peserta = ref(null);

const FetchPresensi = async () => {
    try {
        loading.value=true;
        if (AuthStore.user?.role?.nama_role === "Admin"){
            const { data } = await customAPI.get('/presensi', {
                headers: { Authorization: `Bearer ${AuthStore.token}` }
            });
            allPresensi.value = data.data; 
        } 
        else {
            const { data: pesertaData } = await customAPI.get('/me', {
                headers: { Authorization: `Bearer ${AuthStore.token}`},
            });

            peserta.value = pesertaData.user.peserta;

            const peserta_id = pesertaData.user.peserta.id;
            const { data } = await customAPI.get(`/presensi/peserta/${peserta_id}`, {
                headers: { Authorization: `Bearer ${AuthStore.token}`}
            });
            allPresensi.value = data.data; 
        }
        
        allPresensi.value.sort((a, b) => new Date(b.tanggal_presensi) - new Date(a.tanggal_presensi));
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
};

const filteredPresensi = computed(() => {
    return allPresensi.value.filter(presensi => {
        // Filter berdasarkan pencarian
        const matchesSearchQuery = searchQuery.value === '' ||
            presensi.peserta.nama_peserta.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            presensi.peserta.institusi.nama_institusi.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            presensi.peserta.lokasi.nama_lokasi.toLowerCase().includes(searchQuery.value.toLowerCase())||
            presensi.keterangan.nama_keterangan.toLowerCase().includes(searchQuery.value.toLowerCase());

        // Filter berdasarkan tanggal
        const matchesDate = !selectedDate.value ||
            format(new Date(presensi.tanggal_presensi), 'yyyy-MM-dd') === selectedDate.value;

        return matchesSearchQuery && matchesDate;
    });
});

const handleDeletePresensi = async (paramsId) => {
    PresensiToDelete.value = paramsId;
    showDeleteDialog.value = true;
}

const confirmDelete = async () => {
    if (PresensiToDelete.value) {
        const paramsId = PresensiToDelete.value;
        
        try {
            await customAPI.post(`/presensi/${paramsId}?_method=DELETE`, null,
            {
                headers: { Authorization: `Bearer ${AuthStore.token}` },
            })
                
            isSuccess.value = true;
            successMessage.value = 'Presensi berhasil dihapus!';
            FetchPresensi();
        } catch (error) {
            console.error("Terjadi kesalahan saat menghapus lokasi:", error);
            isFailed.value = true;
            failedMessage.value = 'Gagal menghapus data. Silahkan coba lagi!';
        } finally {
            showDeleteDialog.value = false;
        }
    }
};

const formatTimestap = (date) => {
    return format(new Date(date), 'dd MMMM yyyy HH:mm:ss', { locale: id });
};

// Dialog Export
const showModalExport = ref(false);

const openModalExport = () => {
    showModalExport.value = true;
};

const closeModalExport = () => {
    showModalExport.value = false;
};

const handleSavedExport = () => {
    isSuccess.value = true;
    successMessage.value = 'Presensi berhasil diekspor!';
    FetchPresensi();
    closeModalExport(); 
};
// End Dialog Export

// Dialog Tambah
const showModalAdd = ref(false);

const openModalAdd = () => {
    try {
        if (AuthStore.user.role?.nama_role === "Peserta") {
            const today = new Date().toLocaleDateString('en-CA'); // Format YYYY-MM-DD
            const hasPresensiToday = allPresensi.value.some(
                (presensi) => new Date(presensi.tanggal_presensi).toLocaleDateString('en-CA') === today
            );

            if (hasPresensiToday) {
                isWarning.value = true;
                warningMessage.value = 'Anda sudah melakukan presensi hari ini!';
                return;
            }

            showModalAdd.value = true;
        } else {
            showModalAdd.value = true;
        }
    } catch (error) {
        console.error("Error saat memeriksa presensi:", error);
        isFailed.value = true;
        failedMessage.value = 'Terjadi kesalahan. Silahkan coba lagi!';
    }

};

const closeModal = () => {
    showModalAdd.value = false;
};

const handleSaved = () => {
    isSuccess.value = true;
    successMessage.value = 'Presensi berhasil disimpan!';
    FetchPresensi();
    closeModal(); 
};
// End Dialog Tambah

// Dialog Ekspor From Peserta
const showModalPeserta = ref(false);
const selectedPeserta= ref(null);
const openModalPeserta = () => {
    if (peserta.value) {
        showModalPeserta.value = true;
        selectedPeserta.value = { 
            id: peserta.value.id, 
            nama_peserta: peserta.value.nama_peserta, 
            nomor_induk: peserta.value.nomor_induk,
        };
    }
};

const closeModalPeserta = () => {
    showModalPeserta.value = false;
    selectedPeserta.value = null;
};

const handleSavedPeserta = () => {
    isSuccess.value = true;
    successMessage.value = 'Presensi berhasil diekspor!';
    FetchPresensi();
    closeModalPeserta(); 
};
// End Dialog Ekspor

// Paginasi
const currentPage = ref(1);
const itemsPerPage = 20;

const totalPages = computed(() => Math.ceil(filteredPresensi.value.length / itemsPerPage));

const paginatedPages = computed(() => {
    const pages = [];
    for (let i = 1; i <= totalPages.value; i++) {
        pages.push(i);
    }
    return pages;
});

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredPresensi.value.slice(start, end);
});

const goToPage = (page) => {
    currentPage.value = page;
};

onMounted(() => {
    FetchPresensi();
});
</script>