<script setup>
import useAuthenticate from '@/composables/Authenticate/useAuthenticate';
import useLDAPAuthenticate from '@/composables/Authenticate/useLDAPAuthenticate';
import useAuthStore from '@/store/useAuthStore';
import notify from '@/utils/notify';
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const authStore = useAuthStore();

const showPassword = ref(false);
const isLoading = ref(false);

const { myProfile } = useAuthenticate();
const { ldapLogin } = useLDAPAuthenticate();
const router = useRouter();
const route = useRoute();

const auth = async () => {
  isLoading.value = true;
  try {
    await ldapLogin({ ...authStore.ldapCredentials });
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
</script>

<template>
  <q-form @submit.prevent="auth">
    <span class="text-weight-medium text--font-13">Domínio ou e-mail institucional</span>
    <q-input
      v-model="authStore.ldapCredentials.name"
      class="input-color-blue input--margin-bottom"
      filled
      placeholder="Insira seu domínio ou e-mail institucional">
    </q-input>

    <span class="text-weight-medium text--font-13">Senha</span>
    <q-input
      v-model="authStore.ldapCredentials.password"
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
          v-model="authStore.ldapCredentials.remember"
          class="text--font-13"
          label="Lembre-me" />
      </div>
      <div class="col-md-4 flex flex-center">
        <span class="text-info text--font-13">Consulte o suporte da sua instituição.</span>
      </div>
    </div>
    <div class="q-mt-xs">
      <q-btn
        :loading="isLoading"
        :disable="isLoading"
        color="secondary"
        class="full-width"
        type="submit">
        <span v-if="!isLoading">Entrar</span>
        <span v-else>Conectando...</span>
      </q-btn>
    </div>
  </q-form>
</template>
