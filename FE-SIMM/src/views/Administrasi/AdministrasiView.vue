<template>
    <div class="mt-5 mx-5 lg:ml-10">
        <h1 class="text-2xl lg:text-3xl font-bold text-center mb-4 pb-4">Administrasi Magang</h1>

        <div class="grid sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 sm:gap-3 md:gap-4 lg:gap-4">
            <div>
                <div v-if="loading === true" class="my-4">
                    <Loading :chart="true"/>
                </div>
                <div v-else class="card bg-base-200 border border-slate-300 shadow-lg my-4 p-4">
                    <div class="flex flex-col gap-4 items-center">
                        <h1 class="text-lg font-bold text-center">Surat Balasan Magang</h1>

                        <template v-if="filterDokumen('Surat Balasan').length > 0">
                            <div v-for="dokumen in filterDokumen('Surat Balasan')" :key="dokumen.id" class="flex gap-4 items-center">
                                <div class="tooltip tooltip-bottom" data-tip="Lihat">
                                    <button class="btn btn-sm btn-primary text-white" @click="openModal(dokumen)">
                                        <span class="pi pi-eye"></span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div v-else>
                            <div class="badge badge-neutral p-3">Belum tersedia</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div v-if="loading === true" class="my-4">
                    <Loading :chart="true"/>
                </div>
                <div v-else class="card bg-base-200 border border-slate-300 shadow-lg my-4 p-4">
                    <div class="flex flex-col gap-4 items-center">
                        <h1 class="text-lg font-bold text-center">Sertifikat Magang</h1>
                        
                        <template v-if="filterDokumen('Sertifikat').length > 0">
                            <div v-for="dokumen in filterDokumen('Sertifikat')" :key="dokumen.id" class="flex gap-4 items-center">
                                <div class="tooltip tooltip-bottom" data-tip="Lihat">
                                    <button class="btn btn-sm btn-primary text-white" @click="openModal(dokumen)">
                                        <span class="pi pi-eye"></span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div v-else>
                            <div class="badge badge-neutral p-3">Belum tersedia</div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div v-if="loading === true" class="my-4">
                    <Loading :chart="true"/>
                </div>
                <div v-else class="card bg-base-200 border border-slate-300 shadow-lg my-4 p-4">
                    <div class="flex flex-col gap-4 items-center">
                        <h1 class="text-lg font-bold text-center">Laporan Magang</h1>

                        <template v-if="filterDokumen('Laporan Magang').length > 0">
                            <div v-for="dokumen in filterDokumen('Laporan Magang')" :key="dokumen.id" class="flex items-center gap-4">
                                <!-- Tombol Lihat -->
                                <div class="tooltip tooltip-bottom" data-tip="Lihat">
                                    <button class="btn btn-sm btn-primary text-white" @click="openModal(dokumen)">
                                        <span class="pi pi-eye"></span>
                                    </button>
                                </div>

                                <!-- Tombol Edit -->
                                <div class="tooltip tooltip-bottom" data-tip="Edit">
                                    <button class="btn btn-sm btn-warning text-white" @click="triggerUpload(dokumen.jenis.nama_jenis)">
                                        <span class="pi pi-pencil"></span>
                                    </button>
                                </div>

                                <!-- Tombol Hapus -->
                                <div class="tooltip tooltip-bottom" data-tip="Hapus">
                                    <button class="btn btn-sm btn-error text-white" @click="handleDelete(dokumen.id)">
                                        <span class="pi pi-trash"></span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Jika dokumen tidak tersedia -->
                        <div v-else>
                            <button class="btn btn-sm btn-primary text-white" @click="triggerUpload('Laporan Magang')">
                                <span class="pi pi-upload"></span>
                            </button>
                        </div>

                        <!-- Input file untuk unggah dokumen -->
                        <input type="file" ref="fileInput" accept="application/pdf" @change="handleFileUpload" style="display: none;" />
                    </div>
                </div>
            </div>

            <div>
                <div v-if="loading === true" class="my-4">
                    <Loading :chart="true"/>
                </div>
                <div v-else class="card bg-base-200 border border-slate-300 shadow-lg my-4 p-4">
                    <div class="flex flex-col gap-4 items-center">
                        <h1 class="text-lg font-bold text-center">Lembar Penilaian</h1>

                        <template v-if="filterDokumen('Lembar Penilaian').length > 0">
                            <div v-for="dokumen in filterDokumen('Lembar Penilaian')" :key="dokumen.id" class="flex items-center gap-4">
                                <!-- Tombol Lihat -->
                                <div class="tooltip tooltip-bottom" data-tip="Lihat">
                                    <button class="btn btn-sm btn-primary text-white" @click="openModal(dokumen)">
                                        <span class="pi pi-eye"></span>
                                    </button>
                                </div>

                                <!-- Tombol Edit -->
                                <div class="tooltip tooltip-bottom" data-tip="Edit">
                                    <button class="btn btn-sm btn-warning text-white" @click="triggerUpload(dokumen.jenis.nama_jenis)">
                                        <span class="pi pi-pencil"></span>
                                    </button>
                                </div>

                                <!-- Tombol Hapus -->
                                <div class="tooltip tooltip-bottom" data-tip="Hapus">
                                    <button class="btn btn-sm btn-error text-white" @click="handleDelete(dokumen.id)">
                                        <span class="pi pi-trash"></span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Jika dokumen tidak tersedia -->
                        <div v-else>
                            <button class="btn btn-sm btn-primary text-white" @click="triggerUpload('Lembar Penilaian')">
                                <span class="pi pi-upload"></span>
                            </button>
                        </div>

                        <!-- Input file untuk unggah dokumen -->
                        <input type="file" ref="fileInput" accept="application/pdf" @change="handleFileUpload" style="display: none;" />
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div v-if="showModal" class="modal modal-open">
            <div class="modal-box">
                <DialogAdministrasi @closeModal="closeModal" :dokumen="selectedDokumen" />
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <DeleteConfirm 
            v-if="showDeleteDialog" 
            :dokumen="DokumenToDelete"
            @confirmDelete="confirmDelete"
            @cancelDelete="showDeleteDialog = false"
        />
        
        <!-- Success and Failed Alert -->
        <SuccessAlert v-if="isSuccess" :message="successMessage" @alertClosed="isSuccess = false" />
        <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false"  />
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import Loading from '@/components/Loading.vue';
import DialogAdministrasi from '@/components/Dialog/DialogAdministrasi.vue';
import DeleteConfirm from '@/components/Alerts/DeleteConfirm.vue'; 
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';

