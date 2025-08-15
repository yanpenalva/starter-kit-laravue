<script setup>
import useRole from '@/composables/Roles/useRole';
import usePermissionStore from '@/store/usePermissionStore';
import ErrorInput from '@components/shared/ErrorInput.vue';
import { onBeforeMount, onUnmounted } from 'vue';

const {
  role,
  errors,
  loading,
  selectedPermissionIds,
  shouldBlockSelectPermission,
  togglePermission,
  saveRole,
  initializeRoleData,
  cleanupRoleData,
  isPermissionGroupFullySelected,
  togglePermissionGroup,
} = useRole();

const permissionStore = usePermissionStore();

onBeforeMount(async () => {
  await initializeRoleData();
});

onUnmounted(() => {
  cleanupRoleData();
});
</script>

<template>
  <q-form v-if="role">
    <div>
      <label for="name" class="text-weight-bold">
        Nome do Perfil <span class="text-negative">*</span>
      </label>
      <q-input
        v-model="role.name"
        filled
        placeholder="Campo obrigatório."
        bottom-slots
        lazy-rules
        :error="errors?.name?.length > 0">
        <template #error>
          <ErrorInput :errors="errors.name" />
        </template>
      </q-input>
    </div>

    <div>
      <label for="description" class="text-weight-bold">Descrição</label>
      <q-input
        v-model="role.description"
        filled
        placeholder="Texto não obrigatório."
        bottom-slots
        :error="errors?.description?.length > 0">
        <template #error>
          <ErrorInput :errors="errors.description" />
        </template>
      </q-input>
    </div>

    <div v-if="!shouldBlockSelectPermission" class="q-mt-md">
      <label class="text-weight-bold">Permissões</label>
      <div class="q-mt-sm q-gutter-y-sm">
        <q-card
          flat
          bordered
          v-for="permissionGroup in permissionStore.getPermissions"
          :key="permissionGroup.group">
          <q-expansion-item expand-separator :default-opened="true">
            <template #header>
              <q-item-section avatar>
                <q-icon name="bookmark" color="blue-10" />
              </q-item-section>
              <q-item-section>
                {{ permissionGroup.label }}
              </q-item-section>
            </template>

            <div class="q-mb-sm">
              <q-checkbox
                :model-value="isPermissionGroupFullySelected(permissionGroup)"
                label="Selecionar todas as permissões deste grupo"
                class="text-weight-bold"
                @update:model-value="togglePermissionGroup(permissionGroup)" />
            </div>

            <div class="row q-col-gutter-md">
              <div
                v-for="permissionItem in permissionGroup.permissions"
                :key="permissionItem.value"
                class="col-12 col-sm-6 col-md-4">
                <q-checkbox
                  :model-value="selectedPermissionIds.includes(permissionItem.value)"
                  :label="permissionItem.label"
                  :disable="shouldBlockSelectPermission"
                  @update:model-value="togglePermission(permissionItem.value)" />
              </div>
            </div>
          </q-expansion-item>
        </q-card>
      </div>
    </div>

    <div class="q-mt-lg q-gutter-sm">
      <q-btn
        class="text-weight-bold"
        label="Salvar"
        type="submit"
        color="secondary"
        :loading="loading"
        @click.prevent="saveRole()" />

      <q-btn
        flat
        class="text-weight-bold"
        label="Voltar"
        color="primary"
        :to="{ name: 'listRoles' }" />
    </div>
  </q-form>
</template>
