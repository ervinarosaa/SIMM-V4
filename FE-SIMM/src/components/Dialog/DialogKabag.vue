<template>
    <div>
        <p class="font-bold text-2xl mb-1">Kepala Bagian</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">
            
        <form @submit.prevent="handleSubmit">
            <div class="flex flex-col gap-1 my-2">
                <label for="Nama Lengkap" class="text-sm font-bold">
                    Nama Lengkap
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="kabagData.nama_kabag" required />
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="Nomor Induk Pegawai" class="text-sm font-bold">
                    Nomor Induk Pegawai
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="number" class="grow" v-model="kabagData.nip_kabag" required />
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="Email" class="text-sm font-bold">
                    Email
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="email" class="grow lowercase" v-model="kabagData.email" required />
                </label>
            </div>

            <div class="flex flex-row py-3 items-center justify-end gap-3">
                <div class="btn btn-sm w-20" @click="handleClose">Tutup</div>
                <input type="submit" value="Simpan" class="btn btn-sm btn-primary text-white w-20">
            </div>
        </form>

        <!-- Failed Alert -->
        <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false" />
    </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';

const AuthStore = useAuthStore();
const emit = defineEmits(["closeModal", "SavedKabag"]);

const props = defineProps({
    lokasi: {
        type: Object,
        required: false, 
    },
});

// reactive
const kabagData = reactive({
    nama_kabag: props.lokasi?.kepala_bagian?.nama_kabag || '',
    nip_kabag: props.lokasi?.kepala_bagian?.nip_kabag || '',
    email: props.lokasi?.kepala_bagian?.user?.email || '',
});

watch(() => props.lokasi, (newLokasi) => {
    kabagData.nama_kabag = newLokasi?.kepala_bagian?.nama_kabag || '';
    kabagData.nip_kabag = newLokasi?.kepala_bagian?.nip_kabag || '';
    kabagData.email = newLokasi?.kepala_bagian?.user?.email || '';
}, { immediate: true });

const capitalizeWords = (text) => {
    return text.replace(/\b\w/g, char => char.toUpperCase());
};

const resetForm = () => {
    kabagData.nama_kabag = "";
    kabagData.nip_kabag = "";
    kabagData.email = "";
};

// State untuk mengontrol alert
const isFailed = ref(false);
const failedMessage = ref('');

const handleSubmit = async () => {
    try {
        const formData = new FormData();
        formData.append("nama_kabag", capitalizeWords(kabagData.nama_kabag));
        formData.append("nip_kabag", kabagData.nip_kabag);
        formData.append("email", kabagData.email);
        formData.append("id_lokasi", props.lokasi?.id);

        await customAPI.post('/kepala-bagian', formData, {
            headers: { 
                Authorization: `Bearer ${AuthStore.token}`,
                'Content-Type': 'multipart/form-data',
            },
        });

        resetForm(); 
        emit('SavedKabag'); 
    } catch (error) {
        console.error('Failed to save kepala bagian:', error);
        isFailed.value = true;
        failedMessage.value = 'Gagal menyimpan data. Silahkan coba lagi!';
    } 
};

const handleClose = () => {
    resetForm();
    emit('closeModal');
}
</script>
