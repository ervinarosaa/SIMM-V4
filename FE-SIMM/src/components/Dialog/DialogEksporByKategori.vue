<template>
    <div>
        <p class="font-bold text-2xl mb-1">Ekspor {{ logbookMode ? 'Logbook' : 'Presensi' }}  Magang</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">

        <form @submit.prevent="handleSubmit">
            <div class="flex flex-col gap-1 my-2">
                <label for="Kategori Filter" class="text-sm font-bold">
                    Kategori Ekspor
                    <span class="text-red-500">*</span>
                </label>
                <select class="input input-bordered h-[30px]" v-model="kategori" required>
                    <option disabled value="">Pilih Kategori Filter</option>
                    <option value="Tanggal">Tanggal</option>
                    <option value="Peserta Magang">Peserta Magang</option>
                </select>
            </div>

            <div v-if="formKategori === 'Peserta Magang'" class="flex flex-col">
                <div class="flex flex-col gap-1 mt-2">
                    <label for="Institusi Pendidikan" class="text-sm font-bold">
                        Institusi Pendidikan
                        <span class="text-red-500">*</span>
                    </label>
                    <select class="input input-bordered h-[30px]" v-model="id_institusi" required>
                        <option disabled value="">Pilih Institusi</option>
                        <option v-for="institusi in AllInstitusi" :key="institusi.id" :value="institusi.id">
                            {{ institusi.nama_institusi }}
                        </option>
                    </select>
                </div>
                <div class="flex flex-col gap-1 mt-2">
                    <label for="Peserta Magang" class="text-sm font-bold">
                        Peserta Magang
                        <span class="text-red-500">*</span>
                    </label>
                    <select class="input input-bordered h-[30px]" v-model="id_peserta" required>
                        <option disabled value="">Pilih Peserta Magang</option>
                        <option v-for="peserta in AllPeserta" :key="peserta.id" :value="peserta.id">
                            {{ peserta.nomor_induk }} - {{ peserta.nama_peserta }}
                        </option>
                    </select>
                </div>
            </div>

            <div v-else-if="formKategori === 'Tanggal'" class="flex flex-col">
                <div class="flex flex-col gap-1 mt-4">
                    <label for="Tanggal Awal" class="text-sm font-bold">
                        Tanggal Awal
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="date" class="input input-bordered h-[30px]" :max="maxEndDate" v-model="selectedStartDate" required />
                </div>
                <div class="flex flex-col gap-1 mt-4">
                    <label for="Tanggal Akhir" class="text-sm font-bold">
                        Tanggal Akhir
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="date" class="input input-bordered h-[30px]" :min="minEndDate" :max="maxEndDate" v-model="selectedEndDate" required />
                </div>
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
import { ref, watch, onMounted, computed } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';

const AuthStore = useAuthStore();
const emit = defineEmits(["closeModal", "saved"]);

let kategori = ref("");
let formKategori = ref("");
let id_institusi = ref("");
let id_peserta = ref("");
let selectedStartDate = ref(null);
let selectedEndDate = ref(null);
const AllInstitusi = ref([]);
const AllPeserta = ref([]);

const props = defineProps({
    logbookMode: {
        type: Boolean,
        required: true,
    }
})

const FetchInstitusi = async () => {
    try {
        const { data } = await customAPI.get('/institusi', {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });
        AllInstitusi.value = [...data.data];
    } catch (error) {
        console.log('Error fetching data:', error);
    }
};

const FetchPeserta = async () => {
    try {
        const { data } = await customAPI.get(`/institusi/${id_institusi.value}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });

        if ( data.data &&
            Array.isArray(data.data.list_peserta) &&
            data.data.list_peserta.length > 0) {
            AllPeserta.value = data.data.list_peserta;
        } 
    } catch (error) {
        AllPeserta.value = [];
    }
}

watch(id_institusi, () => {
    id_peserta.value = "";
    FetchPeserta();
});

watch(kategori, (newKategori) => {
    formKategori.value = newKategori;
});

const minEndDate = computed(() => {
    if (selectedStartDate.value) {
        return selectedStartDate.value; 
    }
    return '';
});

const maxEndDate = new Date().toISOString().split('T')[0];

const resetForm = () => {
    kategori.value = "";
    formKategori.value = "";
    id_institusi.value = "";
    selectedStartDate.value = null;
    selectedEndDate.value = null;
};

// State untuk mengontrol alert
const isFailed = ref(false);
const failedMessage = ref('');

const handleSubmit = async () => {
    try {
        let params = {};
        let filename = null;

        if (kategori.value === "Tanggal") {
            params = {
                startDate: selectedStartDate.value,
                endDate: selectedEndDate.value,
            };

            if (props.logbookMode) {
                filename = `Logbook-Magang-(${selectedStartDate.value}_${selectedEndDate.value}).xlsx`;
            } else {
                filename = `Presensi-Magang-(${selectedStartDate.value}_${selectedEndDate.value}).xlsx`;
            }
        } else if (kategori.value === "Peserta Magang") {
            const peserta = AllPeserta.value.find(p => p.id === id_peserta.value);
            if (!id_peserta.value) {
                alert('Peserta Magang belum dipilih!');
                return; 
            }
            params = {
                id_peserta: id_peserta.value,
            };

            if (props.logbookMode) {
                filename = `Logbook-Magang-${peserta.nama_peserta}.xlsx`;
            } else {
                filename = `Presensi-Magang-${peserta.nama_peserta}.xlsx`;
            }
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

onMounted(() => {
    FetchInstitusi();
});
</script>
