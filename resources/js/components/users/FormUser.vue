<script setup>
import ErrorInput from '@/components/shared/ErrorInput.vue';
import RequiredLabel from '@/components/shared/RequiredLabel.vue';
import useUser from '@/composables/User/useUser';
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';

const props = defineProps({
  profiles: Array,
  loading: Boolean,
  user: Object,
});

const emit = defineEmits(['send']);
const route = useRoute();
const { errors } = useUser();

const formData = ref({
  send_random_password: false,
  role_slug: null,
  active: 0,
});

onMounted(() => {
  formData.value.name = props.user?.name || '';
  formData.value.cpf = props.user?.cpf || '';
  formData.value.email = props.user?.email || '';
  formData.value.role = props.user?.roles?.[props.user.roles.length - 1] || null;
  formData.value.active = props.user?.active ? 1 : 0;
});

const onSubmit = async () => {
  emit('send', {
    email: formData.value.email,
    name: formData.value.name,
    cpf: formData.value.cpf,
    password: formData.value.password,
    role_id: formData.value.role?.id,
    role_slug: formData.value.role?.slug,
    active: formData.value.active ?? 0,
    send_random_password: formData.value.send_random_password,
  });
};

watch([formData, () => formData.value.send_random_password], () => {
  if (formData.value.send_random_password) formData.value.password = null;
});
</script>

<template>
  <q-form class="q-gutter-md" @submit.prevent="onSubmit">
    <div class="row q-col-gutter-md">
      <div class="col-md-6">
        <RequiredLabel required>Nome</RequiredLabel>
        <q-input
          v-model="formData.name"
          filled
          placeholder="Digite o nome"
          bottom-slots
          lazy-rules
          :error="errors?.name?.length > 0">
          <template #error>
            <ErrorInput :errors="errors.name" />
          </template>
        </q-input>
      </div>
      <div class="col-md-6">
        <RequiredLabel required>CPF</RequiredLabel>
        <q-input
          v-model="formData.cpf"
          filled
          placeholder="Digite o CPF"
          mask="###.###.###-##"
          bottom-slots
          lazy-rules
          :error="errors?.cpf?.length > 0">
          <template #error>
            <ErrorInput :errors="errors.cpf" />
          </template>
        </q-input>
      </div>
    </div>

    <div class="row q-col-gutter-md">
      <div class="col-md-12">
        <RequiredLabel required>Email</RequiredLabel>
        <q-input
          v-model="formData.email"
          filled
          type="email"
          placeholder="Digite o email"
          bottom-slots
          lazy-rules
          :error="errors?.email?.length > 0">
          <template #error>
            <ErrorInput :errors="errors.email" />
          </template>
        </q-input>
      </div>
    </div>

    <div class="row q-col-gutter-md">
      <div class="col-md-12">
        <RequiredLabel required>Senha</RequiredLabel>
        <q-input
          v-model="formData.password"
          filled
          type="password"
          placeholder="Digite a senha"
          bottom-slots
          lazy-rules
          :readonly="formData.send_random_password"
          :error="errors?.password?.length > 0">
          <template #error>
            <ErrorInput :errors="errors.password" />
          </template>
        </q-input>
      </div>
    </div>

    <div v-if="route.name !== 'editUsers'" class="row q-col-gutter-md">
      <div class="col-md-12">
        <q-checkbox
          v-model="formData.send_random_password"
          label="Enviar senha aleatória por email" />
      </div>
    </div>

    <div class="row q-col-gutter-md">
      <div class="col-md-6">
        <RequiredLabel required>Perfil</RequiredLabel>
        <q-select
          v-model="formData.role"
          :style="{ width: '100%' }"
          :options="props.profiles"
          option-label="name"
          option-value="id"
          filled
          label="Selecione um perfil"
          bottom-slots
          lazy-rules
          :error="errors?.role?.length > 0">
          <template #no-option>
            <q-item>
              <q-item-section class="text-italic text-grey">
                Nenhuma opção disponível
              </q-item-section>
            </q-item>
          </template>
          <template #error>
            <ErrorInput :errors="errors.role" />
          </template>
        </q-select>
      </div>
      <div class="col-md-6 d-flex align-items-end">
        <q-toggle
          v-model="formData.active"
          :true-value="1"
          :false-value="0"
          class="text-weight-bold"
          name="active"
          label="Ativo"
          :style="{ width: '100%' }" />
      </div>
    </div>

    <div class="q-mt-lg q-gutter-sm">
      <q-btn
        :loading="props.loading"
        class="text-weight-bold"
        label="Salvar"
        type="submit"
        color="primary" />
      <q-btn
        flat
        class="text-weight-bold"
        label="Voltar"
        type="button"
        color="primary"
        :to="{ name: 'listUsers' }" />
    </div>
  </q-form>
</template>
