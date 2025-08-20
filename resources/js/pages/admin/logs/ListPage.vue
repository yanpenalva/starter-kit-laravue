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
      <div class="row justify-between">
        <div class="col-md-4">
          <SearchInput
            :value="filter"
            @update-search="handleSearch"
            @trigger-search="handleSearch" />

          <small class="text-grey-7 q-mt-xs">
            🔍 Pesquise por <strong>descrição</strong>, <strong>executado por</strong>,
            <strong>afetado</strong> ou por <strong>data</strong> (ex:
            <code>DD/MM/AAAA</code>).
          </small>
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
