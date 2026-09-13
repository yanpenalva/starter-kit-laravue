<script setup>
import AuthComponent from '@/components/AuthComponent.vue';
import LoginForm from '@/components/auth/login/LoginForm.vue';
import LdapLoginForm from '@/components/auth/login/LdapLoginForm.vue';
import useAuthStore from '@/store/useAuthStore';
import { watch } from 'vue';

const authStore = useAuthStore();

if (authStore.activeTab === 'ldap') {
  authStore.setActiveTab('external');
}

watch(
  () => authStore.activeTab,
  (tab) => {
    if (tab === 'ldap') {
      authStore.setActiveTab('external');
      return;
    }

    authStore.clearCredentialsOnTabChange();
  },
);
</script>

<template>
  <AuthComponent>
    <q-tabs
      v-model="authStore.activeTab"
      indicator-color="secondary"
      active-color="black"
      text-color="black">
      <div class="institutional-tab-wrapper">
        <q-tab name="ldap" label="Login Institucional" disable />
        <q-tooltip max-width="360px" anchor="bottom middle" self="top middle">
          <div class="text-body2">
            Login institucional indisponível no momento. Esta integração depende de um
            provedor AD/LDAP ou SSO.
            <br /><br />
            Possíveis opções: <b>directorytree/ldaprecord-laravel</b> ou
            <b>adldap2/adldap2-laravel</b> para conexão direta com LDAP/Active Directory;
            ou <b>socialiteproviders/keycloak</b> com OAuth 2.0/OpenID Connect para SSO
            via Keycloak.
            <br /><br />
            É necessário configurar servidor, TLS, credenciais de bind, realm/client e
            o mapeamento dos atributos do usuário antes de habilitar o acesso.
          </div>
        </q-tooltip>
      </div>
      <q-tab name="external" label="Login Público Externo" />
    </q-tabs>

    <q-tab-panels v-model="authStore.activeTab" animated>
      <q-tab-panel name="ldap">
        <LdapLoginForm />
      </q-tab-panel>
      <q-tab-panel name="external">
        <LoginForm />
      </q-tab-panel>
    </q-tab-panels>
  </AuthComponent>
</template>

<style scoped>
.institutional-tab-wrapper {
  display: inline-flex;
}
</style>
