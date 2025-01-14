<template>
    <div></div>
</template>

<script setup>
import Swal from 'sweetalert2';
import { defineEmits } from 'vue';

const emit = defineEmits(['confirmDelete', 'cancelDelete']);

const showDeleteConfirmation = async () => {
    const result = await Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data ini akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
    });

    if (result.isConfirmed) {
        emit('confirmDelete');
    } else {
        emit('cancelDelete');
    }
};

showDeleteConfirmation();
</script>
