<template>
    <div class="mx-5 lg:mx-auto mt-10 card max-w-[140vh] lg:h-min-[405px] bg-base-200 rounded-xl">
        <div class="card-body items-center text-center p-0 grid lg:grid-cols-2">
            <div class="hidden lg:flex justify-center items-center">
                <img :src="loginImage" alt="Logo Kementerian Agama" class="rounded-l-xl h-full object-cover">
            </div>
            <div class="flex flex-col justify-center px-10">
                <div class="hidden lg:flex justify-center">
                    <h1 class="text-center text-3xl font-bold pb-5">Login</h1>
                </div>
                <div class="lg:hidden flex flex-col justify-center text-center px-10 py-4">
                    <div class="flex justify-center items-center">
                        <img :src="logo" alt="Logo Kementerian Agama" class="h-[60px] object-cover">
                    </div>
                    <h1 class="text-sm md:text-lg text-gray-700 font-bold">SISTEM INFORMASI MANAJEMEN MAGANG</h1>
                    <h1 class="hidden md:flex text-lg text-gray-700 font-bold justify-center">KEMENTERIAN AGAMA KOTA SURABAYA</h1>
                </div>
                <form @submit.prevent="handleAuth" method="post" class="space-y-5 lg:py-1 pb-10">
                    <label class="input input-bordered flex items-center gap-4">
                        <span class="pi pi-envelope text-gray-500"></span>
                        <input type="email" required class="grow focus:outline-none" 
                            placeholder="Masukkan Email" v-model="userInput.email" 
                        />
                    </label>
                    <label class="input input-bordered flex items-center gap-4">
                        <span class="pi pi-key text-gray-500"></span>
                        <input type="password" required class="grow focus:outline-none" 
                        placeholder="Masukkan Password" v-model="userInput.password" 
                        />
                    </label>
                    <div class="text-end">
                        <RouterLink to="/auth/reset-password" class="text-sm text-blue-500 hover:underline">Reset password?</RouterLink>
                    </div>
                    <input type="submit" value="Login" class="btn bg-green-700 text-lg font-semibold text-white btn-block mt-5">
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from "@/stores/AuthStore";
import { reactive } from "vue";
import loginImage from '@/assets/login.png';
import logo from '@/assets/logo.png';

const AuthStore = useAuthStore();

const userInput = reactive({
    email: "",
    password: "",
});

const handleAuth = () => {
    AuthStore.loginUser(userInput);
};
</script>
