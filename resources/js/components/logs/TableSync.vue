<script setup>
const emit = defineEmits(['updatePagination', 'onConsult']);
const props = defineProps({
  loading: Boolean,
  pagination: Object,
  columns: Array,
  rows: Array,
});
</script>

<template>
  <q-table
    class="table-default-data-table"
    flat
    bordered
    :rows="props.rows ?? []"
    :columns="props.columns ?? []"
    row-key="id"
    no-data-label="Nenhum registro encontrado"
    :rows-per-page-options="[10, 25, 50, 100]"
    :loading="props.loading"
    loading-label="Carregando..."
    :pagination="props.pagination"
    @update:pagination="emit('updatePagination', $event)"
    @request="emit('updatePagination', $event)">
    <template #header="props">
      <q-tr :props="props">
        <q-th v-for="col in props.cols" :key="col.name" :props="props">
          {{ col.label }}
        </q-th>
      </q-tr>
    </template>

    <template #body="bodyProps">
      <q-tr :props="bodyProps">
        <q-td
          v-for="col in bodyProps.cols"
          :key="col.name"
          :props="bodyProps"
          v-if="col !== undefined && col.name">
          <q-btn
            v-if="col.name === 'action'"
            dense
            flat
            round
            icon="more_horiz"
            class="button-more-horiz">
            <q-menu>
              <q-list dense style="min-width: 150px">
                <q-item
                  v-if="col.methods?.onConsult"
                  clickable
                  v-close-popup
                  @click="emit('onConsult', bodyProps.row)">
                  <q-item-section>Ver detalhes</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
          <span v-else>{{ bodyProps.row[col.name] ?? '-' }}</span>
        </q-td>
      </q-tr>
    </template>

    <template #pagination="scope">
      <span>Página {{ scope.pagination.page }} de {{ scope.pagesNumber }}</span>
      <q-btn
        v-if="scope.pagesNumber > 2"
        icon="first_page"
        color="grey-8"
        round
        dense
        flat
        :disable="scope.isFirstPage"
        @click="scope.firstPage" />
      <q-btn
        icon="chevron_left"
        color="grey-8"
        round
        dense
        flat
        :disable="scope.isFirstPage"
        @click="scope.prevPage" />
      <q-btn
        icon="chevron_right"
        color="grey-8"
        round
        dense
        flat
        :disable="scope.isLastPage"
        @click="scope.nextPage" />
      <q-btn
        v-if="scope.pagesNumber > 2"
        icon="last_page"
        color="grey-8"
        round
        dense
        flat
        :disable="scope.isLastPage"
        @click="scope.lastPage" />
    </template>
  </q-table>
</template>

<style lang="sass" scoped>
.table-default-data-table
  .q-table__top,
  thead tr:first-child th
    background-color: #064C7E
    color: white
    font-weight: bold

  thead tr th
    position: sticky
    z-index: 1
  thead tr:first-child th
    top: 0

:deep(.button-more-horiz i)
  font-size: 1.2rem !important

.q-list--dense > .q-item, .q-item--dense
  min-height: 38px
</style>
