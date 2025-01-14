<template>
    <div>
        <p class="font-bold text-2xl mb-1">{{ editMode ? 'Edit ' : 'Tambah' }} Logbook Magang</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">
            
        <form @submit.prevent="handleSubmit">
            <div class="flex flex-col gap-1 my-2">
                <label for="Tanggal" class="text-sm font-bold">
                    Tanggal
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="date" class="grow" :max="maxDate" v-model="logbookData.tanggal_logbook" required />
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="Judul Kegiatan" class="text-sm font-bold">
                    Judul Kegiatan
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="logbookData.title" required />
                </label>
            </div>

            <div class="flex flex-col gap-1">
                <label for="Deskripsi Kegiatan" class="text-sm font-bold">
                    Deskripsi Kegiatan
                    <span class="text-red-500">*</span>
                </label>
                <label class="textarea textarea-bordered flex items-center">
                    <textarea class="grow" v-model="logbookData.deskripsi" required></textarea>
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="Dokumentasi Kegiatan" class="text-sm font-bold">
                    Dokumentasi Kegiatan
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[35px]" v-if="!props.editMode" >
                    <input type="file" class="grow" accept="image/*" @change="handleUpload" required/>
                </label>
                <label class="input input-bordered flex items-center h-[35px]" v-else >
                    <input type="file" class="grow" accept="image/*" @change="handleUpload"/>
                </label>
            </div>

            <!-- Menampilkan gambar dokumentasi yang sudah ada -->
            <div v-if="imagePreview" class="flex justify-center mt-4">
                <img :src="imagePreview" alt="Image Preview" />
            </div>

            <div class="flex flex-row py-3 items-center justify-end gap-3">
                <div class="btn btn-sm w-20" @click="handleClose">Tutup</div>
                <input type="submit" :value="editMode ? 'Simpan' : 'Tambah'" class="btn btn-sm btn-primary text-white w-20">
            </div>
        </form>

        <!-- Success and Failed Alert -->
        <SuccessAlert v-if="isSuccess" :message="successMessage" />
        <FailedAlert v-if="isFailed" :message="failedMessage" />
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
const maxDate = new Date().toISOString().split('T')[0];

const props = defineProps({
    logbook: {
        type: Object,
        required: false, 
    },
    editMode: {
        type: Boolean,
        required: true,
    },
});

// reactive
const logbookData = reactive({
    tanggal_logbook: props.logbook?.tanggal_logbook || '',
    title: props.logbook?.title || '',
    deskripsi: props.logbook?.deskripsi || '',
    dokumentasi: null,
});

const imagePreview = ref(props.logbook?.dokumentasi || null);

watch(() => props.logbook, (newVal) => {
    if (newVal) {
        Object.assign(logbookData, newVal);
        imagePreview.value = newVal.dokumentasi;
    }
}, { immediate: true });

const selectedImage = ref(null);
const handleUpload = (e) => {
    const MAX_SIZE_MB = 2;
    const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;
    const file = e.target.files[0];

    if (file.size > MAX_SIZE_BYTES) {
        alert("Ukuran foto lebih dari 2MB!");
        e.target.value = ""; // Reset input file
        return;
    }
    selectedImage.value = file;
    logbookData.dokumentasi = selectedImage;
    imagePreview.value = URL.createObjectURL(file); 
};

const capitalizeWords = (text) => {
    return text.replace(/\b\w/g, char => char.toUpperCase());
};

const resetForm = () => {
    logbookData.tanggal_logbook = null;
    logbookData.title = "";
    logbookData.deskripsi = "";
    selectedImage.value = null;
    logbookData.dokumentasi = null;
    imagePreview.value = null;
};

// State untuk mengontrol alert
const isSuccess = ref(false);
const isFailed = ref(false);
const successMessage = ref('');
const failedMessage = ref('');

const handleSubmit = async () => {
    try {
        const formData = new FormData();
        formData.append("tanggal_logbook", capitalizeWords(logbookData.tanggal_logbook));
        formData.append("title", logbookData.title);
        formData.append("deskripsi", logbookData.deskripsi);
        
        const { data } = await customAPI.get('/me', {
            headers: { Authorization: `Bearer ${AuthStore.token}`},
        });

        formData.append("id_peserta", data.user.peserta.id);

        if (logbookData.dokumentasi instanceof File) {
            formData.append("dokumentasi", logbookData.dokumentasi);
        } 
        

        if (props.editMode) {
            await customAPI.post(`/logbook/${props.logbook.id}?_method=PUT`, formData, {
                headers: { 
                    Authorization: `Bearer ${AuthStore.token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        } else {
            await customAPI.post('/logbook', formData, {
                headers: { 
                    Authorization: `Bearer ${AuthStore.token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        }

        emit('saved'); 
        resetForm(); 
        isSuccess.value = true;
        successMessage.value = 'Logbook Magang berhasil disimpan!';
    } catch (error) {
        console.error('Failed to save logbook:', error);
        isFailed.value = true;
        failedMessage.value = 'Gagal menyimpan data. Silahkan coba lagi!';
    }
};

const handleClose = () => {
    resetForm();
    emit('closeModal');
}
</script>
