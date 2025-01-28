<template>
    <div>
        <!-- Loading Alert -->
        <LoadingForm :show="isLoading" />

        <p class="font-bold text-2xl mb-1">Data Penandatangan</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">
            
        <form @submit.prevent="handleSubmit">
            <div class="flex flex-col gap-1 my-2">
                <label for="Nama Penandatangan" class="text-sm font-bold">
                    Nama Penandatangan
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="sertifikatData.nama_penandatangan" name="nama_penandatangan" @input="filterText" required />
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="NIP Penandatangan" class="text-sm font-bold">
                    NIP Penandatangan
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="number" class="grow capitalize" v-model="sertifikatData.nip_penandatangan" required />
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="Jabatan Penandatangan" class="text-sm font-bold">
                    Jabatan Penandatangan
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="sertifikatData.jabatan_penandatangan" name="jabatan_penandatangan" @input="filterText" required />
                </label>
            </div>

            <div class="flex flex-row py-3 items-center justify-end gap-3">
                <div class="btn btn-sm w-20" @click="handleClose">Tutup</div>
                <input type="submit" value="Simpan" class="btn btn-sm btn-primary text-white">
            </div>
        </form>

        <!-- Failed Alert -->
        <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false" />
        <SuccessAlert v-if="isSuccess" :message="successMessage" @alertClosed="isSuccess = false" />
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';
import LoadingForm from '@/components/Alerts/LoadingForm.vue';

const AuthStore = useAuthStore();
const emit = defineEmits(["closeModal", "sertifikatSaved"]);

// reactive
const sertifikatData = reactive({
    nama_penandatangan: '',
    nip_penandatangan: '',
    jabatan_penandatangan: '',
});

const filterText = (event) => {
    const fieldName = event.target.name;
    if (sertifikatData[fieldName] !== undefined) {
        sertifikatData[fieldName] = event.target.value.replace(/[0-9]/g, '');
    }
};

const resetForm = () => {
    sertifikatData.nama_penandatangan = "";
    sertifikatData.nip_penandatangan = "";
    sertifikatData.jabatan_penandatangan = "";
};

// State untuk mengontrol alert
const isLoading = ref(false);
const isFailed = ref(false);
const isSuccess = ref(false);
const failedMessage = ref('');
const successMessage = ref('');

const handleSubmit = async () => {
    try {
        isLoading.value = true; // Tampilkan loading

        const formData = new FormData();
        formData.append("nama_penandatangan", sertifikatData.nama_penandatangan);
        formData.append("nip_penandatangan", sertifikatData.nip_penandatangan);
        formData.append("jabatan_penandatangan", sertifikatData.jabatan_penandatangan);

        await customAPI.post('/penandatangan', formData, {
            headers: { 
                Authorization: `Bearer ${AuthStore.token}`,
                'Content-Type': 'multipart/form-data',
            },
        });

        emit('sertifikatSaved'); 
        resetForm(); 

        isSuccess.value = true;
        successMessage.value = 'Data berhasil disimpan!';
    } catch (error) {
        console.error('Failed to save data:', error);
        isFailed.value = true;
        failedMessage.value = 'Gagal menyimpan data. Silahkan coba lagi!';
    } finally {
        isLoading.value = false; // Sembunyikan loading
    }
};

const handleClose = () => {
    resetForm();
    emit('closeModal');
}

</script>
