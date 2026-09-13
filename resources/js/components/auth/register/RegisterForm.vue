<script setup>
import { useRegisterForm } from '@/composables/Authenticate/useRegisterForm';
import ErrorInput from '@components/shared/ErrorInput.vue';

const props = defineProps({
  loading: Boolean,
  errors: null,
});

const emit = defineEmits(['send']);

const { formData, showFields, show, isLoading, isParecerista, onSubmit } =
  useRegisterForm(props, emit);
</script>
<template>
  <q-form class="form-register--padding" @submit.prevent="onSubmit">
    <label for="role" class="text-weight-medium text--font-13">Tipo de usuário</label>
    <q-select
      v-model="formData.role"
      class="input-color-blue input--margin-bottom"
      :options="[{ name: 'Administrador', value: 'Administrador' }]"
      option-label="name"
      option-value="value"
      filled
      placeholder="Selecione o seu tipo de usuário"
      lazy-rules
      :error="props.errors && props.errors?.role?.length > 0">
      <template #error>
        <ErrorInput :errors="props.errors.role"></ErrorInput>
      </template>
    </q-select>
    <div v-if="showFields.cpf">
      <label for="cpf" class="text-weight-medium text--font-13">CPF</label>
      <q-input
        v-model="formData.cpf"
        class="input-color-blue input--margin-bottom"
        filled
        placeholder="Digite seu CPF"
        lazy-rules
        mask="###.###.###-##"
        unmasked-value
        :error="props.errors && props.errors?.cpf?.length > 0">
        <template #error>
          <ErrorInput :errors="props.errors.cpf"></ErrorInput>
        </template>
      </q-input>
    </div>

    <div v-if="showFields.registration">
      <label for="registration" class="text-weight-medium text--font-13">Matrícula</label>
      <q-input
        v-model="formData.registration"
        class="input-color-blue input--margin-bottom"
        filled
        placeholder="Digite sua matrícula"
        lazy-rules
        mask="###########"
        :error="props.errors && props.errors?.registration?.length > 0">
        <template #error>
          <ErrorInput :errors="props.errors.registration"></ErrorInput>
        </template>
      </q-input>
    </div>

    <div v-if="showFields.nameEmail">
      <label for="name" class="text-weight-medium text--font-13">Nome Completo</label>
      <q-input
        v-model="formData.name"
        class="input-color-blue input--margin-bottom"
        filled
        placeholder="Digite seu nome"
        lazy-rules
        maxlength="80"
        :error="props.errors && props.errors?.name?.length > 0"
        :readonly="!isParecerista">
        <template #error>
          <ErrorInput :errors="props.errors.name"></ErrorInput>
        </template>
      </q-input>

      <label for="email" class="text-weight-medium text--font-13">E-mail</label>
      <q-input
        v-model="formData.email"
        class="input-color-blue input--margin-bottom"
        filled
        placeholder="Digite seu e-mail"
        lazy-rules
        :error="props.errors && props.errors?.email?.length > 0"
        :readonly="!isParecerista">
        <template #error>
          <ErrorInput :errors="props.errors.email"></ErrorInput>
        </template>
      </q-input>
    </div>

    <label for="password" class="text-weight-medium text--font-13">Senha</label>
    <q-input
      v-model="formData.password"
      :type="show.isPassword ? 'password' : 'text'"
      class="input-color-blue input--margin-bottom"
      filled
      placeholder="Digite sua senha"
      lazy-rules
      :error="props.errors && props.errors?.password?.length > 0">
      <template #error>
        <ErrorInput :errors="props.errors.password"></ErrorInput>
      </template>
      <template #append>
        <q-icon
          :name="show.isPassword ? 'visibility_off' : 'visibility'"
          class="cursor-pointer"
          @click="show.isPassword = !show.isPassword" />
      </template>
    </q-input>

    <label for="password_confirmation" class="text-weight-medium text--font-13"
      >Repetir Senha</label
    >
    <q-input
      v-model="formData.password_confirmation"
      :type="show.isPasswordConfirmation ? 'password' : 'text'"
      class="input-color-blue input--margin-bottom"
      filled
      placeholder="Confirme sua senha"
      lazy-rules
      :error="props.errors && props.errors?.password_confirmation?.length > 0">
      <template #error>
        <ErrorInput :errors="props.errors.password_confirmation"></ErrorInput>
      </template>
      <template #append>
        <q-icon
          :name="show.isPasswordConfirmation ? 'visibility_off' : 'visibility'"
          class="cursor-pointer"
          @click="show.isPasswordConfirmation = !show.isPasswordConfirmation" />
      </template>
    </q-input>

    <div class="q-mt-xs">
      <q-btn
        :loading="isLoading"
        label="Cadastrar"
        type="submit"
        color="secondary"
        class="full-width" />
    </div>
    <div class="q-mt-xs">
      <span class="text--policy">
        Ao me cadastrar, concordo com os Termos e Políticas do sistema
      </span>
    </div>
  </q-form>
</template>
