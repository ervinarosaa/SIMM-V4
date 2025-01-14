<template>
    <div class="mt-5 mx-5 lg:ml-10 max-w-[340px] sm:max-w-[660px] md:max-w-[750px] lg:max-w-full">
        <h1 class="text-2xl lg:text-3xl font-bold text-center mb-4 pb-4">Institusi Pendidikan</h1>

        <div class="flex flex-col lg:flex-row gap-2 lg:items-center">
            <button 
                v-if="AuthStore.user.role.nama_role === 'Admin'" 
                class="btn btn-sm btn-primary text-white" 
                @click="openModal()">
                <span class="pi pi-building"></span> Tambah
            </button>

            <div class="modal" :class="{ 'modal-open': showModal }">
                <div class="modal-box">
                    <DialogInstitusi @closeModal="closeModal" :editMode="editMode" :institusi="selectedInstitusi" @institusiSaved="handleSaved" />
                </div>
            </div>

            <!-- Search -->
            <label class="input input-bordered flex items-center justify-between h-[33px]">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Cari nama institusi..."
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
                                <td>INSTITUSI PENDIDIKAN</td>
                                <td>TINGKAT PENDIDIKAN</td>
                                <td>AKSI</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(institusi, index) in filteredInstitusi" :key="institusi.id" class="text-center">
                                <th>{{ index + 1 }}</th>
                                <td class="text-lg">{{ institusi.nama_institusi }}</td>
                                <td class="w-[150px] text-lg">{{ institusi.tingkat_pendidikan?.nama_tingkat }}</td>
                                <td>
                                    <div class="flex flex-row gap-2 justify-center items-center">
                                        <!-- View Detail Institusi -->
                                        <div class="tooltip tooltip-bottom" data-tip="Detail">
                                            <RouterLink 
                                                :to="{ name: 'DetailInstitusi', params: {id: institusi.id}}"
                                                class="btn btn-sm btn-info text-white py-2 px-4 rounded"
                                            >
                                                <span class="pi pi-eye"></span>
                                            </RouterLink>
                                        </div>
                                        
                                        <!-- Edit Institusi -->
                                        <div class="tooltip tooltip-bottom" data-tip="Edit"
                                            v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Admin'">
                                            <button 
                                                @click="openEditModal(institusi)" 
                                                class="btn btn-sm btn-warning text-white py-2 px-4 rounded " 
                                            >
                                                <span class="pi pi-pencil"></span>
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Institusi -->
                                        <div class="tooltip tooltip-bottom" data-tip="Hapus"
                                            v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Admin'">
                                            <button 
                                                @click="handleDeleteInstitusi(institusi)" 
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
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <DeleteConfirm 
            v-if="showDeleteDialog" 
            :institusi="institutiToDelete"
            @confirmDelete="confirmDelete"
            @cancelDelete="showDeleteDialog = false"
        />
        
        <!-- Success and Failed Alert -->
        <SuccessAlert v-if="isSuccess" :message="successMessage" />
        <FailedAlert v-if="isFailed" :message="failedMessage" />
        <WarningAlert v-if="isWarning" :message="warningMessage" @alertClosed="isWarning = false"  />
    </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/AuthStore';
import { customAPI } from '@/api';
import Loading from '@/components/Loading.vue';
import DialogInstitusi from '@/components/Dialog/DialogInstitusi.vue';
import DeleteConfirm from '@/components/Alerts/DeleteConfirm.vue'; 
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';
import WarningAlert from '@/components/Alerts/WarningAlert.vue';

// State untuk mengontrol alert
const showDeleteDialog = ref(false);
const institutiToDelete = ref(null);
const isSuccess = ref(false);
const isFailed = ref(false);
const isWarning = ref(false);
const successMessage = ref('');
const failedMessage = ref('');
const warningMessage = ref('');

const AuthStore = useAuthStore();

const loading = ref(false);
const searchQuery = ref('');
const allInstitusi = ref([]);

const FetchInstitusi = async () => {
    try {
        loading.value=true;
        const {data} = await customAPI.get('/institusi', {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });
        allInstitusi.value = data.data;
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
};

const filteredInstitusi = computed(() => {
    return allInstitusi.value.filter(institusi => {
        const matchesSearchQuery = searchQuery.value === "" ||
            institusi.nama_institusi.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            institusi.tingkat_pendidikan?.nama_tingkat.toLowerCase().includes(searchQuery.value.toLowerCase());
            return matchesSearchQuery;
    })
});

const handleDeleteInstitusi = (institusi) => {
    if (Array.isArray(institusi.list_peserta) && institusi.list_peserta.length > 0) {
        isWarning.value = true;
        warningMessage.value = 'Institusi ini memiliki data peserta yang terkait, sehingga tidak dapat dihapus untuk menjaga keutuhan data';
    } else {
        institutiToDelete.value = institusi;
        showDeleteDialog.value = true;
    } 
};

const confirmDelete = async () => {
    if (institutiToDelete.value) {
        const id = institutiToDelete.value.id;
        
        try {
            await customAPI.post(`/institusi/${id}?_method=DELETE`, null, {
                headers: { Authorization: `Bearer ${AuthStore.token}` },
            });
            isSuccess.value = true;
            successMessage.value = 'Institusi Pendidikan berhasil dihapus!';
            FetchInstitusi();
        } catch (error) {
            console.error("Terjadi kesalahan saat menghapus institusi:", error);
            isFailed.value = true;
            failedMessage.value = 'Gagal menghapus data. Silahkan coba lagi!';
        } finally {
            isWarning.value = false;
            showDeleteDialog.value = false;
        }
    }
};

const showModal = ref(false);
const editMode = ref(false);
const selectedInstitusi = ref(null);

const openModal = () => {
    selectedInstitusi.value = null;
    showModal.value = true;
    editMode.value = false;
};

const openEditModal = (InstitusiData) => {
    selectedInstitusi.value = InstitusiData;
    showModal.value = true;
    editMode.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedInstitusi.value = null;
    editMode.value = false;
};

const handleSaved = () => {
    FetchInstitusi();
    closeModal(); 
};

onMounted(() => {
    FetchInstitusi();
});
</script>