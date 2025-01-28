import { ref } from 'vue';
import { defineStore } from 'pinia';
import { useRouter } from 'vue-router';
import { customAPI } from '@/api';
import Swal from 'sweetalert2';

export const useAuthStore = defineStore('AuthStore', () => {
    const router = useRouter();

    const safeParse = (item) => {
        try {
            return JSON.parse(item);
        } catch (error) {
            return null;
        }
    };

    const token = ref(safeParse(localStorage.getItem('token')));
    const user = ref(safeParse(localStorage.getItem('user')));

    const setToken = (tokenValue) => {
        token.value = tokenValue;
        localStorage.setItem('token', JSON.stringify(tokenValue));
    };

    const setUser = (userValue) => {
        user.value = userValue;
        localStorage.setItem('user', JSON.stringify(userValue));
    };

    const loginUser = async (inputData) => {
        try {
            const { email, password } = inputData;
            const { data } = await customAPI.post('/auth/login', 
                { email, password }
            );
            const { token: tokenUser, user: userData } = data;

            setToken(tokenUser);
            setUser(userData);

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "Login berhasil!"
            });

            if (userData.role.nama_role === 'Peserta') {
                router.push({ name: 'Home' });
            } else {
                router.push({ name: 'Beranda' });
            }
        } catch (error) {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "error",
                title: error.response.data.message
            });
        }
    };

    const getMe = async () => {
        try {
            const response = await customAPI.get('/me', {
                headers: { Authorization: `Bearer ${token.value}`},
            });

            const { user: userData } = response.data;

            setUser(userData);
        } catch (error) {
            console.log(error);
        }
    };

    const isLogout = ref(false);

    const logoutUser = async () => {
        if (isLogout.value || !token.value) return;

        isLogout.value = true;

        try {
            await customAPI.post('auth/logout', null, {
                headers: { Authorization: `Bearer ${token.value}`},
            });
        } catch (error) {
            console.error('Error during logout:', error);
        } finally {
            localStorage.removeItem('token');
            localStorage.removeItem('user');

            token.value = null;
            user.value = null;

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "Logout berhasil!"
            });
            isLogout.value = false;

            router.push({ name: 'Login' });
        }
    };

    return {
        token,
        user,
        loginUser,
        getMe,
        logoutUser,
        setToken,
        setUser,
    }
})