// State untuk mengontrol alert
const showDeleteDialog = ref(false);
const DokumenToDelete = ref(null);
const isSuccess = ref(false);
const isFailed = ref(false);
const successMessage = ref('');
const failedMessage = ref('');

const AuthStore = useAuthStore();
const loading = ref(false);
const allDokumen = ref([]);
const AllJenisDokumen = ref([]);
const fileInput = ref(null);
const id_peserta = ref(null);
const jenisIdTerpilih = ref(null);

const FetchDokumen = async () => {
    try {
        const { data } = await customAPI.get(`/dokumen/peserta/${id_peserta.value}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });
        allDokumen.value = data.data;
    } catch (error) {
        console.error('Error fetching data dokumen:', error);
    }
};

const filterDokumen = (jenis) => {
    return allDokumen.value.filter(dokumen => dokumen.jenis && dokumen.jenis.nama_jenis === jenis);
};

const FetchJenisDokumen = async () => {
    try {
        const { data } = await customAPI.get('/jenis-dokumen', {
            headers: { Authorization: `Bearer ${AuthStore.token}` },
        });
        AllJenisDokumen.value = data.data;
    } catch (error) {
        console.log('Failed to fetch jenis dokumen:', error);
    }
};

const filterJenisDokumen = (jenis) => {
    const filtered = AllJenisDokumen.value.find(item => item.nama_jenis === jenis);
    jenisIdTerpilih.value = filtered ? filtered.id : null;
};

const triggerUpload = (jenis) => {
    filterJenisDokumen(jenis);
    fileInput.value.click();
};

const handleFileUpload = async (event) => {
    const file = event.target.files[0];

    if (!file) {
        isFailed.value = true;
        failedMessage.value = 'Tidak ada file yang dipilih!';
        return;
    }

    if (file.type !== 'application/pdf') {
        isFailed.value = true;
        failedMessage.value = 'Hanya format PDF yang diizinkan!';
        return;
    }

    const maxFileSize = 5 * 1024 * 1024;
    if (file.size > maxFileSize) {
        isFailed.value = true;
        failedMessage.value = 'Ukuran file maksimal 5MB!';
        return;
    }

    try {
        const formData = new FormData();
        formData.append("file", file);
        formData.append("id_jenis", jenisIdTerpilih.value);
        formData.append("id_peserta", id_peserta.value);

        await customAPI.post('/dokumen', formData, {
            headers: { 
                Authorization: `Bearer ${AuthStore.token}`,
                'Content-Type': 'multipart/form-data',
            },
        });

        await FetchDokumen(); 
        isSuccess.value = true;
        successMessage.value = 'Dokumen berhasil disimpan!';
    } catch (error) {
        console.error('Failed to save document:', error);
        isFailed.value = true;
        failedMessage.value = 'Terjadi kesalahan saat menyimpan dokumen. Silahkan coba lagi!';
    }
};

const showModal = ref(false);
const selectedDokumen = ref(null);

const openModal = (dokumen) => {
    selectedDokumen.value = dokumen;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedDokumen.value = null;
};

const handleDelete = async (dokumenId) => {
    DokumenToDelete.value = dokumenId;
    showDeleteDialog.value = true;
}

const confirmDelete = async () => {
    if (DokumenToDelete.value) {
        const paramsId = DokumenToDelete.value;
        
        try {
            await customAPI.post(`/dokumen/${paramsId}?_method=DELETE`, null,
            {
                headers: { Authorization: `Bearer ${AuthStore.token}` },
            })
            
            isSuccess.value = true;
            successMessage.value = 'Dokumen berhasil dihapus!';
            await FetchDokumen();
            await FetchJenisDokumen();
        } catch (error) {
            console.error("Terjadi kesalahan saat menghapus dokumen:", error);
            isFailed.value = true;
            failedMessage.value = 'Gagal menghapus dokumen. Silahkan coba lagi!';
        } finally {
            showDeleteDialog.value = false;
        }
    }
};

onMounted(async () => {
    try {
        loading.value=true;
        const { data: pesertaData } = await customAPI.get('/me', {
            headers: { Authorization: `Bearer ${AuthStore.token}` },
        });
        id_peserta.value = pesertaData.user.peserta.id;
    } catch (error) {
        console.log('Failed to fetch peserta:', error);
    } finally {
        loading.value = false;
    }
    
    await FetchDokumen();
    await FetchJenisDokumen();
});
</script>
