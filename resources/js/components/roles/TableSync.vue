<script setup>
import Pagination from '@/components/shared/Pagination.vue';
import useRole from '@/composables/Roles/useRole';
import { ref } from 'vue';

const {
  shouldBlockEditRoleAdmin,
  shouldBlockDeleteRoleUserAuth,
  shouldBlockDeleteProtectedRole,
} = useRole();

const emit = defineEmits(['updatePagination', 'onConsult', 'onEdit', 'onDelete']);

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
                Tem certeza que deseja excluir por definitivo este perfil?
              </strong>
            </span>
          </q-card-section>

          <q-card-actions align="center">
            <q-btn
              v-close-popup
              outline
              label="Sim"
              color="primary"
              @click="confirmDeleteRow(true)" />
            <q-btn
              v-close-popup
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

    <template #body="props">
      <q-tr :props="props">
        <q-td v-for="col in props.cols" :key="col.name" :props="props">
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
                  clickable
                  v-close-popup
                  :to="{ name: 'showRole', params: { id: props.row.id } }"
                  @click="emit('onConsult', props.row)">
                  <q-item-section>Ver detalhes</q-item-section>
                </q-item>

                <q-item
                  v-if="!shouldBlockEditRoleAdmin(props.row.slug) && col.methods?.onEdit"
                  clickable
                  v-close-popup
                  @click="emit('onEdit', props.row)">
                  <q-item-section>Editar</q-item-section>
                </q-item>

                <q-separator />

                <q-item
                  v-if="
                    !shouldBlockDeleteProtectedRole(props.row.slug) &&
                    shouldBlockDeleteRoleUserAuth(props.row.id) &&
                    col.methods?.onDelete
                  "
                  clickable
                  v-close-popup
                  @click="deleteRow(props.row)">
                  <q-item-section>Excluir</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>

          <span v-else>
            {{ props.row[col.field] ?? '-' }}
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
:deep(.button-more-horiz i)
  font-size: 1.2rem !important

.q-list--dense > .q-item, .q-item--dense
  min-height: 38px
</style>
