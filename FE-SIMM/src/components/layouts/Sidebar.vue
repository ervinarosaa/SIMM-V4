<template>
    <!-- Sidebar -->
    <div class="group relative lg:block hidden">
        <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
        <ul class="fixed p-2 bg-base-200 text-base-content w-14 group-hover:w-60 transition-all duration-300 min-h-full overflow-y-auto">
            <!-- User Info -->
            <li class="mb-4">
                <div class="flex items-center gap-3">
                    <div v-if="role.nama_role == 'Admin'">
                        <div v-if="loading === true">
                            <Loading :sidebar="true"/>
                        </div>
                        <div v-else>
                            <div class="w-10 h-10 rounded-full bg-accent text-neutral flex items-center justify-center">
                                <span class="text-lg font-bold">{{ user.nama_admin.charAt(0).toUpperCase() }}</span>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="role.nama_role == 'Kepala Bagian'">
                        <div v-if="loading === true">
                            <Loading :sidebar="true"/>
                        </div>
                        <div v-else>
                            <div class="w-10 h-10 rounded-full bg-accent text-neutral flex items-center justify-center">
                                <span class="text-lg font-bold">{{ user.nama_kabag.charAt(0).toUpperCase() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else>
                        <div v-if="loading === true">
                            <Loading :sidebar="true"/>
                        </div>
                        <RouterLink v-if="user.id" :to="{ name: 'EditDataMagang', params: { id: user.id } }">
                            <img v-if="user.foto_profil != null" :src="user.foto_profil" alt="User Avatar" class="w-10 h-10 rounded-full object-cover object-center" />
                            <img v-else :src="profilDefault" alt="User Avatar" class="w-10 h-10 rounded-full object-cover object-center" />
                        </RouterLink>
                    </div>

                    <div class="hidden group-hover:block">
                        <p v-if="role.nama_role == 'Admin'" class="font-semibold">{{ user.nama_admin}}</p>
                        <p v-else-if="role.nama_role == 'Kepala Bagian'" class="font-semibold">{{ user.nama_kabag}}</p>
                        <p v-else class="font-semibold">{{ user.nama_peserta}}</p>
                        <p class="text-sm text-gray-500">{{role.nama_role}}</p>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <div class="divider"></div>

            <!-- Menu Items -->
            <li class="hover:bg-base-300 hover:rounded" v-for="side in filterMenu" :key="side.id">
                <RouterLink :to="side.url" class="flex px-2 py-3 gap-4 items-center">
                    <span :class="side.icon" class="text-xl text-gray-500"></span>
                    <span class="hidden group-hover:block">{{ side.name }}</span>
                </RouterLink>
            </li>
            <li class="hover:bg-base-300 hover:rounded" @click="handleLogout">
                <div class="flex justify-start px-2 py-3 gap-4">
                    <span class="pi pi-sign-out text-xl text-gray-500"></span>
                    <span class="hidden group-hover:block">Logout</span>
                </div>
            </li>
        </ul>
    </div>
    
    <div class="drawer-side lg:hidden min-h-full z-10">
        <label for="my-drawer-3" aria-label="close sidebar" class="drawer-overlay"></label>
        <ul class="fixed p-2 bg-base-200 text-base-content min-h-full overflow-y-auto pt-20">
            <!-- User Info -->
            <li class="mb-4">
                <div class="flex items-center gap-3">
                    <div v-if="role.nama_role == 'Admin'">
                        <div v-if="loading === true">
                            <Loading :sidebar="true"/>
                        </div>
                        <div v-else class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-accent text-neutral flex items-center justify-center">
                                <span class="text-lg font-bold">{{ user.nama_admin.charAt(0).toUpperCase() }}</span>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="role.nama_role == 'Kepala Bagian'">
                        <div v-if="loading === true">
                            <Loading :sidebar="true"/>
                        </div>
                        <div v-else>
                            <div class="w-10 h-10 rounded-full bg-accent text-neutral flex items-center justify-center">
                                <span class="text-lg font-bold">{{ user.nama_kabag.charAt(0).toUpperCase() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else>
                        <div v-if="loading === true">
                            <Loading :sidebar="true"/>
                        </div>
                        <RouterLink v-if="user.id" :to="{ name: 'EditDataMagang', params: { id: user.id } }">
                            <img v-if="user.foto_profil != null" :src="user.foto_profil" alt="User Avatar" class="w-10 h-10 rounded-full object-cover object-center" />
                            <img v-else :src="profilDefault" alt="User Avatar" class="w-10 h-10 rounded-full object-cover object-center" />
                        </RouterLink>
                    </div>

                    <div>
                        <p v-if="role.nama_role == 'Admin'" class="font-semibold">{{ user.nama_admin}}</p>
                        <p v-else-if="role.nama_role == 'Kepala Bagian'" class="font-semibold">{{ user.nama_kabag}}</p>
                        <p v-else class="font-semibold">{{ user.nama_peserta}}</p>
                        <p class="text-sm text-gray-500">{{role.nama_role}}</p>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <div class="divider"></div>

            <!-- Menu Items -->
            <li v-for="side in filterMenu" :key="side.id">
                <RouterLink :to="side.url" class="flex px-2 py-3 gap-4 items-center">
                    <span :class="side.icon" class="text-xl text-gray-500"></span>
                    <span>{{ side.name }}</span>
                </RouterLink>
            </li>
            <li @click="handleLogout">
                <div class="flex justify-start px-2 py-3 gap-4">
                    <span class="pi pi-sign-out text-xl text-gray-500"></span>
                    <span>Logout</span>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { RouterLink } from 'vue-router';
import { customAPI } from '@/api';
import { useAuthStore } from '@/stores/AuthStore';
import Loading from '../Loading.vue';
import profilDefault from '@/assets/profil-default.png';

const AuthStore = useAuthStore();
const loading = ref(false);
const role = ref({});
const user = ref({});

const getProfile = async () => {
    try {
        loading.value = true;
        const { data } = await customAPI.get('/me', {
            headers: { Authorization: `Bearer ${AuthStore.token}`},
        });
        role.value = data.user.role;
        
        if (role.value.nama_role === 'Admin') {
            user.value = data.user.admin;
        } else if (role.value.nama_role === 'Peserta') {
            user.value = data.user.peserta;
        } else if (role.value.nama_role === 'Kepala Bagian') {
            user.value = data.user.kepala_bagian;
        }
    } catch (error) {
        console.error('Error fetching profile:', error);
    } finally {
        loading.value = false;
    }
}

const menu = [
    {
        id: 1,
        name: 'Home',
        icon: 'pi pi-home',
        url: '/beranda',
        admin: true,
        kabag: true,
    },
    {
        id: 2,
        name: 'Home',
        icon: 'pi pi-home',
        url: '/home',
        peserta: true,
    },
    {
        id: 3,
        name: 'Data Peserta Aktif',
        icon: 'pi pi-users',
        url: '/data-magang',
        admin: true,
        kabag: true,
    },
    {
        id: 4,
        name: 'Institusi Pendidikan',
        icon: 'pi pi-building',
        url: '/institusi-pendidikan',
        admin: true,
        kabag: true,
    },
    {
        id: 5,
        name: 'Lokasi Magang',
        icon: 'pi pi-map',
        url: '/lokasi',
        admin: true,
        kabag: true,
    },
    {
        id: 6,
        name: 'Logbook Magang',
        icon: 'pi pi-book',
        url: '/logbook',
        admin: true,
        peserta: true,
    },
    {
        id: 7,
        name: 'Presensi Magang',
        icon: 'pi pi-id-card',
        url: '/presensi',
        admin: true,
        peserta: true,
    },
    {
        id: 8,
        name: 'Arsip Data Magang',
        icon: 'pi pi-inbox',
        url: '/arsip',
        admin: true,
    },
    {
        id: 9,
        name: 'Administrasi',
        icon: 'pi pi-folder',
        url: '/administrasi',
        peserta: true,
    },
    {
        id: 10,
        name: 'Reset Password',
        icon: 'pi pi-cog',
        url: '/reset-password',
        peserta: true,
        kabag: true,
    },
];

const filterMenu = computed(() => {
    if (AuthStore.user?.role?.nama_role === 'Admin') {
        return menu.filter(sidebar => sidebar.admin);
    } else if (AuthStore.user?.role?.nama_role === 'Peserta') {
        return menu.filter(sidebar => sidebar.peserta);
    } else if (AuthStore.user?.role?.nama_role === 'Kepala Bagian') {
        return menu.filter(sidebar => sidebar.kabag);
    }
});

onMounted(() => {
    getProfile();
});

const handleLogout = () => {
    AuthStore.logoutUser();
}
</script>
