import axios from "axios";
import { useAuthStore } from "./stores/AuthStore";

export const customAPI = axios.create({
    baseURL: 'https://simm-kemenagsby.com/api/v1'
})

customAPI.interceptors.response.use(
    response => response,
    error => {
        const AuthStore = useAuthStore();

        if (error.response && error.response.status === 401) {
            AuthStore.logoutUser();
        }

        return Promise.reject(error);
    }
);