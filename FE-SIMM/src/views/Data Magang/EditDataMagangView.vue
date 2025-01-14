<template>
    <DataMagangForm 
        title="Edit" 
        :isUpdate="true" 
        :peserta="peserta" 
        :tingkatPend="tingkatpendidikan"
    />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/AuthStore';
import { customAPI } from '@/api';
import { useRoute } from 'vue-router';
import DataMagangForm from '@/components/Data Magang Component/DataMagangForm.vue';

const AuthStore = useAuthStore();
const route = useRoute();
const peserta = ref(null);
const tingkatpendidikan = ref("");

const fetchPesertaById = async () => {
    try {
        const response = await customAPI.get(`/peserta/${route.params.id}`, {
            headers: { Authorization: `Bearer ${AuthStore.token}` },
        });

        if (response.data.data) {
            peserta.value = response.data.data;
            tingkatpendidikan.value = peserta.value.institusi.tingkat_pendidikan.nama_tingkat;
        } 
    } catch (error) {
        console.log("Failed to fetch peserta by ID", error);
    }
};

onMounted(fetchPesertaById);
</script>

