<template>
    <div>
        <p class="font-bold text-2xl mb-1">Tambah Presensi Magang</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">

        <form @submit.prevent="handleSubmit">
            <div v-if="AuthStore.user.role.nama_role === 'Admin'">
                <div class="flex flex-col">
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
                </div>

                <div class="flex flex-col">
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
            </div>

            <div class="flex flex-col">
                <div class="flex flex-col gap-1 mt-2">
                    <label for="Tanggal" class="text-sm font-bold">
                        Tanggal
                        <span class="text-red-500">*</span>
                    </label>
                    <input v-if="AuthStore.user.role.nama_role === 'Admin'"
                        type="datetime-local" 
                        class="input input-bordered h-[30px] w-full" 
                        v-model="tanggal_presensi" 
                        :max="currentDate" 
                        required 
                    />
                    <input v-else-if="AuthStore.user.role.nama_role === 'Peserta'"
                        type="datetime-local" 
                        class="input input-bordered h-[30px] w-full" 
                        v-model="tanggal_presensi"
                        readonly 
                    />
                </div>
            </div>
            
            <div class="flex flex-col">
                <div class="flex flex-col gap-1 mt-2">
                    <label for="Keterangan" class="text-sm font-bold">
                        Keterangan
                        <span class="text-red-500">*</span>
                    </label>
                    <select class="input input-bordered h-[30px]" v-model="keterangan_presensi" required v-if="AuthStore.user.role.nama_role === 'Admin'">
                        <option disabled value="">Pilih Keterangan</option>
                        <option value="Hadir">Hadir</option>
                        <option value="Izin">Izin</option>
                        <option value="Sakit">Sakit</option>
                    </select>
                    <div v-else>
                        <label class="input input-bordered flex items-center h-[30px]">
                            <input type="text" value="Hadir" class="grow" readonly>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex flex-row py-3 items-center justify-end gap-3">
                <div class="btn btn-sm w-20" @click="handleClose">Tutup</div>
                <input type="submit" value="Simpan" class="btn btn-sm btn-primary text-white w-20">
            </div>
        </form>

        <!-- Success and Failed Alert -->
        <FailedAlert v-if="isFailed" :message="failedMessage" @alertClosed="isFailed = false"  />
        <WarningAlert v-if="isWarning" :message="warningMessage" @alertClosed="isWarning = false"  />
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';
import WarningAlert from '@/components/Alerts/WarningAlert.vue';

const AuthStore = useAuthStore();
const emit = defineEmits(["closeModal", "saved"]);
const getLocalDateTime = () => {
    const now = new Date();
    const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000);
    return localDateTime.toISOString().slice(0, 16);
};

const currentDate = ref(getLocalDateTime());

let id_institusi = ref("");
let id_peserta = ref("");
let keterangan_presensi = ref("");
let tanggal_presensi = AuthStore.user.role.nama_role === 'Admin' ? ref(null) : currentDate;
const AllInstitusi = ref([]);
const AllPeserta = ref([]);

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
        const { data } = await customAPI.get(`/institusi-aktif/${id_institusi.value}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });

        if ( data.data &&
            Array.isArray(data.data.list_peserta_aktif) &&
            data.data.list_peserta_aktif.length > 0) {
            AllPeserta.value = data.data.list_peserta_aktif;
        } else {
            AllPeserta.value = []; 
        }
    } catch (error) {
        console.log('Error fetching data peserta:', error);
        AllPeserta.value = [];
    }
}

watch(id_institusi, () => {
    id_peserta.value = "";
    FetchPeserta();
});

const latitude_presensi = ref(null);
const longitude_presensi = ref(null);

const getLocation = async () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                latitude_presensi.value = position.coords.latitude;
                longitude_presensi.value = position.coords.longitude;
            },
            (error) => {
                console.error('Error getting location:', error.message);
            },
            {
                enableHighAccuracy: true,
                timeout: 30000,
                maximumAge: 0,
            }
        );
    } else {
        console.log("Geolocation is not supported by this browser.");
    }
};

const resetForm = () => {
    id_institusi.value = "";
    keterangan_presensi.value = "";
    if (AuthStore.user.role.nama_role === 'Admin') {
        tanggal_presensi.value = null;
    }
};

// State untuk mengontrol alert
const isFailed = ref(false);
const failedMessage = ref('');
const isWarning = ref(false);
const warningMessage = ref('');

const checkDuplicatePresensi = async (idPeserta, tanggalPresensi) => {
    const tanggalOnly = tanggalPresensi.slice(0, 10);

    try {
        const { data } = await customAPI.get(`/check`, {
            params: {
                id_peserta: idPeserta,
                tanggal_presensi: tanggalOnly, 
            },
            headers: { Authorization: `Bearer ${AuthStore.token}` }
        });

        return data.data;
    } catch (error) {
        console.error('Error checking duplicate:', error);
        return false;
    }
};

const handleSubmit = async () => {
    try {
        const isDuplicate = await checkDuplicatePresensi(id_peserta.value, tanggal_presensi.value);

        if (isDuplicate) {
            isWarning.value = true;
            warningMessage.value = "Data presensi sudah ada!";
            return;
        }

        await getLocation();

        if (!latitude_presensi.value || !longitude_presensi.value) {
            alert('Gagal mendapatkan lokasi, coba lagi!');
            return;
        }

        const formData = new FormData();
        formData.append("keterangan_presensi", keterangan_presensi.value || "Hadir");
        formData.append("tanggal_presensi", tanggal_presensi.value);

        if (AuthStore.user.role.nama_role === 'Admin') {
            const { data } = await customAPI.get(`/peserta/${id_peserta.value}`, {
                headers: { Authorization: `Bearer ${AuthStore.token}`},
            });
            formData.append("id_peserta", id_peserta.value);
            formData.append("latitude_presensi", data.data.lokasi.latitude_lokasi);
            formData.append("longitude_presensi", data.data.lokasi.longitude_lokasi);
        } else {
            const { data } = await customAPI.get('/me', {
                headers: { Authorization: `Bearer ${AuthStore.token}`},
            });
            formData.append("id_peserta", data.user.peserta.id);
            formData.append("latitude_presensi", latitude_presensi.value);
            formData.append("longitude_presensi", longitude_presensi.value);
        }

        await customAPI.post('/presensi', formData, {
            headers: { 
                Authorization: `Bearer ${AuthStore.token}`,
                'Content-Type': 'multipart/form-data',
            },
        });

        resetForm();
        emit('saved');
    } catch (error) {
        console.error('Terjadi kesalahan:', error);
        isFailed.value = true;
        failedMessage.value = error.response.data.message || 'Gagal menyimpan data. Silahkan coba lagi!';
    }
};

const handleClose = () => {
    resetForm();
    emit('closeModal');
};

onMounted(() => {
    FetchInstitusi();
    getLocation();
});
</script>
