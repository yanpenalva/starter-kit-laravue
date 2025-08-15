<script setup>
import useAuthenticate from '@/composables/Authenticate/useAuthenticate';
import useAuthStore from '@/store/useAuthStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const { logout } = useAuthenticate();
const router = useRouter();

const goToViewProfile = () => {
  router.push({ name: 'showUsers', params: { id: authStore.getUser?.id } });
};
</script>

<template>
  <q-header elevated class="header-custom">
    <q-toolbar class="bg-white text-grey-8 q-pt-xs q-pb-xs toolbar-custom">
      <slot></slot>
      <q-btn
        class="question-mark"
        round
        dense
        size="sm"
        unelevated
        color="secondary"
        icon="question_mark">
        <q-tooltip anchor="top middle" self="bottom middle">
          Para mais dúvidas, entre em contato com o administrador do sistema.
        </q-tooltip>
      </q-btn>

      <q-btn-dropdown
        class="q-pl-xs dropdown__header--style"
        color="primary"
        flat
        round
        dense
        size="md"
        :label="authStore.getUser?.name || 'Conta'"
        icon="account_circle">
        <q-list>
          <q-item v-close-popup clickable @click="goToViewProfile">
            <q-item-section avatar>
              <q-icon name="person" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Meu Perfil</q-item-label>
            </q-item-section>
          </q-item>

          <q-item v-close-popup clickable @click="logout">
            <q-item-section avatar>
              <q-icon name="logout" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Sair</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-btn-dropdown>
    </q-toolbar>
  </q-header>
</template>
