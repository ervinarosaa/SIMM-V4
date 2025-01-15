<template>
    <div v-if="props.all" class="flex w-full flex-col gap-4">
        <div class="skeleton h-36 w-full"></div>
        <div class="skeleton h-4 w-full"></div>
        <div class="skeleton h-4 w-full"></div>
        <div class="skeleton h-4 w-full"></div>
        <div class="skeleton h-4 w-full"></div>
        <div class="skeleton h-4 w-full"></div>
    </div>
    <div v-else-if="props.chart">
        <div class="skeleton h-48 w-full"></div>
    </div>
    <div v-else-if="props.text" class="flex w-full flex-col gap-4">
        <div class="skeleton h-4 w-full"></div>
        <div class="skeleton h-4 w-full"></div>
    </div>
    <div v-else-if="props.scorecard">
        <div class="skeleton h-24 w-full"></div>
    </div>
    <div v-else-if="props.sidebar">
        <div class="flex w-52 flex-col gap-4">
            <div class="flex items-center gap-4">
                <div class="skeleton w-10 h-10 shrink-0 rounded-full"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    text: {
        type: Boolean,
        default: false,
    },
    chart: {
        type: Boolean,
        default: false,
    },
    all: {
        type: Boolean, 
        default: false,
    },
    scorecard: {
        type: Boolean, 
        default: false,
    },
    sidebar: {
        type: Boolean, 
        default: false,
    },
});
</script>