<template>
    <div class="relative">
        <main class="lg:mx-10 py-5 max-w-8xl rounded-xl min-h-[80vh]">
            <RouterView v-slot="{ Component, route }">
                <Transition name="fade" mode="out-in">
                    <component :is="Component" :key="route.path"/>
                </Transition>
            </RouterView>
        </main>
    </div>
</template>
<script setup>
    import { RouterView } from 'vue-router';
</script>

<style scoped>
    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.5s;
    }

    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }
</style>