<template>
    <div class="mt-5 mx-5 lg:ml-10">
        <h1 class="text-2xl lg:text-3xl font-bold text-center mb-4 pb-4">Logbook Magang</h1>

        <div class="flex flex-col lg:flex-row gap-2 lg:items-center">
            <!-- Add Logbook For Peserta -->
            <button 
                class="btn btn-sm btn-primary text-white" 
                @click="openModalAdd()"
                v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Peserta'">
                <span class="pi pi-book"></span> Tambah
            </button>

            <div class="modal" :class="{ 'modal-open': showModalAdd }">
                <div class="modal-box">
                    <DialogLogbook @closeModal="closeModalAdd" :editMode="editMode" :logbook="selectedLogbook" @saved="handleSaved" />
                </div>
            </div>

            <!-- Export Logbook For Admin -->
            <button 
                class="btn btn-sm btn-primary text-white px-5" 
                @click="openModal()" 
                v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Admin'">
                <span class="pi pi-file-excel"></span> Export
            </button>

            <div class="modal" :class="{ 'modal-open': showModal }">
                <div class="modal-box">
                    <DialogEksporLogbook @closeModal="closeModal" :logbookMode="true" @saved="handleSavedExport" />
                </div>
            </div>

            <!-- Export Logbook For Peserta -->
            <button 
                class="btn btn-sm btn-primary text-white px-5" 
                @click="openModalPeserta()" 
                v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Peserta'">
                <span class="pi pi-file-excel"></span> Export
            </button>

            <div class="modal" :class="{ 'modal-open': showModalPeserta }">
                <div class="modal-box">
                    <ExportFromPeserta @closeModal="closeModalPeserta" :logbookMode="true" :peserta="selectedPeserta" @saved="handleSavedPeserta" />
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-2">
                <!-- Search -->
                <label class="input input-bordered flex items-center justify-between h-[33px]">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Cari Logbook..."
                        class="input input-sm w-full"
                    />
                    <span class="pi pi-search"></span>
                </label>

                <!-- Date Filter -->
                <label class="input input-bordered flex items-center h-[33px]">
                    <input
                        type="date"
                        v-model="selectedDate"
                        class="input input-sm w-full"
                    />
                </label>
            </div>
        </div>

        <div v-if="loading === true" class="my-5">
            <Loading :all="true"/>
        </div>
        <div v-else class="my-5" v-for="logbook in paginatedData" :key="logbook.id">
            <div 
                @click="handleCardClickLogbook(logbook.id)"
                class="card bg-base-200 border border-slate-300 shadow-lg"
            >
                <div class="card-body p-4">
                    <div class="flex flex-col gap-1">
                        <div class="flex flex-col-reverse lg:flex-row justify-between">
                            <h1 v-if="logbook.peserta?.nama_peserta.length >39" class="text-xl font-bold">
                                {{ logbook.peserta.nama_peserta.substring(0,38) }} ... - ({{ logbook.peserta.lokasi.nama_lokasi }})
                            </h1>
                            <h1 v-else class="text-xl font-bold">
                                {{ logbook.peserta?.nama_peserta }} - {{ logbook.peserta?.lokasi?.nama_lokasi }}
                            </h1>
                            <div class="flex flex-row gap-2 lg:justify-center items-center" v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Peserta'">
                                <!-- View Detail Logbook -->
                                <div class="tooltip tooltip-bottom" data-tip="Detail">
                                    <RouterLink 
                                        :to="{ name: 'DetailLogbookMagang', params: {id: logbook.id}}"
                                        class="btn btn-sm btn-info text-white py-2 px-4 rounded"
                                    >
                                        <span class="pi pi-eye"></span>
                                    </RouterLink>
                                </div>
                                
                                <!-- Edit  Logbook-->
                                <div class="tooltip tooltip-bottom" data-tip="Edit">
                                    <button 
                                        @click="openEditModal(logbook)" 
                                        class="btn btn-sm btn-warning text-white py-2 px-4 rounded " 
                                    >
                                        <span class="pi pi-pencil"></span>
                                    </button>
                                </div>
                                
                                <!-- Delete Logbook -->
                                <div class="tooltip tooltip-bottom" data-tip="Hapus">
                                    <button 
                                        @click="handleDelete(logbook.id)" 
                                        class="btn btn-sm btn-error text-white py-2 px-4 rounded"
                                    >
                                        <span class="pi pi-trash"></span>
                                    </button>
                                </div>
                                
                            </div>
                        </div>
                        
                        <div class="flex flex-col lg:flex-row justify-start text-sm gap-2">
                            <h1>
                                <span class="pi pi-clock"></span> {{ formatTimestap(logbook.created_at) }}
                            </h1>
                            <h1>
                                <span class="pi pi-building"></span> {{ logbook.peserta?.institusi?.nama_institusi }}
                            </h1>
                        </div>
                    </div>

                    <hr>
                    <h1 class="font-semibold">[{{ formatDate(logbook.tanggal_logbook) }} - {{ logbook.title }}]</h1>

                    <p v-if="logbook.deskripsi.length >300" class="text-sm leading-relaxed text-justify">
                        {{ logbook.deskripsi.substring(0,300) }} ...
                    </p>
                    <p v-else class="text-sm leading-relaxed text-justify">
                        {{ logbook.deskripsi }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center">
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

        <!-- Delete Confirmation Dialog -->
        <DeleteConfirm 
            v-if="showDeleteDialog" 
            :logbook="LogbookToDelete"
            @confirmDelete="confirmDelete"
            @cancelDelete="showDeleteDialog = false"
        />
        
        <!-- Success and Failed Alert -->
        <SuccessAlert v-if="isSuccess" :message="successMessage" @alertClosed="isSuccess = false" />
        <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false"  />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/AuthStore';
import { customAPI } from '@/api';
import { useRouter } from 'vue-router';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';
import Loading from '@/components/Loading.vue';
import DialogEksporLogbook from '@/components/Dialog/DialogEksporByKategori.vue';
import DialogLogbook from '@/components/Dialog/DialogLogbook.vue';
import ExportFromPeserta from '@/components/ExportFromPeserta.vue';
import DeleteConfirm from '@/components/Alerts/DeleteConfirm.vue'; 
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';

// State untuk mengontrol alert
const showDeleteDialog = ref(false);
const LogbookToDelete = ref(null);
const isSuccess = ref(false);
const isFailed = ref(false);
const successMessage = ref('');
const failedMessage = ref('');

const AuthStore = useAuthStore();
const router = useRouter();
const loading = ref(false);
const searchQuery = ref('');
const selectedDate = ref(null);
const allLogbook = ref([]);
const peserta = ref(null);

const FetchLogbook = async () => {
    try {
        loading.value=true;
        if (AuthStore.user.role.nama_role === "Admin") {
            const { data } = await customAPI.get('/logbook', {
                headers: { Authorization: `Bearer ${AuthStore.token}` }
            });
            allLogbook.value = data.data; 
        } else {
            const { data: pesertaData } = await customAPI.get('/me', {
                headers: { Authorization: `Bearer ${AuthStore.token}`},
            });

            peserta.value = pesertaData.user.peserta;
            
            const peserta_id =  pesertaData.user.peserta.id;
            const { data } = await customAPI.get(`/logbook/peserta/${peserta_id}`, {
                headers: { Authorization: `Bearer ${AuthStore.token}` }
            });
            allLogbook.value = data.data; 
        }
        allLogbook.value.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
};

const filteredLogbook = computed(() => {
    return allLogbook.value.filter(logbook => {
        // Filter berdasarkan pencarian
        const matchesSearchQuery = searchQuery.value === '' ||
            logbook.peserta.nama_peserta.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            logbook.peserta.institusi.nama_institusi.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            logbook.peserta.lokasi.nama_lokasi.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            logbook.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            logbook.deskripsi.toLowerCase().includes(searchQuery.value.toLowerCase());

        // Filter berdasarkan tanggal
        const formattedDate = selectedDate.value;
        const logbookDate = new Date(logbook.tanggal_logbook).toISOString().split('T')[0];
        const matchesDate = !formattedDate || logbookDate === formattedDate;

        return matchesSearchQuery && matchesDate;
    });
});

const formatTimestap = (date) => {
    return format(new Date(date), 'dd MMMM yyyy HH:mm:ss', { locale: id });
};

const formatDate = (date) => {
    return format(new Date(date), 'dd/MM/yyyy');
};

const handleCardClickLogbook  = (LogbookID) => {
    if (!AuthStore.user || AuthStore.user.role.nama_role === 'Admin') {
        router.push({ name:'DetailLogbookMagang', params: {id: LogbookID} });
    }
}

// Dialog Ekspor
const showModal = ref(false);

const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const handleSavedExport = () => {
    isSuccess.value = true;
    successMessage.value = 'Logbook berhasil diekspor!';
    FetchLogbook();
    closeModal(); 
};
// End Dialog Ekspor

// Dialog Tambah & Edit 
const showModalAdd = ref(false);
const editMode = ref(false);
const selectedLogbook = ref(null);

const openModalAdd = () => {
    selectedLogbook.value = null;
    showModalAdd.value = true;
    editMode.value = false;
};

const openEditModal = (LogbookData) => {
    selectedLogbook.value = LogbookData;
    showModalAdd.value = true;
    editMode.value = true;
};

const closeModalAdd = () => {
    showModalAdd.value = false;
    selectedLogbook.value = null;
    editMode.value = false;
};

const handleSaved = () => {
    isSuccess.value = true;
    successMessage.value = 'Logbook berhasil disimpan!!';
    FetchLogbook();
    closeModalAdd();
};
// End Dialoh Tambah & Edit

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
    successMessage.value = 'Logbook berhasil diekspor!';
    FetchLogbook();
    closeModalPeserta(); 
};
// End Dialog Ekspor

const handleDelete = async (paramsId) => {
    LogbookToDelete.value = paramsId;
    showDeleteDialog.value = true;
}

const confirmDelete = async () => {
    if (LogbookToDelete.value) {
        const paramsId = LogbookToDelete.value;
        
        try {
            await customAPI.post(`/logbook/${paramsId}?_method=DELETE`, null,
            {
                headers: { Authorization: `Bearer ${AuthStore.token}` },
            })
                
            isSuccess.value = true;
            successMessage.value = 'Logbook Magang berhasil dihapus!';
            FetchLogbook();
        } catch (error) {
            console.error("Terjadi kesalahan saat menghapus lokasi:", error);
            isFailed.value = true;
            failedMessage.value = 'Gagal menghapus data. Silahkan coba lagi!';
        } finally {
            showDeleteDialog.value = false;
        }
    }
};

// Paginasi
const currentPage = ref(1);
const itemsPerPage = 20;

const totalPages = computed(() => Math.ceil(filteredLogbook.value.length / itemsPerPage));

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
    return filteredLogbook.value.slice(start, end);
});

const goToPage = (page) => {
    currentPage.value = page;
};

onMounted(() => {
    FetchLogbook();
});
</script>