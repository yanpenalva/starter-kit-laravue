<script setup>
import { useLogModal } from '@/composables/Log/useLogModal';

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  log: { type: [Object, null], default: null },
});

const emit = defineEmits(['update:modelValue']);

const { MODAL_TYPES, isVisible, modalTitle, modalData, getFieldLabel, formatValue } =
  useLogModal(props, emit);
</script>

<template>
  <q-dialog v-model="isVisible" persistent>
    <q-card class="modal-card">
      <q-card-section>
        <div class="text-h6 text-weight-bold">{{ modalTitle }}</div>
      </q-card-section>

      <q-separator />

      <!-- =========================================
           CREATE e DELETE
           - CREATE: mostra os dados criados
           - DELETE: mostra os dados antes de excluir
           ========================================= -->
      <q-card-section
        v-if="
          modalData &&
          (modalData.type === MODAL_TYPES.CREATE || modalData.type === MODAL_TYPES.DELETE)
        ">
        <div class="q-gutter-sm">
          <div
            v-for="field in modalData.fields"
            :key="field.key"
            class="row items-center q-py-xs">
            <!-- Label → ocupa só o espaço necessário -->
            <div class="col-auto text-grey-7 text-body2 q-pr-sm">
              {{ getFieldLabel(field.key) }}:
            </div>

            <!-- Valor → ocupa o resto da linha -->
            <div
              class="col text-body1 text-weight-medium"
              :class="{
                'text-grey': formatValue(modalData.data?.[field.key], field.key) === '-',
              }">
              {{ formatValue(modalData.data?.[field.key], field.key) }}
            </div>
          </div>
        </div>
      </q-card-section>

      <!-- =========================================
           UPDATE
           - Mostra comparação Antes / Depois
           - Campos com compare: exibem os dois
           - Campos sem compare: exibem o valor atual
           ========================================= -->
      <q-card-section v-else-if="modalData && modalData.type === MODAL_TYPES.UPDATE">
        <div v-for="field in modalData.fields" :key="field.key" class="q-mb-md">
          <!-- Label -->
          <div class="text-caption text-grey-7 q-mb-xs">
            {{ getFieldLabel(field.key) }}
          </div>

          <!-- Valores -->
          <div class="q-ml-sm">
            <template v-if="field.compare">
              <div>
                <em>Antes:</em>
                {{ formatValue(modalData.before?.[field.key], field.key) }}
              </div>
              <div>
                <em>Depois:</em>
                {{ formatValue(modalData.after?.[field.key], field.key) }}
              </div>
            </template>
            <template v-else>
              {{
                formatValue(
                  modalData.after?.[field.key] ?? modalData.before?.[field.key],
                  field.key,
                )
              }}
            </template>
          </div>
        </div>
      </q-card-section>

      <q-separator />

      <!-- Ações -->
      <q-card-actions align="right">
        <q-btn flat label="Fechar" v-close-popup />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<style scoped>
.modal-card {
  max-width: 700px;
  width: 100%;
}
</style>
