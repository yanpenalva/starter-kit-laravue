<script setup>
import Pagination from '@/components/shared/Pagination.vue';
import { ref } from 'vue';

const emit = defineEmits([
  'update:modelValue',
  'confirm',
  'cancel',
  'updatePagination',
  'onStatus',
  'onConsult',
  'onEdit',
  'onDelete',
  'notify',
  'onValidate',
]);

const loading = ref(false);
const pagination = ref({});
const columns = ref([]);
const rows = ref([]);
const itemDelete = ref(null);
const confirmRowDelete = ref(false);

const deleteRow = (row) => {
  confirmRowDelete.value = true;
  itemDelete.value = row;
};

const confirmDeleteRow = (isStatus) => {
  confirmRowDelete.value = false;
  if (isStatus) {
    emit('onDelete', itemDelete.value);
  }
  itemDelete.value = null;
};
</script>

<template>
  <q-table
    class="table-default-data-table"
    flat
    bordered
    :rows="rows"
    :columns="columns"
    row-key="id"
    :rows-per-page-options="[10, 25, 50, 100]"
    :loading="loading"
    loading-label="Carregando..."
    :pagination="pagination"
    :no-data-label="loading ? '' : 'Nenhum registro encontrado'"
    @update:pagination="emit('updatePagination', $event)"
    @request="emit('updatePagination', $event)">
    <template #header="props">
      <q-dialog v-model="confirmRowDelete" persistent>
        <q-card>
          <q-card-section class="row items-center">
            <span class="q-ml-sm">
              <strong>
                Tem certeza de que deseja excluir este usuário?
                <br />
                Esta ação não poderá ser desfeita.
              </strong>
            </span>
          </q-card-section>

          <q-card-actions align="center">
            <q-btn
              v-close-popup
              label="Sim"
              color="primary"
              @click="confirmDeleteRow(true)" />
            <q-btn
              v-close-popup
              outline
              label="Não"
              color="primary"
              @click="confirmDeleteRow(false)" />
          </q-card-actions>
        </q-card>
      </q-dialog>

      <q-tr :props="props">
        <q-th v-for="col in props.cols" :key="col.name" :props="props">
          {{ col.label }}
        </q-th>
      </q-tr>
    </template>

    <template #body="bodyProps">
      <q-tr :props="bodyProps">
        <q-td v-for="col in bodyProps.cols" :key="col.name" :props="bodyProps">
          <span v-if="col.name === 'setSituation'">
            <q-toggle
              v-model="bodyProps.row.active"
              color="primary"
              keep-color
              @update:model-value="
                emit('onStatus', { value: $event, data: bodyProps.row })
              " />
          </span>

          <q-btn
            v-else-if="col.name === 'action'"
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
                  :to="{ name: 'showUsers', params: { id: bodyProps.row.id } }"
                  @click="emit('onConsult', bodyProps.row)">
                  <q-item-section>Ver detalhes</q-item-section>
                </q-item>
                <q-item
                  v-if="col.methods?.onEdit"
                  clickable
                  v-close-popup
                  @click="emit('onEdit', bodyProps.row)">
                  <q-item-section>Editar</q-item-section>
                </q-item>
                <q-separator />
                <q-item
                  v-if="col.methods?.onDelete(bodyProps.row)"
                  clickable
                  v-close-popup
                  @click="deleteRow(bodyProps.row)">
                  <q-item-section>Excluir</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>

          <span v-else>
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
