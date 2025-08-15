<script setup>
import ActionButton from '@/components/shared/ActionButton.vue';
import PageTopTitle from '@/components/shared/PageTopTitle.vue';
import SearchInput from '@/components/shared/SearchInput.vue';
import TableSync from '@/components/users/TableSync.vue';
import useUser from '@/composables/User/useUser';
import useUserConfigListPage from '@/composables/User/useUserConfigListPage';
import PageWrapper from '@/pages/admin/PageWrapper.vue';
import { hasPermission } from '@utils/hasPermission';
import { USER_PERMISSION } from '@utils/permissions';

const { columns } = useUserConfigListPage();
const {
  router,
  filter,
  handleSearch,
  onEdit,
  onDelete,
  loading,
  rows,
  pagination,
  updatePagination,
  onStatus,
  confirmHandleStatus,
  handleStatus,
} = useUser();
</script>
<template>
  <PageWrapper>
    <template #title>
      <PageTopTitle>Gerencie a sua lista de usuários</PageTopTitle>
    </template>
    <template #actions>
      <div class="row justify-between">
        <div class="col-md-4">
          <SearchInput
            :value="filter"
            @update-search="handleSearch"
            @trigger-search="handleSearch" />
        </div>
        <div class="col-md-4 offset-md-4">
          <div class="column items-end">
            <ActionButton
              v-if="hasPermission([USER_PERMISSION.CREATE])"
              icon="add"
              label="Criar"
              color="accent"
              @click-action="router.push({ name: 'createUsers' })" />
          </div>
        </div>
      </div>
    </template>
    <template #content>
      <TableSync
        :loading="loading"
        :columns="columns"
        :rows="rows"
        :pagination="pagination"
        :pagination-values="pagination"
        @update-pagination="updatePagination"
        @on-status="onStatus"
        @on-edit="onEdit"
        @on-delete="onDelete" />

      <q-dialog v-model="confirmHandleStatus" persistent>
        <q-card>
          <q-card-section class="row items-center">
            <span class="q-ml-sm"
              >Deseja enviar e-mail para esse usuário notificando a ativação?</span
            >
          </q-card-section>
          <q-card-actions align="center">
            <q-btn
              v-close-popup
              outline
              label="Sim"
              color="secondary"
              @click="handleStatus(true)" />
            <q-btn
              v-close-popup
              label="Não"
              color="secondary"
              @click="handleStatus(false)" />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </template>
  </PageWrapper>
</template>
