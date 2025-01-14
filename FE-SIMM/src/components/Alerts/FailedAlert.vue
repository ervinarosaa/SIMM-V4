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

const showFailedAlert = async () => {
    await Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: props.message,
        confirmButtonText: 'OK',
    });

    emit('alertClosed');
};

// Panggil alert saat komponen ini dimuat
onMounted(() => {
    showFailedAlert();
});
</script>
