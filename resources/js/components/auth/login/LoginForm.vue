<script setup>
import useAuthenticate from '@/composables/Authenticate/useAuthenticate';
import useAuthStore from '@/store/useAuthStore';
import notify from '@/utils/notify';
import { onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const authStore = useAuthStore();

const showPassword = ref(false);
const isLoading = ref(false);
const { login, myProfile } = useAuthenticate();
const router = useRouter();
const route = useRoute();

onMounted(() => {
  authStore.clearExternalCredentials();
  authStore.clearLdapCredentials();
});

const auth = async () => {
  isLoading.value = true;
  try {
    await login({ ...authStore.externalCredentials });
    await myProfile();

    notify('Logado com Sucesso', 'positive');

    const { routeName, id } = route.query || {};
    router.push(routeName && id ? { name: routeName, params: { id } } : '/admin/inicio');
  } catch (error) {
    isLoading.value = false;
    throw new Error('Erro ao autenticar: ' + error.message);
  } finally {
    isLoading.value = false;
  }
};

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

watch(
  () => authStore.externalCredentials.email,
  (newEmail) => {
    const ldapDomains = ['@uefs.br', '@uefs.local', '@discente.uefs.br'];
    ldapDomains.some((domain) => newEmail.endsWith(domain))
      ? (authStore.setLdapCredentials({
          ...authStore.ldapCredentials,
          name: newEmail,
        }),
        authStore.setActiveTab('ldap'))
      : null;
  },
);
</script>

<template>
  <q-form @submit.prevent="auth">
    <span class="text-weight-medium text--font-13">Usuário ou E-mail</span>
    <q-input
      v-model="authStore.externalCredentials.email"
      class="input-color-blue input--margin-bottom"
      filled
      placeholder="E-mail">
    </q-input>

    <span class="text-weight-medium text--font-13">Senha</span>
    <q-input
      v-model="authStore.externalCredentials.password"
      class="input-color-blue input--margin-bottom"
      filled
      :type="showPassword ? 'text' : 'password'"
      placeholder="Digite sua senha">
      <template #append>
        <q-icon
          :name="showPassword ? 'visibility_off' : 'visibility'"
          class="cursor-pointer"
          @click="togglePasswordVisibility" />
      </template>
    </q-input>
    <div class="row justify-between">
      <div class="col-md-4">
        <q-checkbox
          v-model="authStore.externalCredentials.remember"
          class="text--font-13"
          label="Lembre-me" />
      </div>
      <div class="col-md-4 flex flex-center">
        <RouterLink to="/esqueci-minha-senha" class="link--style text--font-13">
          Esqueceu a senha?
        </RouterLink>
      </div>
    </div>
    <div class="q-mt-xs">
      <q-btn
        :loading="isLoading"
        :disable="isLoading"
        color="secondary"
        class="full-width"
        @click="auth">
        <span v-if="!isLoading">Entrar</span>
        <span v-else>Conectando...</span>
      </q-btn>
    </div>
  </q-form>
</template>
