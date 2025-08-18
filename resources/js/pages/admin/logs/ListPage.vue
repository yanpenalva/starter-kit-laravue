<script setup>
import PageWrapper from '@/pages/admin/PageWrapper.vue';
import PageTopTitle from '@/components/shared/PageTopTitle.vue';
import SearchInput from '@/components/shared/SearchInput.vue';
import TableSync from '@/components/users/TableSync.vue';
import useLog from '@/composables/Log/useLog';
import useLogConfigListPage from '@/composables/Log/useLogConfigListPage';
import { hasPermission } from '@utils/hasPermission';
import { LOG_PERMISSION } from '@utils/permissions';
import { ref } from 'vue';

const { columns } = useLogConfigListPage();
const { filter, handleSearch, loading, rows, pagination, listPage, updatePagination, viewLog } = useLog();

const showModal = ref(false);
const selected = ref(null);

const openModal = async (row) => {
  selected.value = await viewLog(row.id);
  showModal.value = true;
};
</script>

<template>
  <PageWrapper>
    <template #title>
      <PageTopTitle>Logs</PageTopTitle>
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
          <div class="column items-end"></div>
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
        @on-consult="openModal" />

      <q-dialog v-model="showModal" persistent>
        <q-card style="min-width:640px;">
          <q-card-section>
            <div class="row items-center q-mb-sm">
              <div class="col">
                <h6>{{ selected?.logName }} — {{ selected?.eventPt }}</h6>
                <div class="text-subtitle2">{{ selected?.description }}</div>
              </div>
              <div class="col-auto">
                <div>{{ selected?.createdAt }}</div>
              </div>
            </div>
            <q-separator />
            <div class="q-mt-md">
              <div><strong>Executado por:</strong> {{ selected?.causer?.name || '-' }}</div>
              <div><strong>Afetado:</strong> {{ selected?.subject?.name || '-' }}</div>
            </div>
            <q-separator class="q-my-md" />
            <div>
              <h6>Propriedades</h6>
              <pre style="white-space:pre-wrap;">{{ JSON.stringify(selected?.properties, null, 2) }}</pre>
            </div>
          </q-card-section>
          <q-card-actions align="right">
            <q-btn label="Fechar" color="primary" v-close-popup @click="showModal = false" />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </template>
  </PageWrapper>
</template>
