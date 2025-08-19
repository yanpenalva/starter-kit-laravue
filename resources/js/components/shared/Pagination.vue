<script setup>
import { computed } from 'vue';

const props = defineProps({
  scope: {
    type: Object,
    required: true,
  },
});

const pagination = computed(() => ({
  page: props.scope.pagination.current_page || props.scope.pagination.page,
  perPage: props.scope.pagination.per_page || props.scope.pagination.rowsPerPage,
  total: props.scope.pagination.total || props.scope.pagination.rowsNumber,
  lastPage: props.scope.pagination.last_page || props.scope.pagesNumber,
}));

const rangeText = computed(() => {
  const start = (pagination.value.page - 1) * pagination.value.perPage + 1;
  const end = Math.min(
    pagination.value.page * pagination.value.perPage,
    pagination.value.total,
  );
  return `${start}-${end} de ${pagination.value.total}`;
});

const isFirstPage = computed(() => pagination.value.page === 1);
const isLastPage = computed(() => pagination.value.page === pagination.value.lastPage);
</script>

<template>
  <div class="q-gutter-sm flex items-center justify-between full-width">
    <div>
      <span class="text-caption">
        {{ rangeText }}
      </span>
    </div>

    <div class="row items-center">
      <span class="q-mr-sm text-caption"
        >Página {{ pagination.page }} de {{ pagination.lastPage }}</span
      >

      <q-btn
        v-if="pagination.lastPage > 2"
        icon="first_page"
        color="grey-8"
        round
        dense
        flat
        :disable="isFirstPage"
        @click="props.scope.firstPage" />

      <q-btn
        icon="chevron_left"
        color="grey-8"
        round
        dense
        flat
        :disable="isFirstPage"
        @click="props.scope.prevPage" />

      <q-btn
        icon="chevron_right"
        color="grey-8"
        round
        dense
        flat
        :disable="isLastPage"
        @click="props.scope.nextPage" />

      <q-btn
        v-if="pagination.lastPage > 2"
        icon="last_page"
        color="grey-8"
        round
        dense
        flat
        :disable="isLastPage"
        @click="props.scope.lastPage" />
    </div>
  </div>
</template>
