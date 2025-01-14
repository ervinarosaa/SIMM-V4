<template>
    <div class="flex items-center justify-center mt-10">
        <div class="mt-5 mx-3 lg:mx-0 border border-slate-200 shadow-xl rounded-lg w-[350px] lg:w-[500px]">
            <div class="flex items-center justify-center mx-4 mt-2 p-2 rounded-lg font-bold text-xl ">
                Reset Password
            </div>

            <hr class="mx-4">

            <!-- Step 1: Input Email -->
            <form v-if="step === 1" @submit.prevent="sendOtp" class="flex flex-col gap-4 mx-4 my-6">
                <div class="flex flex-col gap-1">
                    <label for="Email" class="text-sm font-bold">
                        Email
                        <span class="text-red-500">*</span>
                    </label>
                    <label class="input input-bordered flex items-center h-[30px]">
                        <input v-if="AuthStore.token" type="email" class="grow" v-model="email" required readonly />
                        <input v-else type="email" class="grow" v-model="email" required />
                    </label>
                </div>
                <input type="submit" value="Kirim OTP" class="btn bg-green-700 text-white btn-sm px-4">
            </form>

            <!-- Step 2: Input OTP and Password -->
            <form v-if="step === 2" @submit.prevent="handleSubmit" class="flex flex-col gap-4 mx-4 my-6">
                <div class="flex flex-col gap-1">
                    <label for="OTP" class="text-sm font-bold">
                        Masukkan OTP
                        <span class="text-red-500">*</span>
                    </label>
                    <label class="input input-bordered flex items-center h-[30px]">
                        <input type="text" class="grow" v-model="otp" required />
                    </label>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="Password Baru" class="text-sm font-bold">
                        Password Baru
                        <span class="text-red-500">*</span>
                    </label>
                    <label class="input input-bordered flex items-center h-[30px]">
                        <input type="password" class="grow" v-model="new_password" required />
                    </label>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="Konfirmasi Password Baru" class="text-sm font-bold">
                        Konfirmasi Password Baru
                        <span class="text-red-500">*</span>
                    </label>
                    <label class="input input-bordered flex items-center h-[30px]">
                        <input type="password" class="grow" v-model="confirm_password" required />
                    </label>
                </div>
                <input type="submit" value="Reset Password" class="btn bg-green-700 text-white btn-sm px-4">
                <button type="button" @click="sendOtp" class="btn bg-black text-white btn-sm px-4">
                    Kirim Ulang OTP
                </button>
            </form>

            <!-- Success and Failed Alert -->
            <SuccessAlert v-if="isSuccess" :message="successMessage" />
            <FailedAlert v-if="isFailed" :message="failedMessage" />
            <WarningAlert v-if="isWarning" :message="warningMessage" />
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/AuthStore';
import { ref, onMounted } from 'vue';
import { customAPI } from '@/api';
import { useRouter } from 'vue-router';
import SuccessAlert from '@/components/Alerts/SuccessAlert.vue';
import FailedAlert from '@/components/Alerts/FailedAlert.vue';
import WarningAlert from '@/components/Alerts/WarningAlert.vue';

// State untuk mengontrol alert
const isSuccess = ref(false);
const isFailed = ref(false);
const isWarning = ref(false);
const successMessage = ref('');
const failedMessage = ref('');
const warningMessage = ref('');

const AuthStore = useAuthStore();
const router = useRouter();

const step = ref(1);
const otp = ref("");
const email = ref("");
const new_password = ref("");
const confirm_password = ref("");

const getMe = async () => {
    try {
        const { data } = await customAPI.get('/me', {
            headers: { Authorization: `Bearer ${AuthStore.token}`},
        });

        email.value = data.user.email;
    } catch (error) {
        isFailed.value = true;
        failedMessage.value = 'Gagal mendapatkan data email';
    }
}

const resetAlerts = () => {
    isSuccess.value = false;
    isFailed.value = false;
    isWarning.value = false;
};

const sendOtp = async () => {
    resetAlerts();
    try {
        await customAPI.post('/auth/generate-otp-code', { email: email.value });
            step.value = 2; // Lanjut ke langkah berikutnya
            isSuccess.value = true;
            successMessage.value = 'Kode OTP berhasil dikirim ke email Anda.';
    } catch (error) {
        isFailed.value = true;
        if (error.response && error.response.data && error.response.data.message) {
            failedMessage.value = error.response.data.message;
        } else {
            failedMessage.value = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
};

// Fungsi untuk menghandle form submit
const handleSubmit = async () => {
    resetAlerts();
    if (new_password.value !== confirm_password.value) {
        isWarning.value = true;
        warningMessage.value = 'Password baru dan konfirmasi tidak cocok.';
        return;
    }

    try {
        await customAPI.post('/auth/reset-password', {
            email: email.value,
            otp: otp.value,
            new_password: new_password.value,
            confirm_password: confirm_password.value,
        });

        isSuccess.value = true;
        successMessage.value = 'Password berhasil diubah!';
        if (AuthStore.token && AuthStore.user.role.nama_role === 'Kepala Bagian') {
            router.push({ name: "Beranda" });
        } else if (AuthStore.token && AuthStore.user.role.nama_role === 'Peserta') {
            router.push({ name: "Home" });
        } else {
            router.push({ name: "Login" });
        }
    } catch (error) {
        isFailed.value = true;
        if (error.response && error.response.data && error.response.data.message) {
            failedMessage.value = error.response.data.message;
        } else {
            failedMessage.value = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
};

onMounted(() => {
    if (AuthStore.token) {
        getMe();
    }
});
</script>
