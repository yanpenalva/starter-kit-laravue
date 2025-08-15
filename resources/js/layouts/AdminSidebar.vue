<script setup>
import useAuthenticate from '@/composables/Authenticate/useAuthenticate';
import useAuthStore from '@/store/useAuthStore';
import logoImage from '@assets/logo-125px40px.png';
import logoSmaller from '@assets/logoSmaller-50px40px.png';
import { hasPermission } from '@utils/hasPermission';
import { isActiveLink } from '@utils/isActiveLink';
import { ROLE_PERMISSION, USER_PERMISSION } from '@utils/permissions';
import { useRouter } from 'vue-router';

const props = defineProps({
  miniState: Boolean,
  isMobile: Boolean,
});

const authStore = useAuthStore();
const { logout } = useAuthenticate();
const router = useRouter();

const goToViewProfile = () => {
  router.push({ name: 'showUsers', params: { id: authStore.getUser?.id } });
};

const emit = defineEmits(['update:miniState']);
</script>

<template>
  <q-scroll-area class="sidebar-custom">
    <q-list padding class="q-mt-md q-pb-none">
      <q-item
        v-ripple
        clickable
        exact
        :to="{ name: 'adminHome' }"
        :class="isActiveLink('adminHome')">
        <q-item-section avatar class="text-white">
          <q-icon name="sym_o_space_dashboard" />
        </q-item-section>
        <q-item-section class="text-white"> Dashboard </q-item-section>
      </q-item>
    </q-list>

    <q-list v-if="hasPermission([USER_PERMISSION.LIST, ROLE_PERMISSION.LIST])">
      <q-item
        v-if="hasPermission([USER_PERMISSION.LIST])"
        v-ripple
        clickable
        exact
        :to="{ name: 'listUsers' }"
        :class="isActiveLink('listUsers')">
        <q-item-section avatar class="text-white">
          <q-icon name="sym_o_people_alt" />
        </q-item-section>
        <q-item-section class="text-white"> Usuários </q-item-section>
      </q-item>

      <q-item
        v-if="hasPermission([ROLE_PERMISSION.LIST])"
        v-ripple
        clickable
        exact
        :to="{ name: 'listRoles' }"
        :class="isActiveLink('listRoles')">
        <q-item-section avatar class="text-white">
          <q-icon name="sym_o_contacts" />
        </q-item-section>
        <q-item-section class="text-white"> Perfis </q-item-section>
      </q-item>
    </q-list>

    <div v-if="isMobile">
      <q-separator style="background-color: #ffffff2b"></q-separator>

      <q-item clickable v-ripple>
        <q-item-section avatar class="text-white">
          <q-icon name="help" />
        </q-item-section>
        <q-item-section class="text-white"> Ajuda </q-item-section>
      </q-item>

      <q-item clickable v-ripple @click="goToViewProfile">
        <q-item-section avatar class="text-white">
          <q-icon name="account_circle" />
        </q-item-section>
        <q-item-section class="text-white"> Meu Perfil </q-item-section>
      </q-item>

      <q-item clickable v-ripple @click="logout">
        <q-item-section avatar class="text-white">
          <q-icon name="exit_to_app" />
        </q-item-section>
        <q-item-section class="text-white"> Sair </q-item-section>
      </q-item>
    </div>
  </q-scroll-area>

  <div v-if="!isMobile">
    <div
      v-if="!props.miniState"
      class="logo__sidebar--active"
      style="top: 65px; left: 55px">
      <img :src="logoImage" width="124px" />
    </div>
    <div v-else class="logo__sidebar--inactive" style="top: 65px; left: 20px">
      <img :src="logoSmaller" width="50px" />
    </div>
  </div>

  <div v-if="!isMobile" class="absolute" style="top: 80px; right: -11px">
    <q-btn
      round
      dense
      size="sm"
      unelevated
      color="secondary"
      :icon="props.miniState ? 'chevron_right' : 'chevron_left'"
      @click="emit('update:miniState', !props.miniState)"></q-btn>
  </div>

  <div
    v-if="isMobile"
    class="absolute-top text-white avatar-mobile"
    style="height: 150px">
    <div class="absolute-bottom bg-transparent">
      <div size="56px" class="q-mb-sm">
        <q-icon name="account_circle" size="56px" />
      </div>
      <div class="text-weight-bold">{{ authStore.getUser?.name }}</div>
    </div>
  </div>
</template>
