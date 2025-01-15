<template>
    <div>
        <p class="font-bold text-xl lg:text-2xl mb-1">{{ props.dokumen.jenis_dokumen }}</p>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="handleClose">✕</button>
        <hr class="pb-3">
        
        <div class="pdf-container">
            <embed :src="props.dokumen.file" type="application/pdf" />
        </div>
    </div>
</template>

<script setup>
const emit = defineEmits(["closeModal"]);

const props = defineProps({
    dokumen: {
        type: Object,
        required: false, 
    },
});

const handleClose = () => {
    emit('closeModal');
}
</script>

<style scoped>
.pdf-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px; 
    overflow: hidden; 
}

embed {
    width: 100%;
    height: 100%;
    width: 80vh;
    height: 60vh; 
}
</style>