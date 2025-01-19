<template>
    <div>
        <p class="font-bold text-2xl mb-1">{{ editMode ? 'Ubah Institusi Pendidikan' : 'Tambah Institusi Pendidikan' }}</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">
            
        <form @submit.prevent="handleSubmit">
            <div class="flex flex-col gap-1 my-2">
                <label for="Nama Lengkap" class="text-sm font-bold">
                    Nama Institusi Pendidikan
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="institusiData.nama_institusi" required />
                </label>
            </div>
            
            <div class="flex flex-col gap-1 mt-4">
                <label for="Tingkat Pendidikan" class="text-sm font-bold">
                    Tingkat Pendidikan
                    <span class="text-red-500">*</span>
                </label>
                <select class="input input-bordered h-[30px]" v-model="institusiData.tingkat_pendidikan" required>
                    <option disabled value="">Pilih Tingkat Pendidikan</option>
                    <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                    <option value="Sekolah Kejuruan">Sekolah Kejuruan</option>
                </select>
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
const emit = defineEmits(["closeModal", "institusiSaved"]);

const props = defineProps({
    institusi: {
        type: Object,
        required: false, 
    },
    editMode: {
        type: Boolean,
        required: true,
    },
});

// reactive
const institusiData = reactive({
    nama_institusi: props.institusi?.nama_institusi || '',
    tingkat_pendidikan: props.institusi?.tingkat_pendidikan|| '',
});

watch(() => props.institusi, (newInstitusi) => {
    if (newInstitusi) {
        institusiData.nama_institusi = newInstitusi.nama_institusi;
        institusiData.tingkat_pendidikan = newInstitusi.tingkat_pendidikan;
    }
});

const capitalizeWords = (text) => {
    return text.replace(/\b\w/g, char => char.toUpperCase());
};

const resetForm = () => {
    institusiData.nama_institusi = "";
    institusiData.tingkat_pendidikan = ""
};

// State untuk mengontrol alert
const isSuccess = ref(false);
const isFailed = ref(false);
const successMessage = ref('');
const failedMessage = ref('');

const handleSubmit = async () => {
    try {
        const formData = new FormData();
        formData.append("nama_institusi", capitalizeWords(institusiData.nama_institusi));
        formData.append("tingkat_pendidikan", institusiData.tingkat_pendidikan);

        if (props.editMode) {
            await customAPI.post(`/institusi/${props.institusi.id}?_method=PUT`, formData, {
                headers: { 
                    Authorization: `Bearer ${AuthStore.token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });          
        } else {
            await customAPI.post('/institusi', formData, {
                headers: { 
                    Authorization: `Bearer ${AuthStore.token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        }

        emit('institusiSaved'); 
        resetForm(); 
        isSuccess.value = true;
        successMessage.value = 'Institusi Pendidikan berhasil disimpan!';
    } catch (error) {
        console.error('Failed to save institusi:', error);
        isFailed.value = true;
        failedMessage.value = 'Gagal menyimpan data. Silahkan coba lagi!';
    }
};

const handleClose = () => {
    resetForm();
    emit('closeModal');
}
</script>
