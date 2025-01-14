<template>
    <div>
        <p class="font-bold text-2xl mb-1">Sertifikat</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
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
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="sertifikatData.nama_penandatangan" required />
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="NIP Penandatangan" class="text-sm font-bold">
                    NIP Penandatangan
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="sertifikatData.nip_penandatangan" required />
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="Jabatan Penandatangan" class="text-sm font-bold">
                    Jabatan Penandatangan
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="sertifikatData.jabatan_penandatangan" required />
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="Tanggal Penandatangan" class="text-sm font-bold">
                    Tanggal Penandatangan
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="date" class="grow capitalize" v-model="sertifikatData.tanggal_penandatangan" required />
                </label>
            </div>

            <div class="flex flex-row py-3 items-center justify-end gap-3">
                <div class="btn btn-sm w-20" @click="handleClose">Tutup</div>
                <input type="submit" value="Unduh" class="btn btn-sm btn-primary text-white">
            </div>
        </form>

        <!-- Failed Alert -->
        <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false" />
        <WarningAlert v-if="isWarning" :message="warningMessage" @alertClosed="isWarning = false" />
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';
import WarningAlert from '@/components/Alerts/WarningAlert.vue';

const AuthStore = useAuthStore();
const emit = defineEmits(["closeModal", "sertifikatSaved"]);
const AllNilai = ref("");
const defaultDate = new Date().toISOString().split('T')[0];

const props = defineProps({
    peserta: {
        type: Object,
        required: false, 
    },
});

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

// reactive
const sertifikatData = reactive({
    nomor_sertifikat: props.peserta?.sertifikat?.nomor_sertifikat || '',
    id_nilai: props.peserta?.nilai?.id || '',
    nama_penandatangan: props.peserta?.sertifikat?.nama_penandatangan || '',
    nip_penandatangan: props.peserta?.sertifikat?.nip_penandatangan || '',
    jabatan_penandatangan: props.peserta?.sertifikat?.jabatan_penandatangan || '',
    tanggal_penandatangan: props.peserta?.sertifikat?.tanggal_penandatangan || defaultDate,
});

watch(() => props.peserta, (newPeserta) => {
    if (newPeserta) {
        sertifikatData.nomor_sertifikat = props.peserta?.sertifikat?.nomor_sertifikat || '';
        sertifikatData.id_nilai = props.peserta?.nilai?.id || '';
        sertifikatData.nama_penandatangan = props.peserta?.sertifikat?.nama_penandatangan || '';
        sertifikatData.nip_penandatangan = props.peserta?.sertifikat?.nip_penandatangan || '';
        sertifikatData.jabatan_penandatangan = props.peserta?.sertifikat?.jabatan_penandatangan || '';
        sertifikatData.tanggal_penandatangan = props.peserta?.sertifikat?.tanggal_penandatangan || defaultDate;
    }
});

const resetForm = () => {
    sertifikatData.nomor_sertifikat = "";
    sertifikatData.id_nilai = "";
    sertifikatData.nama_penandatangan = "";
    sertifikatData.nip_penandatangan = "";
    sertifikatData.jabatan_penandatangan = "";
    sertifikatData.tanggal_penandatangan = "";
};

// State untuk mengontrol alert
const isFailed = ref(false);
const failedMessage = ref('');
const isWarning = ref(false);
const warningMessage = ref('');

const handleSubmit = async () => {
    if (props.peserta.foto_profil === null) {
        isWarning.value = true;
        warningMessage.value = 'Silahkan mengunggah pas foto terlebih dahulu!';
        handleClose()
    } else {
        try {
            const formData = new FormData();
            formData.append("nomor_sertifikat", sertifikatData.nomor_sertifikat);
            formData.append("id_nilai", sertifikatData.id_nilai);
            formData.append("id_peserta", props.peserta.id);
            formData.append("nama_penandatangan", sertifikatData.nama_penandatangan);
            formData.append("nip_penandatangan", sertifikatData.nip_penandatangan);
            formData.append("jabatan_penandatangan", sertifikatData.jabatan_penandatangan);
            formData.append("tanggal_penandatangan", sertifikatData.tanggal_penandatangan || defaultDate);

            await customAPI.post('/sertifikat', formData, {
                headers: { 
                    Authorization: `Bearer ${AuthStore.token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });

            // kode generate sertifikat
            const pesertaId = props.peserta?.id || response.data.id_peserta;

            await generateCertificatePDF(pesertaId);

            emit('sertifikatSaved'); 
            resetForm(); 
        } catch (error) {
            console.error('Failed to create sertifikat:', error);
            isFailed.value = true;
            failedMessage.value = 'Gagal mengunduh sertifikat. Silahkan coba lagi!';
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
        link.setAttribute('download', `sertifikat-${props.peserta.nama_peserta}.pdf`); // Nama file sertifikat
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error('Failed to generate certificate PDF:', error);
    }
};

const handleClose = () => {
    resetForm();
    emit('closeModal');
}

onMounted(() => {
    FetchNilai();
})
</script>
