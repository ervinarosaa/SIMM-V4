<template>
    <div class="relative">
        <div class="drawer">
            <input id="my-drawer-3" type="checkbox" class="drawer-toggle" />
            <div class="drawer-content flex flex-col">
                <!-- Navbar -->
                <Navbar />
                
                <!-- Main content area with Sidebar -->
                <div class="flex mt-16">
                    <div class="relative z-10">
                        <Sidebar v-if="AuthStore.token && !isLogin" />
                    </div>
                    
                    <!-- Main Content -->
                    <main class="flex-1 lg:mx-10 max-w-8xl rounded-xl min-h-[80vh]">
                        <RouterView v-slot="{ Component, route }" v-if="!route.meta.inPublicLayout">
                            <Transition name="fade" mode="out-in">
                                <component :is="Component" :key="route.path"></component>
                            </Transition>
                        </RouterView>
                    </main>
                </div>

                <!-- Footer -->
                <Footer />
            </div>
            <Sidebar v-if="AuthStore.token && !isLogin" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, RouterView } from 'vue-router';
import { useAuthStore } from '@/stores/AuthStore';
import Navbar from '@/components/layouts/Navbar.vue';
import Sidebar from '@/components/layouts/Sidebar.vue';
import Footer from '@/components/layouts/Footer.vue';

const route = useRoute();
const AuthStore = useAuthStore();
const isLogin = computed(() => route.meta.isLogin || false);

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