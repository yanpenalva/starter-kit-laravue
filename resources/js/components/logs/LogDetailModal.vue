<script setup>
import { computed } from 'vue';

const MODAL_TYPES = Object.freeze({
  CREATE: 'create',
  UPDATE: 'update',
  DELETE: 'delete',
});

const props = defineProps({
  modelValue: Boolean,
  log: Object,
});

const emit = defineEmits(['update:modelValue']);

const isVisible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
});

const modalTitle = computed(() => {
  if (!props.log) return '';
  return `Detalhes da Ação - ${props.log.eventPt || props.log.event}`;
});

const modalData = computed(() => {
  if (!props.log?.properties?.attributes) return null;

  const event = props.log.event;
  const attributes = props.log.properties.attributes;

  switch (event) {
    case MODAL_TYPES.CREATE:
      return {
        type: MODAL_TYPES.CREATE,
        title: 'Dados Criados',
        data: attributes,
      };

    case MODAL_TYPES.UPDATE:
      return {
        type: MODAL_TYPES.UPDATE,
        title: 'Comparação - Antes vs Depois',
        before: attributes.before,
        after: attributes.after,
      };

    case MODAL_TYPES.DELETE:
      return {
        type: MODAL_TYPES.DELETE,
        title: 'Dados Excluídos',
        data: attributes,
      };

    default:
      return null;
  }
});

const formatValue = (value, field = null) => {
  if (value === null || value === undefined) return '-';
  if (typeof value === 'boolean') return value ? 'Sim' : 'Não';

  if (field === 'created_at' || field === 'updated_at') {
    const date = new Date(value);
    return isNaN(date.getTime())
      ? String(value)
      : date.toLocaleDateString('pt-BR', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
        }) +
          ' ' +
          date
            .toLocaleTimeString('pt-BR', {
              hour: '2-digit',
              minute: '2-digit',
            })
            .replace(':', 'h') +
          'min';
  }

  if (typeof value === 'object') return JSON.stringify(value, null, 2);
  return String(value);
};

const getFieldsToShow = (data) => {
  if (!data) return [];

  const commonFields = [
    'id',
    'name',
    'email',
    'cpf',
    'active',
    'description',
    'created_at',
    'updated_at',
  ];

  return Object.keys(data).filter((key) => {
    return (
      commonFields.includes(key) ||
      ((!key.includes('_') || key.endsWith('_at')) && !Array.isArray(data[key]))
    );
  });
};

const getFieldLabel = (field) => {
  const labels = {
    id: 'ID',
    name: 'Nome',
    email: 'E-mail',
    cpf: 'CPF',
    active: 'Ativo',
    description: 'Descrição',
    created_at: 'Criado em',
    updated_at: 'Atualizado em',
  };

  return labels[field] || field.replace('_', ' ').toUpperCase();
};
</script>

<template>
  <q-dialog v-model="isVisible" persistent>
    <q-card class="modal-card">
      <q-card-section class="q-pb-sm">
        <div class="row items-center justify-between q-mb-md">
          <div class="text-h6">{{ modalTitle }}</div>
          <q-btn icon="close" flat round dense v-close-popup />
        </div>

        <div class="row q-gutter-xs q-mb-md">
          <q-chip color="primary" text-color="white" icon="person">
            <strong>Executado por:</strong> {{ log?.causer || '-' }}
          </q-chip>
          <q-chip color="secondary" text-color="white" icon="account_circle">
            <strong>Afetado:</strong> {{ log?.subject || '-' }}
          </q-chip>
          <q-chip color="accent" text-color="white" icon="schedule">
            <strong>Data:</strong> {{ formatValue(log?.createdAt, 'created_at') }}
          </q-chip>
        </div>

        <div v-if="modalData?.type === MODAL_TYPES.CREATE">
          <q-separator />
          <div class="row items-center q-my-sm">
            <q-icon name="add_circle" color="positive" class="q-mr-sm" />
            <span class="text-subtitle1 text-positive text-weight-medium">{{
              modalData.title
            }}</span>
          </div>

          <div class="q-gutter-sm">
            <q-input
              v-for="field in getFieldsToShow(modalData.data)"
              :key="field"
              :label="getFieldLabel(field)"
              :model-value="formatValue(modalData.data[field], field)"
              readonly
              outlined
              dense
              hide-bottom-space />
          </div>
        </div>

        <div v-else-if="modalData?.type === MODAL_TYPES.UPDATE">
          <q-separator />
          <div class="row items-center q-my-sm">
            <q-icon name="compare_arrows" color="warning" class="q-mr-sm" />
            <span class="text-subtitle1 text-warning text-weight-medium">{{
              modalData.title
            }}</span>
          </div>

          <div class="row q-gutter-md">
            <div class="col">
              <div class="row items-center q-mb-sm">
                <q-icon name="history" color="negative" class="q-mr-sm" size="sm" />
                <span class="text-subtitle2 text-negative text-weight-medium">Antes</span>
              </div>
              <div class="q-gutter-sm">
                <q-input
                  v-for="field in getFieldsToShow(modalData.before)"
                  :key="'before-' + field"
                  :label="getFieldLabel(field)"
                  :model-value="formatValue(modalData.before[field], field)"
                  readonly
                  outlined
                  dense
                  class="before-input"
                  hide-bottom-space />
              </div>
            </div>

            <div class="col">
              <div class="row items-center q-mb-sm">
                <q-icon name="update" color="positive" class="q-mr-sm" size="sm" />
                <span class="text-subtitle2 text-positive text-weight-medium"
                  >Depois</span
                >
              </div>
              <div class="q-gutter-sm">
                <q-input
                  v-for="field in getFieldsToShow(modalData.after)"
                  :key="'after-' + field"
                  :label="getFieldLabel(field)"
                  :model-value="formatValue(modalData.after[field], field)"
                  readonly
                  outlined
                  dense
                  class="after-input"
                  hide-bottom-space />
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="modalData?.type === MODAL_TYPES.DELETE">
          <q-separator />
          <div class="row items-center q-my-sm">
            <q-icon name="delete_forever" color="negative" class="q-mr-sm" />
            <span class="text-subtitle1 text-negative text-weight-medium">{{
              modalData.title
            }}</span>
          </div>

          <div class="q-gutter-sm">
            <q-input
              v-for="field in getFieldsToShow(modalData.data)"
              :key="field"
              :label="getFieldLabel(field)"
              :model-value="formatValue(modalData.data[field], field)"
              readonly
              outlined
              dense
              class="delete-input"
              hide-bottom-space />
          </div>
        </div>
      </q-card-section>

      <q-card-actions align="right" class="q-pt-none">
        <q-btn label="Fechar" color="primary" v-close-popup />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<style scoped>
.modal-card {
  min-width: 500px;
  max-width: 90vw;
}

.before-input {
  background-color: #ffebee;
}

.after-input {
  background-color: #e8f5e9;
}

.delete-input {
  background-color: #ffebee;
}
</style>
