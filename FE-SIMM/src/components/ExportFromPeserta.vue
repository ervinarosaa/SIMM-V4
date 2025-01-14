<template>
    <div>
        <p class="font-bold text-2xl mb-1">Ekspor {{ logbookMode ? 'Logbook' : 'Presensi' }}  Magang</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">

        <form @submit.prevent="handleSubmit">
            <div class="flex flex-col gap-1 my-2">
                <label for="Nama Lengkap" class="text-sm font-bold">
                    Nama Lengkap
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="exportData.nama_peserta" readonly />
                </label>
            </div>

            <div class="flex flex-col gap-1 my-2">
                <label for="Nomor Induk Peserta" class="text-sm font-bold">
                    Nomor Induk Peserta
                </label>
                <label class="input input-bordered flex items-center h-[30px]">
                    <input type="text" class="grow capitalize" v-model="exportData.nomor_induk" readonly />
                </label>
            </div>

            <div class="flex flex-row py-3 items-center justify-end gap-3">
                <div class="btn btn-sm w-20" @click="handleClose">Tutup</div>
                <input type="submit" value="Ekspor" class="btn btn-sm btn-primary text-white w-20">
            </div>
        </form>
        
        <!-- Failed Alert -->
        <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false"  />
    </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';

const AuthStore = useAuthStore();
const emit = defineEmits(["closeModal", "saved"]);

const props = defineProps({
    logbookMode: {
        type: Boolean,
        required: true,
    },
    peserta: {
        type: Object,
    },
})

const exportData = reactive({
    id_peserta: props.peserta?.id || '',
    nama_peserta: props.peserta?.nama_peserta || '',
    nomor_induk: props.peserta?.nomor_induk || '',
});

const resetForm = () => {
    exportData.id_peserta = "",
    exportData.nama_peserta = "",
    exportData.nomor_induk = ""
};

watch(
    () => props.peserta,
    (newPeserta) => {
        if (newPeserta) {
            exportData.id_peserta = newPeserta.id || '';
            exportData.nama_peserta = newPeserta.nama_peserta || '';
            exportData.nomor_induk = newPeserta.nomor_induk || '';
        } else {
            resetForm();
        }
    },
    { immediate: true } 
);

// State untuk mengontrol alert
const isFailed = ref(false);
const failedMessage = ref('');

const handleSubmit = async () => {
    try {
        let params = {};
        let filename = null;

        params = {
            id_peserta: exportData.id_peserta,
        };

        if (props.logbookMode) {
            filename = `Logbook-Magang-${exportData.nama_peserta}.xlsx`;
        } else {
            filename = `Presensi-Magang-${exportData.nama_peserta}.xlsx`;
        }

        let response = null;
        if (props.logbookMode) {
            response = await customAPI.get('/export-logbook', {
                headers: { Authorization: `Bearer ${AuthStore.token}`},
                params,
                responseType: 'blob',
            });
        } else {
            response = await customAPI.get('/export-presensi', {
                headers: { Authorization: `Bearer ${AuthStore.token}`},
                params,
                responseType: 'blob',
            });
        }
        
        const urlBlob = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = urlBlob;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();

        emit('saved');
        resetForm();
    } catch (error) {
        console.error('Failed to export data:', error);
        isFailed.value = true;
        failedMessage.value = 'Gagal mengekspor data. Silahkan coba lagi!';
    }
};

const handleClose = () => {
    resetForm();
    emit('closeModal');
};
</script>
