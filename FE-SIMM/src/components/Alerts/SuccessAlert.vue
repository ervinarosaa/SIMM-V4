<template>
    
</template>

<script setup>
import { onMounted } from 'vue';
import Swal from 'sweetalert2';

const emit = defineEmits(['alertClosed']);

const props = defineProps({
    message: {
        type: String,
        required: true,
    },
});

const showSuccessAlert = async () => {
    await Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: props.message,
        confirmButtonText: 'OK',
    });
    
    emit('alertClosed');
};

// Panggil alert saat komponen ini dimuat
onMounted(() => {
    showSuccessAlert();
});
</script>