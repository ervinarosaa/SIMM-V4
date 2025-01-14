<template>
    <div class="mt-5 mx-5 lg:ml-10 max-w-[340px] sm:max-w-[660px] md:max-w-[750px] lg:max-w-full">
        <h1 class="text-2xl lg:text-3xl font-bold text-center mb-4 pb-4">Lokasi Magang</h1>

        <div class="flex flex-col lg:flex-row gap-2 lg:items-center">
            <!-- Add Modal -->
            <button 
                v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Admin'"
                class="btn btn-sm btn-primary text-white" 
                @click="openModal()">
                <span class="pi pi-map"></span> Tambah
            </button>

            <div class="modal" :class="{ 'modal-open': showModal }">
                <div class="modal-box">
                    <DialogLokasi @closeModal="closeModal" :editMode="editMode" :lokasi="selectedLokasi" @saved="handleSaved" />
                </div>
            </div>

            <!-- Search -->
            <label class="input input-bordered flex items-center justify-between h-[33px]">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Cari nama lokasi..."
                    class="input input-sm w-full"
                />
                <span class="pi pi-search"></span>
            </label>
        </div>

        <div v-if="loading === true" class="my-4">
                <Loading :all="true"/>
        </div>
        <div v-else class="my-4">
            <div class="bg-base-200 py-2 px-2 max-w-full rounded-lg overflow-x-auto">
                <div class="max-h-[800px] rounded-lg overflow-y-auto">
                    <table class="bg-base-100 table table-auto table-xs table-pin-rows">
                        <thead>
                            <tr class="text-center text-lg">
                                <td>NO</td>
                                <td>LOKASI MAGANG</td>
                                <td>KEPALA BAGIAN</td>
                                <td>TOTAL KUOTA</td>
                                <td>KUOTA TERISI</td>
                                <td>AKSI</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(lokasi, index) in filteredLokasi" :key="lokasi.id">
                                <th>{{ index + 1 }}</th>
                                <td class="w-[300px] text-lg">{{ lokasi.nama_lokasi }}</td>
                                <td class="text-lg text-center" v-if="lokasi.kepala_bagian !== null">{{ lokasi.kepala_bagian.nama_kabag }}</td>
                                <td class="text-lg text-center" v-else>-</td>
                                <td class="w-[150px] text-lg text-center">{{ lokasi.kuota_magang }}</td>
                                <td class="w-[150px] text-lg text-center">{{ lokasi.kuota_terisi }}</td>
                                <td>
                                    <div class="flex flex-row gap-2 justify-center items-center">
                                        <!-- Create or Update Kabag -->
                                        <div class="tooltip tooltip-bottom" data-tip="Kepala Bagian"
                                            v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Admin'">
                                            <button 
                                                @click="openModalKabag(lokasi)" 
                                                class="btn btn-sm btn-active text-white py-2 px-4 rounded " 
                                            >
                                                <span class="pi pi-user"></span>
                                            </button>
                                        </div>
                                        
                                        <!-- View Detail Lokasi Magang -->
                                        <div class="tooltip tooltip-bottom" data-tip="Detail">
                                            <RouterLink 
                                                :to="{ name: 'DetailLokasiMagang', params: {id: lokasi.id}}"
                                                class="btn btn-sm btn-info text-white py-2 px-4 rounded"
                                            >
                                                <span class="pi pi-eye"></span>
                                            </RouterLink>
                                        </div>
                                        
                                        <!-- Edit Lokasi Magang -->
                                        <div class="tooltip tooltip-bottom" data-tip="Edit"
                                            v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Admin'">
                                            <button 
                                                @click="openEditModal(lokasi)" 
                                                class="btn btn-sm btn-warning text-white py-2 px-4 rounded " 
                                            >
                                                <span class="pi pi-pencil"></span>
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Lokasi Magang -->
                                        <div class="tooltip tooltip-bottom" data-tip="Hapus"
                                            v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Admin'">
                                            <button 
                                                @click="handleDeleteLokasi(lokasi)" 
                                                class="btn btn-sm btn-error text-white py-2 px-4 rounded"
                                            >
                                                <span class="pi pi-trash"></span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="modal" :class="{ 'modal-open': showModalKabag }">
                        <div class="modal-box">
                            <DialogKabag @closeModal="closeModalKabag"  :lokasi="selectedLokasi" @SavedKabag="handleSavedKabag" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Delete Confirmation Dialog -->
        <DeleteConfirm 
            v-if="showDeleteDialog" 
            :lokasi="LokasiToDelete"
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
import Loading from '@/components/Loading.vue';
import DialogLokasi from '@/components/Dialog/DialogLokasi.vue';
import DialogKabag from '@/components/Dialog/DialogKabag.vue';
import DeleteConfirm from '@/components/Alerts/DeleteConfirm.vue'; 
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';
import WarningAlert from '@/components/Alerts/WarningAlert.vue';

