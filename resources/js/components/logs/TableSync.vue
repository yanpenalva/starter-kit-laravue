<script setup>
import Pagination from '@/components/shared/Pagination.vue';

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
    :rows-per-page-options="[10, 25, 50, 100]"
    :loading="props.loading"
    loading-label="Carregando..."
    :pagination="props.pagination"
    :no-data-label="props.loading ? '' : 'Nenhum registro encontrado'"
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
        <q-td v-for="col in bodyProps.cols" :key="col.name" :props="bodyProps">
          <!-- Botão só aparece se não for coluna action OU se for action mas não for evento 'view' -->
          <q-btn
            v-if="col.name === 'action' && bodyProps.row.event !== 'view'"
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
          <span v-else-if="col.name !== 'action'">
            {{ bodyProps.row[col.field] ?? '-' }}
          </span>
        </q-td>
      </q-tr>
    </template>

    <template #pagination="scope">
      <Pagination :scope="scope" />
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
