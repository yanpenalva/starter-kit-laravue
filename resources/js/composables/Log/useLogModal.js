import { formatValue, getFieldLabel } from '@/utils/logFields';
import { MODAL_TYPES } from '@/utils/logTypes';
import { computed } from 'vue';
import { FORMATTERS } from './formatters';

export function useLogModal(props, emit) {
  const isVisible = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
  });

  const modalTitle = computed(() =>
    props.log ? `Detalhes da Ação - ${props.log.eventPt || props.log.event}` : '',
  );

  const modalData = computed(() => {
    if (!props.log) return null;
    const formatter = FORMATTERS[props.log.logName];
    return formatter ? formatter(props.log) : null;
  });

  return {
    MODAL_TYPES,
    isVisible,
    modalTitle,
    modalData,
    getFieldLabel,
    formatValue,
  };
}
