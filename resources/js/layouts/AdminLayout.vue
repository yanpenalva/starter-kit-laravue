<script setup>
import useAuthenticate from '@/composables/Authenticate/useAuthenticate';
import { useQuasar } from 'quasar';
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AdminHeaderLayout from './AdminHeaderLayout.vue';
import AdminSidebar from './AdminSidebar.vue';

const drawer = ref(true);
const miniState = ref(false);
const route = useRoute();
const router = useRouter();
const { myProfile } = useAuthenticate();
const $q = useQuasar();

const isMobile = computed(() => $q.screen.width <= 1024);

onMounted(async () => {
  await myProfile();
});

const handleBack = () => {
  if (['createUsers', 'showUsers', 'editUsers'].includes(route?.name)) {
    router.push({ name: 'listUsers' });
    return;
  }
  if (['createRoles', 'showRole', 'editRoles'].includes(route?.name)) {
    router.push({ name: 'listRoles' });
    return;
  }
  router.push({ name: 'adminHome' });
};

const hasBackButton = computed(() => {
  return ['Usuários', 'Perfis'].includes(route?.meta?.module);
});
</script>

<template>
  <!-- Desktop Layout -->
  <q-layout v-if="!isMobile" view="lHh Lpr lff" container class="layout__admin">
    <AdminHeaderLayout>
      <div v-if="route?.meta?.module && route?.meta?.icon" class="desktop-only">
        <div class="row items-center">
          <q-btn
            v-if="hasBackButton"
            dense
            flat
            round
            class="arrow-back"
            @click="handleBack">
            <q-icon name="arrow_back" size="sm" />
          </q-btn>

          <div class="page-icon-style" :style="{ backgroundColor: route?.meta.iconBg }">
            <q-icon
              :name="route?.meta?.icon"
              class="icon-style"
              :style="{ color: route?.meta.iconColor }" />
          </div>

          <span class="text-h4">
            {{ route?.meta?.module }}
          </span>
        </div>
      </div>
      <q-toolbar-title></q-toolbar-title>
    </AdminHeaderLayout>

    <q-drawer
      v-model="drawer"
      show-if-above
      :mini="miniState"
      :mini-width="90"
      :width="238"
      bordered
      :breakpoint="400"
      @mouseover="miniState = miniState ? false : miniState"
      @mouseleave="miniState = drawer ? miniState : true"
      :class="$q.dark.isActive ? 'bg-primary' : 'bg-primary'">
      <AdminSidebar v-model:miniState="miniState"></AdminSidebar>
    </q-drawer>

    <q-page-container class="bg__grey--style">
      <q-page padding class="q-mt-md">
        <router-view></router-view>
      </q-page>
    </q-page-container>
  </q-layout>

  <!-- Mobile Layout -->
  <q-layout
    v-else
    view="hHh Lpr lff"
    container
    style="height: 300px"
    class="layout__admin header-mobile">
    <q-header elevated class="bg-white">
      <q-toolbar class="q-pl-md q-pr-md row justify-between items-center">
        <div v-if="route?.meta?.module && route?.meta?.icon" class="text-black">
          <div class="row items-center">
            <q-btn
              v-if="hasBackButton"
              dense
              flat
              round
              class="arrow-back"
              @click="handleBack">
              <q-icon name="arrow_back" size="sm" />
            </q-btn>
            <q-icon :name="route?.meta?.icon" size="sm" class="q-mr-sm" />
            <span class="text-name-page-mobile">
              {{ route?.meta?.module }}
            </span>
          </div>
        </div>
        <q-btn flat @click="drawer = !drawer" round dense icon="menu" class="text-dark" />
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="drawer"
      show-if-above
      :mini="miniState"
      @mouseenter="miniState = false"
      @mouseleave="miniState = true"
      mini-to-overlay
      :mini-width="120"
      :width="238"
      :breakpoint="500"
      bordered
      :class="$q.dark.isActive ? 'bg-primary' : 'bg-primary'">
      <AdminSidebar v-model:miniState="miniState" :isMobile="isMobile"></AdminSidebar>
    </q-drawer>

    <q-page-container class="bg__grey--style">
      <q-page padding class="q-mt-md">
        <router-view></router-view>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<style lang="css">
@import '@css/admin.scss';
</style>
