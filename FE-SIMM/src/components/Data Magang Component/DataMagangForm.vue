<template>
    <div class="mt-5 mx-2 lg:ml-10 border border-slate-200 shadow-xl rounded-lg">
        <div class="flex items-center bg-base-300 m-2 p-2 rounded-lg font-bold">
            <span v-if="props.title === 'Tambah'" class="pi pi-user-plus px-2 text-xl"></span>
            <span v-else class="pi pi-user-edit px-2 text-xl"></span>
            {{props.title}} Data Magang
        </div>
        
        <form @submit.prevent="handleSubmit()" class="mb-6">
            <div class="flex flex-col lg:flex-row m-2 p-1 gap-2 lg:gap-6">
                <div class="flex flex-col gap-1">
                    <label for="Nomor Induk Siswa" class="text-sm font-bold">
                        Nomor Induk Siswa
                        <span class="text-red-500">*</span>
                    </label>
                    <label class="input input-bordered flex items-center h-[30px] lg:w-[250px]">
                        <input type="text" class="grow" v-model="peserta.nomor_induk" required/>
                    </label>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="Tanggal Mulai" class="text-sm font-bold">
                        Tanggal Mulai
                        <span class="text-red-500">*</span>
                    </label>
                    <label class="input input-bordered flex items-center h-[30px] lg:w-[250px]">
                        <input v-if="AuthStore.user.role.nama_role==='Admin'" type="date" class="grow" v-model="peserta.tanggal_mulai" required />
                        <input v-else type="date" class="grow" v-model="peserta.tanggal_mulai" readonly />
                    </label>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="Tanggal Selesai" class="text-sm font-bold">
                        Tanggal Selesai
                        <span class="text-red-500">*</span>
                    </label>
                    <label class="input input-bordered flex items-center h-[30px] lg:w-[250px]">
                        <input v-if="AuthStore.user.role.nama_role==='Admin'" type="date" :min="minEndDate"  class="grow" v-model="peserta.tanggal_selesai" required />
                        <input v-else type="date" class="grow"v-model="peserta.tanggal_selesai" readonly />
                    </label>
                </div>
                <div v-if="props.isUpdate" class="flex flex-col gap-1">
                    <label for="Status" class="text-sm font-bold">
                        Status
                        <span class="text-red-500">*</span>
                    </label>
                    <select v-if="AuthStore.user.role.nama_role==='Admin'" class="input input-bordered h-[30px] lg:w-[250px]" v-model="peserta.id_status" required>
                        <option disabled value="">Pilih Status Magang</option>
                        <option v-for="status in AllStatus" :key="status.id" :value="status.id">
                            {{ status.nama_status }}
                        </option>
                    </select>
                    <div v-else>
                        <select class="input input-bordered h-[30px] w-full lg:w-[250px] bg-gray-100" v-model="peserta.id_status" disabled>
                            <option disabled value="">Pilih Status Magang</option>
                            <option v-for="status in AllStatus" :key="status.id" :value="status.id">
                                {{ status.nama_status }}
                            </option>
                        </select>
                        <!-- Input Hidden untuk Mengirim Data -->
                        <input type="hidden" v-model="peserta.id_status" name="id_status" />
                    </div>
                </div>
            </div>
            
            <div class="divider m-2 p-1"></div>

            <div class="m-2 p-1 gap-6 grid lg:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <div class="flex flex-col gap-1">
                        <label for="Nama Lengkap" class="text-sm font-bold">
                            Nama Lengkap
                            <span class="text-red-500">*</span>
                        </label>
                        <label class="input input-bordered flex items-center h-[30px]">
                            <input type="text" class="grow capitalize" v-model="peserta.nama_peserta" required />
                        </label>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="Email" class="text-sm font-bold">
                            Email
                            <span class="text-red-500">*</span>
                        </label>
                        <label class="input input-bordered flex items-center h-[30px]">
                            <input v-if="!props.isUpdate" type="email" class="grow lowercase" v-model="peserta.email" required />
                            <input v-else type="email" class="grow lowercase" v-model="peserta.email" readonly />
                        </label>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="Nomor Telepon" class="text-sm font-bold">
                            Nomor Telepon
                            <span class="text-red-500">*</span>
                        </label>
                        <label class="input input-bordered flex items-center h-[30px]">
                            <input type="text" class="grow capitalize" v-model="peserta.nomor_telepon" required />
                        </label>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="Lokasi Magang" class="text-sm font-bold">
                            Lokasi Magang
                            <span class="text-red-500">*</span>
                        </label>
                        <select v-if="AuthStore.user.role.nama_role==='Admin'" class="input input-bordered h-[30px]" v-model="peserta.id_lokasi" required>
                            <option disabled value="">Pilih Lokasi Magang</option>
                            <option v-for="lokasi in AllLokasi" :key="lokasi.id" :value="lokasi.id">
                                {{ lokasi.nama_lokasi }}
                            </option>
                        </select>
                        <div v-else>
                            <select class="input input-bordered h-[30px] bg-gray-100 w-full cursor-not-allowed" v-model="peserta.id_lokasi" disabled>
                                <option disabled value="">Pilih Lokasi Magang</option>
                                <option v-for="lokasi in AllLokasi" :key="lokasi.id" :value="lokasi.id">
                                    {{ lokasi.nama_lokasi }}
                                </option>
                            </select>
                            <!-- Hidden Input untuk Menyimpan Data -->
                            <input type="hidden" class="grow" v-model="peserta.id_lokasi" name="id_lokasi" />
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="Tingkat Pendidikan" class="text-sm font-bold">
                            Tingkat Pendidikan
                            <span class="text-red-500">*</span>
                        </label>
                        <select v-if="AuthStore.user.role.nama_role==='Admin'" class="input input-bordered h-[30px]" v-model="tingkatPendidikan" required>
                            <option disabled value="">Pilih Tingkat Pendidikan</option>
                            <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                            <option value="Sekolah Kejuruan">Sekolah Kejuruan</option>
                        </select>
                        <div v-else>
                            <select class="input input-bordered h-[30px] bg-gray-100 w-full cursor-not-allowed" v-model="tingkatPendidikan" disabled>
                                <option disabled value="">Pilih Tingkat Pendidikan</option>
                                <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                                <option value="Sekolah Kejuruan">Sekolah Kejuruan</option>
                            </select>
                            <!-- Hidden Input untuk Menyimpan Data -->
                            <input type="hidden" class="grow" v-model="tingkatPendidikan" name="tingkatPendidikan" />
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="Institusi Asal" class="text-sm font-bold">
                            Institusi Asal
                            <span class="text-red-500">*</span>
                        </label>
                        <select v-if="AuthStore.user.role.nama_role==='Admin'" class="input input-bordered h-[30px]" v-model="peserta.id_institusi" required>
                            <option disabled value="">Pilih Institusi Asal</option>
                            <option v-for="institusi in AllInstitusi" :key="institusi.id" :value="institusi.id">
                                {{ institusi.nama_institusi }}
                            </option>
                        </select>
                        <div v-else>
                            <select class="input input-bordered h-[30px] bg-gray-100 w-full cursor-not-allowed" v-model="peserta.id_institusi" disabled>
                                <option disabled value="">Pilih Institusi Asal</option>
                                <option v-for="institusi in AllInstitusi" :key="institusi.id" :value="institusi.id">
                                    {{ institusi.nama_institusi }}
                                </option>
                            </select>
                            <!-- Hidden Input untuk Menyimpan Data -->
                            <input type="hidden" class="grow" v-model="peserta.id_institusi" name="id_institusi" />
                        </div>
                    </div>
                    <div v-if="tingkatPendidikan === 'Perguruan Tinggi'" class="flex flex-col gap-1">
                        <label for="Fakultas" class="text-sm font-bold">
                            Fakultas
                            <span class="text-red-500">*</span>
                        </label>
                        <label class="input input-bordered flex items-center h-[30px]">
                            <input v-if="AuthStore.user.role.nama_role==='Admin'" type="text" class="grow capitalize" v-model="peserta.fakultas" />
                            <input v-else type="text" class="grow capitalize" v-model="peserta.fakultas" required />
                        </label>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="Jurusan" class="text-sm font-bold">
                            Jurusan
                            <span class="text-red-500">*</span>
                        </label>
                        <label class="input input-bordered flex items-center h-[30px]">
                            <input v-if="AuthStore.user.role.nama_role==='Admin'" type="text" class="grow capitalize" v-model="peserta.jurusan" />
                            <input v-else type="text" class="grow capitalize" v-model="peserta.jurusan" />
                        </label>
                    </div>
                </div>
                
                <div class="flex flex-col gap-2">
                    <div class="flex flex-col gap-1">
                        <label for="Alamat" class="text-sm font-bold" v-if="AuthStore.user.role.nama_role === 'Admin'">Alamat</label>
                        <label for="Alamat" class="text-sm font-bold" v-else>
                            Alamat
                            <span class="text-red-500">*</span>
                        </label>
                        <label class="textarea textarea-bordered flex items-center">
                            <textarea class="grow capitalize" v-model="peserta.alamat_peserta" v-if="AuthStore.user.role.nama_role === 'Admin'"></textarea>
                            <textarea class="grow capitalize" v-model="peserta.alamat_peserta" required v-else></textarea>
                        </label>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="Dosen Pembimbing" class="text-sm font-bold">Dosen Pembimbing</label>
                        <label class="input input-bordered flex items-center h-[30px]">
                            <input type="text" class="grow capitalize" v-model="peserta.nama_pembimbing" />
                        </label>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="Kontak Dosen Pembimbing" class="text-sm font-bold">Kontak Dosen Pembimbing</label>
                        <label class="input input-bordered flex items-center h-[30px]">
                            <input type="text" class="grow capitalize" v-model="peserta.kontak_pembimbing" />
                        </label>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="Pas Foto" class="text-sm font-bold" v-if="AuthStore.user.role.nama_role === 'Admin'">Pas Foto</label>
                        <label for="Pas Foto" class="text-sm font-bold" v-else>
                            Pas Foto
                            <span class="text-red-500">*</span>
                        </label>
                        <label class="input input-bordered flex items-center h-[30px]">
                            <input type="file" class="grow" accept=".png,.jpeg,.jpg" @change="handleFileChange" />
                        </label>
                        <div class="flex gap-2 mt-1">
                            <div class="w-28 aspect-[3/4] rounded-xl overflow-hidden">
                                <img :src="previewImage" class="rounded-xl w-full h-full object-cover" />
                            </div>
                            <div class="flex flex-col">
                                <p class="font-semibold">Keterangan:</p>
                                <ul class="list-disc list-inside">
                                    <li>Tipe file yang data diunggah adalah .jpeg/.jpg/.png</li>
                                    <li>Ukuran file maksimal 2 MB</li>
                                    <li>Rasio foto 3:4</li>
                                    <li>Foto akan digunakan untuk sertifikat</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider mx-2 px-1"></div>
            
            <div class="mx-2 px-1 flex flex-row py-3 items-center gap-3">
                <div v-if="AuthStore.user.role.nama_role === 'Admin'">
                    <RouterLink  :to="{ name: 'Data Magang' }" class="btn btn-sm btn-default" v-if="!props.isArsip">
                        Tutup
                    </RouterLink>
                    <RouterLink  :to="{ name: 'Arsip Data Magang' }" class="btn btn-sm btn-default" v-else>
                        Tutup
                    </RouterLink>
                </div>
                
                <RouterLink  :to="{ name: 'Home' }" class="btn btn-sm btn-default" v-if="AuthStore.user.role.nama_role === 'Peserta'">
                    Tutup
                </RouterLink>

                <input type="submit" :value="props.title" class="btn btn-primary text-white btn-sm px-4">
            </div>
        </form>

        <!-- Success and Failed Alert -->
        <SuccessAlert v-if="isSuccess" :message="successMessage" />
        <FailedAlert v-if="isFailed" :message="failedMessage" />
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/AuthStore';
import { ref, onMounted, reactive, watch, watchEffect, computed } from 'vue';
import { customAPI } from '@/api';
import { useRouter } from 'vue-router';
import { format } from 'date-fns';
import profilDefault from '@/assets/profil-default.png';
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';

