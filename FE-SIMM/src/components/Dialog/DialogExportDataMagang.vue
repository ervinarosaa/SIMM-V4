<template>
    <div>
        <p class="font-bold text-2xl mb-1">Ekspor Data</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">

        <form @submit.prevent="handleSubmit">
            <div class="flex flex-col gap-1 mt-4">
                <label for="Tanggal Awal" class="text-sm font-bold">
                    Tanggal Awal
                    <span class="text-red-500">*</span>
                </label>
                <input type="date" class="input input-bordered h-[30px]" :max="maxEndDate" v-model="startDate" required />
            </div>
            <div class="flex flex-col gap-1 mt-4">
                <label for="Tanggal Akhir" class="text-sm font-bold">
                    Tanggal Akhir
                    <span class="text-red-500">*</span>
                </label>
                <input type="date" class="input input-bordered h-[30px]" :min="minEndDate" :max="maxEndDate" v-model="endDate" required />
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
import { ref, computed } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';

const AuthStore = useAuthStore();
const emit = defineEmits(["closeModal", "Saved"]);
const startDate = ref(null);
const endDate = ref(null);

const props = defineProps({
    arsipMode:{
        type: Boolean,
        required: true,
    }
});

const minEndDate = computed(() => {
    if (startDate.value) {
        return startDate.value; 
    }
    return '';
});

const maxEndDate = new Date().toISOString().split('T')[0];

const handleClose = () => {
    emit("closeModal");
};

const resetForm = () => {
    startDate.value = null;
    endDate.value = null;
};

// State untuk mengontrol alert
const isFailed = ref(false);
const failedMessage = ref('');

const handleSubmit = async () => {
    const exportData = {
        startDate: startDate.value,
        endDate: endDate.value
    };

    try {
        let response = null;
        let blob = null;
        let filename = null;
        if (props.arsipMode) {
            response = await customAPI.post('/export-arsip-peserta', exportData, {
                headers: { Authorization: `Bearer ${AuthStore.token}` },
                responseType: 'blob',
            });
            blob = response.data;
            filename = `Laporan-Arsip-Magang-(${startDate.value}_${endDate.value}).xlsx`
        } else {
            response = await customAPI.post('/export-peserta-aktif', exportData, {
                headers: { Authorization: `Bearer ${AuthStore.token}` },
                responseType: 'blob',
            });
            blob = response.data;
            filename = `Laporan-Data-Magang-Aktif-(${startDate.value}_${endDate.value}).xlsx`
        }
        
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        emit("Saved");
        resetForm();
    } catch (error) {
        console.error('Error exporting data:', error);
        isFailed.value = true;
        failedMessage.value = 'Gagal mengekspor data. Silahkan coba lagi!';
    }
};
</script>
