import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/AuthStore';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BerandaView from '@/views/BerandaView.vue';
import LoginView from '@/views/LoginView.vue';
import LandingView from '@/views/LandingView.vue';
import DataMagangView from '@/views/Data Magang/DataMagangView.vue';
import DetailDataMagangView from '@/views/Data Magang/DetailDataMagangView.vue';
import AddDataMagangView from '@/views/Data Magang/AddDataMagangView.vue';
import EditDataMagangView from '@/views/Data Magang/EditDataMagangView.vue';
import InstitusiView from '@/views/Institusi/InstitusiView.vue';
import DetailInstitusiView from '@/views/Institusi/DetailInstitusiView.vue';
import LokasiMagangView from '@/views/Lokasi Magang/LokasiMagangView.vue';
import DetailLokasiMagangView from '@/views/Lokasi Magang/DetailLokasiMagangView.vue';
import LogbookView from '@/views/Logbook Magang/LogbookView.vue';
import DetailLogbookView from '@/views/Logbook Magang/DetailLogbookView.vue';
import PresensiView from '@/views/Presensi Magang/PresensiView.vue';
import ArsipView from '@/views/Arsip Data Magang/ArsipView.vue';
import DetailArsipView from '@/views/Arsip Data Magang/DetailArsipView.vue';
import EditArsipDataMagang from '@/views/Arsip Data Magang/EditArsipDataMagangView.vue';
import AdministrasiView from '@/views/Administrasi/AdministrasiView.vue';
import HomeView from '@/views/HomeView.vue';
import ResetPasswordView from '@/views/ResetPasswordView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: PublicLayout,
      children: [
        {
          path: 'beranda',
          name: 'Beranda',
          component: BerandaView,
          meta: { 
            rolesAllowed: ['Admin', 'Kepala Bagian'], 
            requiresAuth: true 
          },
        },
        {
          path: 'home',
          name: 'Home',
          component: HomeView,
          meta: { 
            rolesAllowed: ['Peserta'], 
            requiresAuth: true 
          },
        },
        {
          path: 'data-magang',
          name: 'Data Magang',
          component: DataMagangView,
          meta: { 
            rolesAllowed: ['Admin', 'Kepala Bagian'], 
            requiresAuth: true 
          },
        },
        {
          path: 'data-magang/:id',
          name: 'DetailDataMagang',
          component: DetailDataMagangView,
          meta: { 
            rolesAllowed: ['Admin', 'Kepala Bagian'], 
            requiresAuth: true 
          },
        },
        {
          path: 'data-magang/add',
          name: 'AddDataMagang',
          component: AddDataMagangView,
          meta: { 
            rolesAllowed: ['Admin'], 
            requiresAuth: true 
          },
        },
        {
          path: 'data-magang/edit/:id',
          name: 'EditDataMagang',
          component: EditDataMagangView,
          meta: { 
            requiresAuth: true 
          },
        },
        {
          path: 'institusi-pendidikan',
          name: 'Institusi Pendidikan',
          component: InstitusiView,
          meta: { 
            rolesAllowed: ['Admin', 'Kepala Bagian'], 
            requiresAuth: true 
          },
        },
        {
          path: 'institusi-pendidikan/:id',
          name: 'DetailInstitusi',
          component: DetailInstitusiView,
          meta: { 
            rolesAllowed: ['Admin', 'Kepala Bagian'], 
            requiresAuth: true 
          },
        },
        {
          path: 'lokasi',
          name: 'Lokasi Magang',
          component: LokasiMagangView,
          meta: { 
            rolesAllowed: ['Admin', 'Kepala Bagian'], 
            requiresAuth: true 
          },
        },
        {
          path: 'lokasi/:id',
          name: 'DetailLokasiMagang',
          component: DetailLokasiMagangView,
          meta: { 
            rolesAllowed: ['Admin', 'Kepala Bagian'], 
            requiresAuth: true 
          },
        },
        {
          path: 'logbook',
          name: 'Logbook Magang',
          component: LogbookView,
          meta: { 
            rolesAllowed: ['Admin', 'Peserta'], 
            requiresAuth: true 
          },
        },
        {
          path: 'logbook/:id',
          name: 'DetailLogbookMagang',
          component: DetailLogbookView,
          meta: { 
            rolesAllowed: ['Admin', 'Peserta', 'Kepala Bagian'], 
            requiresAuth: true 
          },
        },
        {
          path: 'presensi',
          name: 'Presensi Magang',
          component: PresensiView,
          meta: { 
            rolesAllowed: ['Admin', 'Peserta'], 
            requiresAuth: true 
          },
        },
        {
          path: 'arsip',
          name: 'Arsip Data Magang',
          component: ArsipView,
          meta: { 
            rolesAllowed: ['Admin'], 
            requiresAuth: true 
          },
        },
        {
          path: 'arsip/:id',
          name: 'DetailArsipMagang',
          component: DetailArsipView,
          meta: { 
            rolesAllowed: ['Admin'], 
            requiresAuth: true 
          },
        },
        {
          path: 'arsip-data-magang/edit/:id',
          name: 'EditArsipDataMagang',
          component: EditArsipDataMagang,
          meta: { 
            requiresAuth: true 
          },
        },
        {
          path: 'administrasi',
          name: 'Administrasi',
          component: AdministrasiView,
          meta: { 
            rolesAllowed: ['Peserta'], 
            requiresAuth: true 
          },
        },
        {
          path: 'reset-password',
          name: 'Reset Password',
          component: ResetPasswordView,
          meta: { 
            rolesAllowed: ['Kepala Bagian', 'Peserta'], 
            requiresAuth: true 
          },
        },
      ]
    },
    {
      path: '',
      name: 'Login',
      component: LoginView,
      meta: { requireGuest: true, isLogin:true},
    },
    
    {
      path: '/auth/reset-password',
      name: 'Forget Password',
      component: ResetPasswordView,
      meta: { requireGuest: true},
    },
  ]
})

// Guard
router.beforeEach((to, from, next) => {
  const AuthStore = useAuthStore();

  // Check requires authentication
  if (to.meta.requiresAuth && !AuthStore.token) {
    alert("Anda belum login!");
    return next({ path: '/' });
  }

  // Check authenticated
  if (to.meta.requireGuest && AuthStore.token) {
    if (AuthStore.user.role.nama_role === 'Peserta') {
      return next({ path: '/home' });
    } else {
      return next({ path: '/beranda' });
    }
  }

  // Check role
  if (to.meta.rolesAllowed && !to.meta.rolesAllowed.includes(AuthStore.user.role.nama_role)) {
    alert("You do not have permission to access this page");
    return next({ path: '/' });
  }
  next();
})

export default router