// State untuk mengontrol alert
const showDeleteDialog = ref(false);
const LokasiToDelete = ref(null);
const isSuccess = ref(false);
const isFailed = ref(false);
const isWarning = ref(false);
const successMessage = ref('');
const failedMessage = ref('');
const warningMessage = ref('');

const AuthStore = useAuthStore();
const loading = ref(false);
const searchQuery = ref('');
const allLokasi = ref([]);

const FetchLokasi = async () => {
    try {
        loading.value=true;
        const { data } = await customAPI.get('/lokasi', {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });
        allLokasi.value = data.data; 
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
};

const filteredLokasi = computed(() => {
    return allLokasi.value.filter(lokasi => {
        const matchesSearchQuery = searchQuery.value === "" ||
            lokasi.nama_lokasi.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            lokasi.alamat_lokasi.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            lokasi.kepala_bagian?.nama_kabag.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            lokasi.kepala_bagian?.nip_kabag.includes(searchQuery.value);
        
            return matchesSearchQuery;
    })
})

const handleDeleteLokasi = (lokasi) => {
    if (Array.isArray(lokasi.list_peserta) && lokasi.list_peserta.length > 0) {
        isWarning.value = true;
        warningMessage.value = 'Lokasi ini memiliki data peserta yang terkait, sehingga tidak dapat dihapus untuk menjaga keutuhan data';
    } else {
        LokasiToDelete.value = lokasi;
        showDeleteDialog.value = true;
    }
}

const confirmDelete = async () => {
    if (LokasiToDelete.value) {
        const paramsId = LokasiToDelete.value.id;
        
        try {
            await customAPI.post(`/lokasi/${paramsId}?_method=DELETE`, null,
            {
                headers: { Authorization: `Bearer ${AuthStore.token}` },
            })
                
            isSuccess.value = true;
            successMessage.value = 'Lokasi Magang berhasil dihapus!';
            FetchLokasi();
        } catch (error) {
            console.error("Terjadi kesalahan saat menghapus lokasi:", error);
            isFailed.value = true;
            failedMessage.value = 'Gagal menghapus data. Silahkan coba lagi!';
        } finally {
            isWarning.value = false;
            showDeleteDialog.value = false;
        }
    }
};

const showModal = ref(false);
const showModalKabag = ref(false);
const editMode = ref(false);
const selectedLokasi = ref(null);

// Modal Tambah Lokasi
const openModal = () => {
    selectedLokasi.value = null;
    showModal.value = true;
    editMode.value = false;
};

// Modal Edit Lokasi
const openEditModal = (LokasiData) => {
    selectedLokasi.value = LokasiData;
    showModal.value = true;
    editMode.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedLokasi.value = null;
    editMode.value = false;
};

const handleSaved = () => {
    FetchLokasi();
    closeModal();  
};

const openModalKabag = (kabag) => {
    selectedLokasi.value = kabag;
    showModalKabag.value = true;
    editMode.value = true;
}

const closeModalKabag = () => {
    showModalKabag.value = false;
    selectedLokasi.value = null;
};

const handleSavedKabag = () => {
    isSuccess.value = true;
    successMessage.value = 'Kepala Bagian berhasil disimpan!';
    FetchLokasi();
    closeModalKabag();  
};

onMounted(() => {
    FetchLokasi();
});
</script>