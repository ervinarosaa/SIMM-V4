<template>
    <div>
        <div class="breadcrumbs text-sm bg-base-300 rounded-full font-bold">
            <ul class="px-4">
                <li>
                    <RouterLink :to="{ name:'Beranda'}">
                        <span class="pi pi-home"></span>
                    </RouterLink>
                </li>
                <li v-if="props.title1">
                    <RouterLink :to="{ name: `${props.route1}`}">
                        {{ props.title1 }}
                    </RouterLink>
                </li>
                <li v-if="props.title2">
                    <RouterLink :to="{ name: `${props.route2}`, params: {id: `${props.id2}`}}">
                        {{ props.title2 }}
                    </RouterLink>
                </li>
            </ul>
        </div>
    </div>
</template>
<script setup>
import { RouterLink } from 'vue-router';
const props = defineProps({
    title1: {
        type: String,
    },
    route1: {
        type: String,
    },
    title2: {
        type: String,
    },
    route2: {
        type: String,
    },
    id2: {
        type: String,
    },
})
</script>