// State untuk mengontrol alert
const isSuccess = ref(false);
const isFailed = ref(false);
const successMessage = ref('');
const failedMessage = ref('');

const AuthStore = useAuthStore();
const router = useRouter();
const AllLokasi = ref({});
const AllInstitusi = ref([]);
const AllStatus = ref([]);

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    isUpdate: {
        type: Boolean,
        default: false,
    },
    tingkatPend: {
        type: String,
    },
    peserta: {
        type: Object,
        default: () => null,
    },
    isArsip: {
        type: Boolean,
        default: false,
    }
});

const tingkatPendidikan = ref(props.tingkatPend || "");
const previewImage = ref(props.peserta?.foto_profil || profilDefault);

const peserta = reactive({
    nomor_induk: props.peserta?.nomor_induk || "",
    tanggal_mulai: props.peserta?.tanggal_mulai || "",
    tanggal_selesai: props.peserta?.tanggal_selesai || "",
    nama_peserta: props.peserta?.nama_peserta || "",
    nomor_telepon: props.peserta?.nomor_telepon || "",
    email: props.peserta?.user?.email || "",
    alamat_peserta: props.peserta?.alamat_peserta || "",
    nama_pembimbing: props.peserta?.nama_pembimbing || "",
    kontak_pembimbing: props.peserta?.kontak_pembimbing || "",
    id_lokasi: props.peserta?.id_lokasi || "",
    id_status: props.peserta?.id_status || "",
    fakultas: props.peserta?.fakultas || "",
    jurusan: props.peserta?.jurusan || "",
    id_institusi: props.peserta?.id_institusi || "",
    foto_profil: props.peserta?.foto_profil || profilDefault,
});

