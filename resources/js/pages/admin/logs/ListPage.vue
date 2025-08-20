<script setup>
import LogDetailModal from '@/components/logs/LogDetailModal.vue';
import TableSync from '@/components/logs/TableSync.vue';
import PageTopTitle from '@/components/shared/PageTopTitle.vue';
import SearchInput from '@/components/shared/SearchInput.vue';
import useLog from '@/composables/Log/useLog';
import useLogConfigListPage from '@/composables/Log/useLogConfigListPage';
import PageWrapper from '@/pages/admin/PageWrapper.vue';

const { columns } = useLogConfigListPage();
const {
  filter,
  handleSearch,
  loading,
  rows,
  pagination,
  updatePagination,
  showModal,
  selectedLog,
  onConsult,
  closeModal,
} = useLog();
</script>

<template>
  <PageWrapper>
    <template #title>
      <PageTopTitle>Histórico de registros da aplicação</PageTopTitle>
    </template>

    <template #actions>
      <div class="row justify-between items-center">
        <div class="col-md-4 row items-center no-wrap">
          <SearchInput
            :value="filter"
            @update-search="handleSearch"
            @trigger-search="handleSearch" />

          <q-icon
            name="help_outline"
            size="20px"
            color="grey-7"
            class="q-ml-sm cursor-pointer">
            <q-tooltip
              class="bg-grey-9 text-white text-body2"
              anchor="top middle"
              self="bottom middle">
              Pesquise por <b>descrição</b>, <b>executado por</b>, <b>afetado</b> ou por
              <b>data</b>.<br />
              Datas devem ser no formato <code>DD/MM/YYYY</code>.
            </q-tooltip>
          </q-icon>
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
        @update-pagination="updatePagination"
        @on-consult="onConsult" />

      <LogDetailModal
        v-model="showModal"
        :log="selectedLog"
        @update:model-value="closeModal" />
    </template>
  </PageWrapper>
</template>
