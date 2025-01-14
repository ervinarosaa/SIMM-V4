<template>
    <div>
        <p class="font-bold text-2xl mb-1">{{ editMode ? 'Ubah Lokasi Magang' : 'Tambah Lokasi Magang' }}</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">
            
        <form @submit.prevent="handleSubmit">
            <div class="flex flex-col gap-1 my-2">
                <label for="Nama Lokasi" class="text-sm font-bold">
                    Nama Lokasi
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="lokasiData.nama_lokasi" required />
                </label>
            </div>

            <div class="flex flex-col gap-1">
                <label for="Alamat" class="text-sm font-bold">
                    Alamat
                    <span class="text-red-500">*</span>
                </label>
                <label class="textarea textarea-bordered flex items-center">
                    <textarea class="grow" v-model="lokasiData.alamat_lokasi" required></textarea>
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="Kuota Magang" class="text-sm font-bold">
                    Kuota Magang
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="number" :min="0" class="grow" v-model="lokasiData.kuota_magang" required />
                </label>
            </div>

            <div v-if="editMode" class="flex flex-col gap-1 my-2">
                <label for="Kuota Terisi" class="text-sm font-bold">
                    Kuota Terisi
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="number" :min="0" class="grow" v-model="lokasiData.kuota_terisi" required />
                </label>
            </div>

            <div class="flex flex-row py-3 items-center justify-end gap-3">
                <div class="btn btn-sm w-20" @click="handleClose">Tutup</div>
                <input type="submit" :value="editMode ? 'Simpan' : 'Tambah'" class="btn btn-sm btn-primary text-white w-20">
            </div>
        </form>

        <!-- Success and Failed Alert -->
        <SuccessAlert v-if="isSuccess" :message="successMessage" @alertClosed="isSuccess = false" />
        <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false"  />
    </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';

const AuthStore = useAuthStore();
const emit = defineEmits(["closeModal", "saved"]);

const props = defineProps({
    lokasi: {
        type: Object,
        required: false, 
    },
    editMode: {
        type: Boolean,
        required: true,
    },
});

// reactive
const lokasiData = reactive({
    nama_lokasi: props.lokasi?.nama_lokasi || '',
    alamat_lokasi: props.lokasi?.alamat_lokasi || '',
    kuota_magang: props.lokasi?.kuota_magang || '',
    kuota_terisi: props.lokasi?.kuota_terisi || '',
});

watch(() => props.lokasi, (newLokasi) => {
    if (newLokasi) {
        lokasiData.nama_lokasi = newLokasi.nama_lokasi;
        lokasiData.alamat_lokasi = newLokasi.alamat_lokasi;
        lokasiData.kuota_magang = newLokasi.kuota_magang;
        lokasiData.kuota_terisi = newLokasi.kuota_terisi;
    }
});

const capitalizeWords = (text) => {
    return text.replace(/\b\w/g, char => char.toUpperCase());
};

const resetForm = () => {
    lokasiData.nama_lokasi = "";
    lokasiData.alamat_lokasi = ""
    lokasiData.kuota_magang = ""
};

// State untuk mengontrol alert
const isSuccess = ref(false);
const isFailed = ref(false);
const successMessage = ref('');
const failedMessage = ref('');

const handleSubmit = async () => {
    try {
        const formData = new FormData();
        formData.append("nama_lokasi", capitalizeWords(lokasiData.nama_lokasi));
        formData.append("alamat_lokasi", lokasiData.alamat_lokasi);
        formData.append("kuota_magang", lokasiData.kuota_magang);

        if (props.editMode) {
            formData.append("kuota_terisi", lokasiData.kuota_terisi);
            await customAPI.post(`/lokasi/${props.lokasi.id}?_method=PUT`, formData, {
                headers: { 
                    Authorization: `Bearer ${AuthStore.token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });        
        } else {
            formData.append("kuota_terisi", "0");
            await customAPI.post('/lokasi', formData, {
                headers: { 
                    Authorization: `Bearer ${AuthStore.token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        }

        resetForm(); 
        emit('saved'); 
        isSuccess.value = true;
        successMessage.value = 'Lokasi Magang berhasil disimpan!';
    } catch (error) {
        console.error('Failed to save lokasi:', error);
        isFailed.value = true;
        failedMessage.value = 'Gagal menyimpan data. Silahkan coba lagi!';
    }
};

const handleClose = () => {
    resetForm();
    emit('closeModal');
}
</script>