const minEndDate = computed(() => {
    if (peserta.tanggal_mulai) {
        return peserta.tanggal_mulai; 
    }
    return '';
});

watch(() => props.tingkatPend, (newValue) => {
    tingkatPendidikan.value = newValue || '';
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file && file.size <= 2 * 1024 * 1024) { 
        peserta.foto_profil = file;
        const reader = new FileReader();

        reader.onload = (e) => {
            previewImage.value = e.target.result; 
        };

        reader.readAsDataURL(file);
    } else {
        file.value = null;
        event.target.value = "";
        alert("Ukuran foto lebih dari 2MB!");
    }
};

const FetchLokasi = async () => {
    try {
        const { data } = await customAPI.get('/lokasi-option', {
            headers: { Authorization: `Bearer ${AuthStore.token}` },
        });
        AllLokasi.value = data.data;
    } catch (error) {
        console.log('Failed to fetch lokasi:', error);
    }
};

watch(
    [() => props.isUpdate, () => peserta.id_lokasi],
    async ([isUpdate, id_lokasi]) => {
        if (isUpdate && id_lokasi) {
            const lokasiExists = AllLokasi.value.some(lokasi => lokasi.id === id_lokasi);

            if (!lokasiExists) {
                try {
                    const { data: currentLoc } = await customAPI.get(`/lokasi/${id_lokasi}`, {
                        headers: { Authorization: `Bearer ${AuthStore.token}` },
                    });

                    AllLokasi.value.push({
                        id: currentLoc.data.id,
                        nama_lokasi: currentLoc.data.nama_lokasi,
                    });
                } catch (error) {
                    console.error('Failed to fetch specific lokasi:', error);
                }
            }
        }
    },
    { immediate: true }
);

