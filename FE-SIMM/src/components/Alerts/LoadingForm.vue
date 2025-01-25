<template>
    
</template>

<script setup>
import { watch } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    title: {
        type: String,
        default: 'Memproses...',
    },
    text: {
        type: String,
        default: 'Harap tunggu',
    },
    show: {
        type: Boolean,
        required: true,
    },
});

watch(
    () => props.show,
    (newValue) => {
        if (newValue) {
            Swal.fire({
                title: props.title,
                text: props.text,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading(),
            });
        } else {
            Swal.close();
        }
    }
);
</script>
