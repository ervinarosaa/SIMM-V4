<template>
    <div class="flex items-center justify-center">
        <div class="mt-5 mx-2 lg:ml-10 p-4 border border-slate-200 shadow-xl rounded-lg lg:w-[500px]">
            <!-- Loading Alert -->
            <LoadingForm :show="isLoading" />

            <p class="font-bold text-2xl mb-1">Sertifikat</p>
            <hr class="pb-3">
                
            <form @submit.prevent="handleSubmit">
                <div class="flex flex-col gap-1 my-2">
                    <label for="Nama Lengkap" class="text-sm font-bold">
                        Nomor Sertifikat
                        <span class="text-red-500">*</span>
                    </label>
                    <label class="input input-bordered flex items-center h-[30px]">
                        <input type="text" class="grow capitalize" v-model="sertifikatData.nomor_sertifikat" required />
                    </label>
                </div>
                
                <div class="flex flex-col gap-1 mt-4">
                    <label for="Tingkat Pendidikan" class="text-sm font-bold">
                        Nilai
                        <span class="text-red-500">*</span>
                    </label>
                    <select class="input input-bordered h-[30px]" v-model="sertifikatData.id_nilai" required>
                        <option disabled value="">Pilih Nilai</option>
                        <option v-for="nilai in AllNilai" :key="nilai.id" :value="nilai.id">
                            {{ nilai.predikat_nilai }}
                        </option>
                    </select>
                </div>

                <div class="flex flex-col gap-1 my-2">
                    <label for="Nama Penandatangan" class="text-sm font-bold">
                        Nama Penandatangan
                        <span class="pi pi-plus p-2 bg-primary rounded text-xs text-white"
                            @click="openModalSertifikat()"></span>
                    </label>
                    <select class="input input-bordered h-[30px]" v-model="sertifikatData.id_penandatangan" required>
                        <option disabled value="">Pilih Penandatangan</option>
                        <option v-for="penandatangan in AllPenandatangan" :key="penandatangan.id" :value="penandatangan.id">
                            {{ penandatangan.nama_penandatangan }} - {{ penandatangan.nip_penandatangan }}
                        </option>
                    </select>
                </div>

                <div class="flex flex-col gap-1 my-2">
                    <label for="NIP Penandatangan" class="text-sm font-bold">
                        NIP Penandatangan
                    </label>
                    <label class="input input-bordered flex items-center h-[30px]">
                        <input type="number" class="grow capitalize" v-model="sertifikatData.nip_penandatangan" required readonly />
                    </label>
                </div>

                <div class="flex flex-col gap-1 my-2">
                    <label for="Jabatan Penandatangan" class="text-sm font-bold">
                        Jabatan Penandatangan
                    </label>
                    <label class="input input-bordered flex items-center h-[30px]">
                        <input type="text" class="grow capitalize" v-model="sertifikatData.jabatan_penandatangan" name="jabatan_penandatangan" @input="filterText" required readonly />
                    </label>
                </div>

                <div class="flex flex-col gap-1 my-2">
                    <label for="Tanggal Penandatangan" class="text-sm font-bold">
                        Tanggal Penandatangan
                        <span class="text-red-500">*</span>
                    </label>
                    <label class="input input-bordered flex items-center h-[30px]">
                        <input type="date" class="grow capitalize" v-model="sertifikatData.tanggal_sertifikat" required />
                    </label>
                </div>

                <div class="flex flex-row py-3 items-center justify-end gap-3">
                    <RouterLink  :to="{ name: 'Arsip Data Magang' }" class="btn btn-sm btn-default">
                        Tutup
                    </RouterLink>
                    <input type="submit" value="Unduh" class="btn btn-sm btn-primary text-white">
                </div>
            </form>

            <div class="modal" :class="{ 'modal-open': showModalSertifikat }">
                <div class="modal-box">
                    <DialogSertifikat @closeModal="closeModalSertifikat" @sertifikatSaved="handleSavedSertifikat" />
                </div>
            </div>

            <!-- Failed Alert -->
            <SuccessAlert v-if="isSuccess" :message="successMessage" />
            <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false" />
            <WarningAlert v-if="isWarning" :message="warningMessage" @alertClosed="isWarning = false" />
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import DialogSertifikat from '@/views/Arsip Data Magang/DialogSertifikat.vue';
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';
import WarningAlert from '@/components/Alerts/WarningAlert.vue';
import LoadingForm from '@/components/Alerts/LoadingForm.vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const AuthStore = useAuthStore();
const AllNilai = ref("");
const AllPenandatangan = ref("");
const defaultDate = new Date().toISOString().split('T')[0];

// Ambil data peserta dari state
const peserta = route.query.peserta ? JSON.parse(route.query.peserta) : null;

const FetchNilai = async () => {
    try {
        const { data } = await customAPI.get('/nilai', {
            headers: { Authorization: `Bearer ${AuthStore.token}` },
        });
        AllNilai.value = data.data;
    } catch (error) {
        console.log('Failed to fetch nilai:', error);
    }
};

