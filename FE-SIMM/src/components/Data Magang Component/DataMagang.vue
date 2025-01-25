<template>
    <div class="mt-5 mx-5 lg:ml-10">
        <h1 class="text-xl lg:text-3xl font-bold text-center mb-4 pb-4">{{ arsipMode ? 'Arsip Data Magang' : 'Data Peserta Aktif' }}</h1>
        
        <div class="flex flex-col lg:flex-row gap-2 lg:items-center w-[340px] sm:w-auto md:w-[700px] lg:w-full">
            <div class="flex gap-2 items-center" v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Admin'">
                <!-- Add Page -->
                <RouterLink 
                    :to="{ name: 'AddDataMagang' }"
                    class="btn btn-sm btn-primary text-white"  
                    v-if="!props.arsipMode"
                >
                    <span class="pi pi-user-plus"></span> Tambah
                </RouterLink>
                
                <!-- Export Modal -->
                <button 
                    @click="openModalExport"
                    class="btn btn-sm btn-primary text-white px-5">
                    <span class="pi pi-file-excel"></span> Export
                </button>

                <div class="modal" :class="{ 'modal-open': showModalExport }">
                    <div class="modal-box">
                        <DialogGeneralExport @closeModal="closeModalExport" :arsipMode="props.arsipMode" @Saved="handleSavedExport" />
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row lg:flex-row gap-2 lg:items-center">
                <!-- Dropdown Filter Tingkat Pendidikan -->
                <select v-model="selectedFilter" class="select select-sm select-bordered h-[33px] w-full" @change="applyFilter">
                    <option value="">Tingkat Pendidikan</option>
                    <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                    <option value="Sekolah Kejuruan">Sekolah Kejuruan</option>
                </select>
                <!-- Search -->
                <label class="input input-bordered flex items-center justify-between h-[33px]  lg:w-full">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Cari Peserta..."
                        class="input input-sm w-full"
                    />
                    <span class="pi pi-search"></span>
                </label>
            </div>

            <!-- Dropdown Multi-Select Dokumen -->
            <div class="relative w-full sm:w-auto md:w-[600px]">
                <div @click="toggleDropdown" class="select select-sm select-bordered h-auto w-full flex items-center justify-between">
                    <span>{{ selectedDokumenFilter.length > 0 ? selectedDokumenFilter.join(', ') : 'Pilih Dokumen' }}</span>
                </div>
                <div v-if="isDropdownOpen" class="absolute z-10 bg-white shadow-lg border w-full mt-1 p-2 rounded-md">
                    <label v-for="option in dokumenOptions" :key="option" class="flex items-center gap-2 p-1 hover:bg-gray-100 rounded-md">
                        <input type="checkbox" v-model="selectedDokumenFilter" :value="option" />
                        {{ option }}
                    </label>
                </div>
            </div>
        </div>        

        <div v-if="loading === true" class="my-4 w-[340px] sm:w-auto md:w-[700px] lg:w-full ">
            <Loading :all="true"/>
        </div>
        <div v-else class="my-4" v-for="peserta in paginatedData" :key="peserta.id">
            <div 
                class="card bg-base-200 border border-slate-300 shadow-lg w-[340px] sm:w-auto md:w-[700px] lg:w-full overflow-x-auto"
                @click="handleCardClick(peserta.id)"
            >
                <div class="flex h-min-[160px] h-auto items-center p-2">
                    <div class="w-32 aspect-[3/4] rounded-xl overflow-hidden shrink-0" v-if="props.arsipMode">
                        <img v-if="peserta.foto_profil != null" :src="peserta.foto_profil" class="rounded-xl w-full h-full object-cover" />
                        <img v-else :src="profilDefault" class="rounded-xl w-full h-full object-cover" />
                    </div>
                    <div class="w-24 aspect-[3/4] rounded-xl overflow-hidden shrink-0" v-else>
                        <img v-if="peserta.foto_profil != null" :src="peserta.foto_profil" class="rounded-xl w-full h-full object-cover" />
                        <img v-else :src="profilDefault" class="rounded-xl w-full h-full object-cover" />
                    </div>
                    <div class="card-body py-1 px-5">
                        <div class="flex flex-col-reverse lg:flex-row justify-between lg:items-center min-w-[400px]">
                            <div>
                                <h1 class="font-bold text-xl"> {{ peserta.nomor_induk }} - {{ peserta.nama_peserta }}</h1>
                            </div>
                            <div class="flex gap-2" v-if="AuthStore.user && AuthStore.user.role.nama_role === 'Admin'">
                                <!-- View Detail Data Magang Page -->
                                <div class="tooltip tooltip-bottom" data-tip="Detail">
                                    <RouterLink 
                                        :to="{ name: 'DetailDataMagang', params: {id: peserta.id}}"
                                        class="btn btn-xs btn-info"
                                        v-if="!props.arsipMode">
                                        <span class="pi pi-eye"></span>
                                    </RouterLink>

                                    <!-- View Detail Arsip Data Magang Page -->
                                    <RouterLink 
                                        :to="{ name: 'DetailArsipMagang', params: {id: peserta.id}}"
                                        class="btn btn-xs btn-info"
                                        v-else>
                                        <span class="pi pi-eye"></span>
                                    </RouterLink>
                                </div>
                                
                                <div class="tooltip tooltip-bottom" data-tip="Edit">
                                    <!-- Edit Page -->
                                    <RouterLink 
                                        :to="{ name: 'EditDataMagang', params: {id: peserta.id}}"
                                        class="btn btn-xs btn-warning"
                                        v-if="!props.arsipMode"
                                    >
                                        <span class="pi pi-pencil"></span>
                                    </RouterLink>
                                    <RouterLink 
                                        :to="{ name: 'EditArsipDataMagang', params: {id: peserta.id}}"
                                        class="btn btn-xs btn-warning"
                                        v-if="props.arsipMode"
                                    >
                                        <span class="pi pi-pencil"></span>
                                    </RouterLink>
                                </div>
                                
                                <div class="tooltip tooltip-bottom" data-tip="Cetak Sertifikat"  
                                    v-if="props.arsipMode && peserta.status.nama_status !== 'Batal' 
                                        && (peserta.dokumen.some(doc => doc.jenis_dokumen === 'Laporan Magang') &&
                                        peserta.dokumen.some(doc => doc.jenis_dokumen === 'Lembar Penilaian'))"
                                >
                                    <!-- Cetak Sertifikat -->
                                    <RouterLink 
                                        :to="{ 
                                            name: 'Sertifikat',
                                            query: { peserta: JSON.stringify(peserta) } 
                                        }"
                                        class="btn btn-xs btn-accent shadow-lg" 
                                    >
                                        <span class="pi pi-file-export"></span>
                                    </RouterLink>
                                </div>
                                
                                <div class="tooltip tooltip-bottom" data-tip="Kirim Email">
                                    <!-- Send File To Email -->
                                    <button 
                                        v-if="peserta.status.nama_status !== 'Batal'"
                                        @click="openModalSend(peserta)" 
                                        class="btn btn-xs btn-active btn-default">
                                        <span class="pi pi-upload"></span>
                                    </button>
                                </div>

                                <div class="modal" :class="{ 'modal-open': showModalSend }">
                                    <div class="modal-box">
                                        <DialogSendEmail @closeModal="closeModalSend"  :peserta="selectedPeserta" @saved="handleSavedSend" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <table class="min-w-[600px]">
                                <tbody>
                                    <tr>
                                        <td class="font-semibold w-[190px]">Institusi Pendidikan</td>
                                        <td>:</td>
                                        <td>{{ peserta.institusi?.nama_institusi }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold w-[160px]">Lokasi Magang</td>
                                        <td>:</td>
                                        <td>{{ peserta.lokasi?.nama_lokasi }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold w-[160px]">Periode Magang</td>
                                        <td>:</td>
                                        <td>{{ peserta.tanggal_mulai }} s/d {{ peserta.tanggal_selesai }}</td>
                                    </tr>
                                    <tr v-if="props.arsipMode && peserta.status.nama_status !== 'Batal'">
                                        <td class="font-semibold w-[160px]">Status Dokumen Admin</td>
                                        <td>:</td>
                                        <td>{{ peserta.adminStatus }}</td>
                                    </tr>
                                    <tr v-if="props.arsipMode && peserta.status.nama_status !== 'Batal'">
                                        <td class="font-semibold w-[160px]">Status Dokumen Peserta</td>
                                        <td>:</td>
                                        <td>{{ peserta.pesertaStatus }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center">
            <div class="join">
                <!-- Radio Button untuk Halaman -->
                <input
                    v-for="page in paginatedPages"
                    :key="page"
                    class="join-item btn btn-square"
                    type="radio"
                    :name="'page-' + page"
                    :aria-label="page.toString()"
                    :checked="currentPage === page"
                    @change="goToPage(page)"
                    :value="page"
                />
            </div>
        </div>
        <!-- Success and Failed Alert -->
        <SuccessAlert v-if="isSuccess" :message="successMessage" @alertClosed="isSuccess = false" />
    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/AuthStore';
import { customAPI } from '@/api';
import profilDefault from '@/assets/profil-default.png';
import DialogSendEmail from '@/components/Dialog/DialogSendEmail.vue';
import DialogGeneralExport from '@/components/Dialog/DialogExportDataMagang.vue';
import Loading from '@/components/layouts/Loading.vue';
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';

// State untuk mengontrol alert
const isSuccess = ref(false);
const successMessage = ref('');

const AuthStore = useAuthStore();
const router = useRouter();
const loading = ref(false);
const allPeserta = ref([]);
const searchQuery = ref('');
const selectedFilter = ref('');
const selectedDokumenFilter = ref([]);
const filteredPeserta = ref([]);
const isDropdownOpen = ref(false);

const props = defineProps({
    arsipMode:{
        type: Boolean,
        required: true,
    }
});

// Dokumen yang dibagi antara admin dan peserta
const adminDokumen = ['Surat Balasan', 'Sertifikat'];
const pesertaDokumen = ['Laporan Magang', 'Lembar Penilaian'];

// Fungsi untuk mengecek status dokumen admin
const checkStatusAdmin = (dokumen) => {
    const dokumenAda = dokumen.map(item => item.jenis_dokumen); 
    const dokumenKurang = adminDokumen.filter(item => !dokumenAda.includes(item));

    if (dokumenKurang.length === 1) {
        return `${dokumenKurang[0]} belum diproses`;
    } else if (dokumenKurang.length === 2) {
        return `${dokumenKurang[0]} dan ${dokumenKurang[1]} belum diproses`;
    }
    return 'Semua dokumen admin lengkap';
};

// Fungsi untuk mengecek status dokumen peserta
const checkStatusPeserta = (dokumen) => {
    const dokumenAda = dokumen.map(item => item.jenis_dokumen); 
    const dokumenKurang = pesertaDokumen.filter(item => !dokumenAda.includes(item));

    if (dokumenKurang.length === 1) {
        return `${dokumenKurang[0]} belum diunggah`;
    } else if (dokumenKurang.length === 2) {
        return `${dokumenKurang[0]} dan ${dokumenKurang[1]} belum diunggah`;
    }
    return 'Semua dokumen peserta lengkap';
};

const FetchPeserta = async () => {
    if (props.arsipMode) {
        try {
            loading.value=true;
            const { data } = await customAPI.get('/peserta-arsip', {
                headers: { Authorization: `Bearer ${AuthStore.token}` }
            });
            allPeserta.value = data.data.map(peserta => {
                const adminStatus = checkStatusAdmin(peserta.dokumen);
                const pesertaStatus = checkStatusPeserta(peserta.dokumen);
                return {
                    ...peserta,
                    adminStatus,  // Status untuk admin
                    pesertaStatus // Status untuk peserta
                };
            });
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            loading.value = false;
        }
    } else {
        try {
            loading.value=true;
            const { data } = await customAPI.get('/peserta-aktif', {
                headers: { Authorization: `Bearer ${AuthStore.token}` }
            });
            allPeserta.value = data.data; 
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            loading.value = false;
        }
    };
};
    
const dokumenOptions = ['Surat Balasan', 'Laporan Magang', 'Lembar Penilaian', 'Sertifikat'];

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const applyFilter = async () => {
    let filteredData = allPeserta.value;

    if (selectedFilter.value === 'Perguruan Tinggi') {
        filteredData = filteredData.filter(p => p.institusi.tingkat_pendidikan === 'Perguruan Tinggi');
    } else if (selectedFilter.value === 'Sekolah Kejuruan') {
        filteredData = filteredData.filter(p => p.institusi.tingkat_pendidikan === 'Sekolah Kejuruan');
    }

    // Filter berdasarkan dokumen yang dipilih dalam multiselect
    if (selectedDokumenFilter.value && selectedDokumenFilter.value.length > 0) {
        filteredData = filteredData.filter(p =>
            !p.dokumen.some(dokumen => selectedDokumenFilter.value.includes(dokumen.jenis_dokumen))
        );
    }

    // Filter berdasarkan searchQuery
    if (searchQuery.value) {
        filteredData = filteredData.filter((p) =>
            p.nama_peserta.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            p.nomor_induk.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            p.institusi.nama_institusi.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            p.lokasi.nama_lokasi.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    filteredPeserta.value = filteredData;
    filteredPeserta.value = filteredPeserta.value.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
};

watch([selectedFilter, selectedDokumenFilter, searchQuery], applyFilter);

const selectedPeserta = ref(null);

// Start Modal Export
const showModalExport = ref(false);

const openModalExport = () => {
    showModalExport.value = true;
};

const closeModalExport = () => {
    showModalExport.value = false;
};

const handleSavedExport = () => {
    isSuccess.value = true;
    successMessage.value = 'Data Arsip berhasil diekspor!';
    closeModalExport();
    FetchPeserta().then(applyFilter);
};
// End Modal Export

// Start Modal Send Email
const showModalSend = ref(false);
const openModalSend = (peserta) => {
    selectedPeserta.value = peserta;
    showModalSend.value = true;
};

const closeModalSend = () => {
    showModalSend.value = false;
    selectedPeserta.value = null;
};

const handleSavedSend = () => {
    isSuccess.value = true;
    successMessage.value = 'Dokumen berhasil disimpan dan dikirim!';
    closeModalSend(); 
    FetchPeserta().then(applyFilter);
};
// End Modal Send Email

const handleCardClick = (pesertaId) => {
    if (AuthStore.user.role.nama_role !== 'Admin') {
        router.push({ name:'DetailDataMagang', params: {id: pesertaId} });
    }
}

// Paginasi
const currentPage = ref(1);
const itemsPerPage = 20;

const totalPages = computed(() => Math.ceil(filteredPeserta.value.length / itemsPerPage));

const paginatedPages = computed(() => {
    const pages = [];
    for (let i = 1; i <= totalPages.value; i++) {
        pages.push(i);
    }
    return pages;
});

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredPeserta.value.slice(start, end);
});

const goToPage = (page) => {
    currentPage.value = page;
};

onMounted(async () => {
    await FetchPeserta();
    applyFilter();
});
</script>
