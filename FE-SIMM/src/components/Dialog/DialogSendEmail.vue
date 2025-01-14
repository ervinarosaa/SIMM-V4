<template>
    <div>
        <p class="font-bold text-2xl mb-1">Unggah File Attachment</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">
            
        <form @submit.prevent="handleSubmit">
            <div class="flex flex-col gap-1 my-2">
                <label for="Nama Lengkap" class="text-sm font-bold">
                    Jenis Dokumen
                    <span class="text-red-500">*</span>
                </label>
                <select class="input input-bordered h-[30px]" v-model="jenis_dokumen" required>
                    <option disabled value="">Pilih Jenis Dokumen</option>
                    <option v-for="jenis in AllJenisDokumen" :key="jenis.id" :value="jenis.id">
                        {{ jenis.nama_jenis }}
                    </option>
                </select>
            </div>
            
            <div class="flex flex-col gap-1 mt-4">
                <label for="Tingkat Pendidikan" class="text-sm font-bold">
                    Unggah Dokumen
                    <span class="text-red-500">*</span>
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="file" class="grow" accept=".doc,.docx,.pdf, .PDF" @change="handleFileChange" />
                </label>
            </div>
            <div class="flex flex-row py-3 items-center justify-end gap-3">
                <div class="btn btn-sm w-20" @click="handleClose">Tutup</div>
                <input type="submit" value="Kirim" class="btn btn-sm btn-primary text-white w-20">
            </div>
        </form>

        <!-- Failed Alert -->
        <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';

const AuthStore = useAuthStore();
const emit = defineEmits(["closeModal", "saved"]);
const AllJenisDokumen = ref("");
const jenis_dokumen = ref("");
const unggah_dokumen = ref(null);

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

const props = defineProps({
    peserta: {
        type: Object,
        required: false, 
    },
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const maxFileSize = 5 * 1024 * 1024;
        if (file.size > maxFileSize) {
            alert('Ukuran file tidak boleh lebih dari 5MB.');
            unggah_dokumen.value = null;
            event.target.value = "";
            return;
        } else {
            unggah_dokumen.value = file;
        }
    }
};

onMounted(() => {
    FetchJenisDokumen();
})

const resetForm = () => {
    jenis_dokumen.value = "";
    unggah_dokumen.value = "";
};

// State untuk mengontrol alert
const isFailed = ref(false);
const failedMessage = ref('');

const handleSubmit = async () => {
    if (!unggah_dokumen.value) {
        alert('Dokumen belum dipilih!');
        return;
    }

    try {
        const formData = new FormData();
        formData.append("file", unggah_dokumen.value);
        formData.append("id_jenis", jenis_dokumen.value);
        formData.append("id_peserta", props.peserta.id);

        await customAPI.post('/sendmail', formData, {
            headers: { 
                Authorization: `Bearer ${AuthStore.token}`,
                'Content-Type': 'multipart/form-data',
            },
        });

        resetForm(); 
        emit('saved'); 
    } catch (error) {
        console.error('Failed to send documen:', error);
        isFailed.value = true;
        failedMessage.value = 'Gagal menyimpan dan mengirim dokumen. Silahkan coba lagi!';
    } 
};

const handleClose = () => {
    resetForm();
    emit('closeModal');
}
</script>