const FetchStatus = async () => {
    try {
        const { data } = await customAPI.get('/status', {
            headers: { Authorization: `Bearer ${AuthStore.token}` },
        });
        AllStatus.value = data.data;
    } catch (error) {
        console.log('Failed to fetch status:', error);
    }
};

const FetchUniversitas = async () => {
    try {
        const { data } = await customAPI.get('/universitas', {
            headers: { Authorization: `Bearer ${AuthStore.token}` },
        });
        AllInstitusi.value = data.data;
    } catch (error) {
        console.log('Failed to fetch universitas:', error);
    }
};

const FetchSekolah = async () => {
    try {
        const { data } = await customAPI.get('/sekolah', {
            headers: { Authorization: `Bearer ${AuthStore.token}` },
        });
        AllInstitusi.value = data.data;
    } catch (error) {
        console.log('Failed to fetch sekolah:', error);
    }
};

watchEffect(() => {
    if (props.peserta) {
        peserta.nomor_induk = props.peserta.nomor_induk || "";
        peserta.tanggal_mulai = props.peserta.tanggal_mulai || "";
        peserta.tanggal_selesai = props.peserta.tanggal_selesai || "";
        peserta.nama_peserta = props.peserta.nama_peserta || "";
        peserta.nomor_telepon = props.peserta.nomor_telepon || "";
        peserta.email = props.peserta?.user?.email || "";
        peserta.alamat_peserta = props.peserta.alamat_peserta || "";
        peserta.nama_pembimbing = props.peserta.nama_pembimbing || "";
        peserta.kontak_pembimbing = props.peserta.kontak_pembimbing || "";
        peserta.id_lokasi = props.peserta.id_lokasi || "";
        peserta.id_status = props.peserta.id_status || "";
        peserta.fakultas = props.peserta.fakultas || "";
        peserta.jurusan = props.peserta.jurusan || "";
        peserta.id_institusi = props.peserta.id_institusi || "";
    }
    
    if (props.peserta && props.peserta.foto_profil) {
        previewImage.value = props.peserta.foto_profil;
    } else {
        previewImage.value = profilDefault;
    }

    if (tingkatPendidikan.value === 'Perguruan Tinggi') {
        FetchUniversitas();
    } else if (tingkatPendidikan.value === 'Sekolah Kejuruan') {
        FetchSekolah();
    }
});