const FetchPenandatangan = async () => {
    try {
        const { data } = await customAPI.get('/penandatangan', {
            headers: { Authorization: `Bearer ${AuthStore.token}` },
        });
        AllPenandatangan.value = data.data;
        console.log(peserta);
        
    } catch (error) {
        console.log('Failed to fetch nilai:', error);
    }
};

// reactive
const sertifikatData = reactive({
    nomor_sertifikat: peserta?.sertifikat?.nomor_sertifikat || '',
    id_nilai: peserta?.nilai?.id || '',
    id_penandatangan: peserta?.sertifikat?.id_penandatangan || '',
    nip_penandatangan: peserta?.sertifikat?.penandatangan.nip_penandatangan || '',
    jabatan_penandatangan: peserta?.sertifikat?.penandatangan.jabatan_penandatangan || '',
    tanggal_sertifikat: peserta?.sertifikat?.tanggal_sertifikat || defaultDate,
});

const filterText = (event) => {
    const fieldName = event.target.name;
    if (sertifikatData[fieldName] !== undefined) {
        sertifikatData[fieldName] = event.target.value.replace(/[0-9]/g, '');
    }
};

watch(() => peserta, (newPeserta) => {
    if (newPeserta) {
        sertifikatData.nomor_sertifikat = peserta?.sertifikat?.nomor_sertifikat || '';
        sertifikatData.id_nilai = peserta?.nilai?.id || '';
        sertifikatData.id_penandatangan = peserta?.sertifikat?.id_penandatangan || '';
        sertifikatData.tanggal_sertifikat = peserta?.sertifikat?.tanggal_sertifikat || defaultDate;
    }
});

// Watcher untuk Nama Penandatangan
watch(
    () => sertifikatData.id_penandatangan,
    (newValue) => {
        const selectedPenandatangan = AllPenandatangan.value.find(
            (penandatangan) => penandatangan.id === newValue
        );
        if (selectedPenandatangan) {
            sertifikatData.nip_penandatangan = selectedPenandatangan.nip_penandatangan;
            sertifikatData.jabatan_penandatangan = selectedPenandatangan.jabatan_penandatangan;
        } else {
            sertifikatData.nip_penandatangan = '';
            sertifikatData.jabatan_penandatangan = '';
        }
    }
);

// State untuk mengontrol alert
const isLoading = ref(false);
const isFailed = ref(false);
const failedMessage = ref('');
const isWarning = ref(false);
const warningMessage = ref('');
const isSuccess = ref(false);
const successMessage = ref('');

const handleSubmit = async () => {
    if (peserta.foto_profil === null) {
        isWarning.value = true;
        warningMessage.value = 'Silahkan mengunggah pas foto terlebih dahulu!';
    } else {
        try {
            isLoading.value = true; // Tampilkan loading

            const formData = new FormData();
            formData.append("nomor_sertifikat", sertifikatData.nomor_sertifikat);
            formData.append("id_nilai", sertifikatData.id_nilai);
            formData.append("id_peserta", peserta.id);
            formData.append("id_penandatangan", sertifikatData.id_penandatangan);
            formData.append("tanggal_sertifikat", sertifikatData.tanggal_sertifikat || defaultDate);

            await customAPI.post('/sertifikat', formData, {
                headers: { 
                    Authorization: `Bearer ${AuthStore.token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });

            // kode generate sertifikat
            const pesertaId = peserta?.id || response.data.id_peserta;

            await generateCertificatePDF(pesertaId);

            isSuccess.value = true;
            successMessage.value = 'Sertifikat berhasil diunduh!';
            router.push({ name: "Arsip Data Magang" });
        } catch (error) {
            console.error('Failed to create sertifikat:', error);
            isFailed.value = true;
            failedMessage.value = 'Gagal mengunduh sertifikat. Silahkan coba lagi!';
        } finally {
            isLoading.value = false; // Sembunyikan loading
        }
    }
    
};

const generateCertificatePDF = async (pesertaId) => {
    try {
        // Panggil API untuk generate sertifikat PDF
        const response = await customAPI.get(`/generate-certificate/${pesertaId}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` },
            responseType: 'blob', // Untuk mendownload file sebagai blob
        });

        // Membuat URL dari blob file
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `sertifikat-${peserta.nama_peserta}.pdf`); // Nama file sertifikat
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error('Failed to generate certificate PDF:', error);
        return error;
    }
};

// Start Modal Sertifikat
const showModalSertifikat = ref(false);

const openModalSertifikat = () => {
    showModalSertifikat.value = true;
};

const closeModalSertifikat = () => {
    showModalSertifikat.value = false;
};

const handleSavedSertifikat = () => {
    closeModalSertifikat();
};
// End Modal Sertifikat

onMounted(() => {
    FetchNilai();
    FetchPenandatangan();
})
</script>