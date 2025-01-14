<template>
    <div class="navbar bg-base-100 shadow-lg fixed top-0 w-full z-20">
        <!-- Toggle for small screens -->
        <div class="flex-none lg:hidden" v-if="AuthStore.token">
            <label for="my-drawer-3" aria-label="open sidebar" class="btn btn-square btn-ghost">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    class="inline-block h-6 w-6 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </label>
        </div>

        <div class="flex-1">
            <RouterLink to="/">
                <div class="flex gap-3 items-center">
                    <img :src="logo" alt="Logo Kementerian Agama" class="h-[30px] w-[40px] lg:h-[45px] lg:w-[45px]">
                    <h1 class="text-xs lg:text-xl font-bold">Sistem Informasi Manajemen Magang | Kemenag Surabaya</h1>
                </div>
            </RouterLink>
            
        </div>
        <div class="flex-none">
            <div class="tooltip tooltip-bottom" data-tip="Logout" v-if="AuthStore.token">
                <div class="btn btn-sm btn-ghost text-sm" @click="handleLogout">
                    <span class="pi pi-sign-out"></span>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import logo from '@/assets/logo.png';
import { useAuthStore } from '@/stores/AuthStore';

const AuthStore = useAuthStore();
const handleLogout = () => {
    AuthStore.logoutUser();
}
</script>