const formatDate = (date) => {
    return format(new Date(date), "yyyy-MM-dd");
};

const capitalizeWords = (text) => {
    return text.replace(/\b\w/g, char => char.toUpperCase());
};

const handleSubmit = async () => {
    try {
        const formData = new FormData();
        formData.append("tanggal_mulai", formatDate(peserta.tanggal_mulai));
        formData.append("tanggal_selesai", formatDate(peserta.tanggal_selesai));
        formData.append("nama_peserta", capitalizeWords(peserta.nama_peserta));
        formData.append("nomor_telepon", peserta.nomor_telepon);
        formData.append("email", peserta.email);
        formData.append("alamat_peserta", capitalizeWords(peserta.alamat_peserta) || "-");
        formData.append("nama_pembimbing", capitalizeWords(peserta.nama_pembimbing) || "-");
        formData.append("kontak_pembimbing", peserta.kontak_pembimbing || "-");
        formData.append("id_lokasi", peserta.id_lokasi);
        formData.append("jurusan", capitalizeWords(peserta.jurusan) || "-");
        formData.append("nomor_induk", peserta.nomor_induk);
        formData.append("id_institusi", peserta.id_institusi);
        
        if (peserta.foto_profil instanceof File) {
            formData.append("foto_profil", peserta.foto_profil);
        } 

        if (tingkatPendidikan.value === 'Perguruan Tinggi') {
            formData.append("fakultas", capitalizeWords(peserta.fakultas));
        } else if (tingkatPendidikan.value === 'Sekolah Kejuruan') {
            formData.append("fakultas", "-");
        }
        
        if (!props.isUpdate) {
            await customAPI.post("/peserta", formData, {
                headers: { Authorization: `Bearer ${AuthStore.token}` },
            });
        } else {
            formData.append("id_status", peserta.id_status);
            
            await customAPI.post(`/peserta/${props.peserta.id}?_method=PUT`, formData, {
                headers: { Authorization: `Bearer ${AuthStore.token}` },
            });
        }
        
        isSuccess.value = true;
        successMessage.value = 'Data Magang berhasil disimpan!';
        if (AuthStore.user.role.nama_role === 'Admin') {
            if (props.isArsip) {
                router.push({ name: "Arsip Data Magang" });
            } else {
                router.push({ name: "Data Magang" });
            }
        } else {
            router.push({ name: "Home" });
        }
    } catch (error) {
        console.error('Failed to submit data:', error);
        isFailed.value = true;
        failedMessage.value = 'Gagal menyimpan data. Silahkan coba lagi!';
    }
};

onMounted(() => {
    FetchLokasi();
    FetchStatus();
});
</script>
