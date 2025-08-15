<script setup>
defineProps({
  user: Object,
});
</script>

<template>
  <h6 class="text-weight-bold text-h6 q-mb-sm" style="color: #106db0">Dados do Perfil</h6>

  <q-separator color="#BBBBBC" spaced />

  <div class="q-mt-md">
    <div class="profile-field">
      <p class="field-label">Nome Completo</p>
      <p class="field-value">{{ user?.name || 'Não informado' }}</p>
    </div>

    <div class="profile-field">
      <p class="field-label">E-mail Institucional</p>
      <p class="field-value">{{ user?.email || 'Não informado' }}</p>
    </div>

    <div class="profile-field">
      <p class="field-label">CPF</p>
      <p class="field-value">{{ user?.cpf || 'Não informado' }}</p>
    </div>

    <div class="profile-field">
      <p class="field-label">Perfil de Acesso</p>
      <p class="field-value">
        {{ user?.roles?.length ? user.roles[0].name : 'Não definido' }}
      </p>
    </div>
  </div>
</template>

<style scoped>
.profile-field {
  margin-bottom: 16px;
}

.field-label {
  font-weight: bold;
  color: #333;
  margin-bottom: 2px;
}

.field-value {
  color: #555;
}

.q-separator {
  border-radius: 2px;
}
</style>
