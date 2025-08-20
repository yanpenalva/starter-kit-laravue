import { formatValue, getFieldLabel } from '@/utils/logFields';
import { MODAL_TYPES, MODULE_LABELS } from '@/utils/logTypes';
import { computed } from 'vue';
import { FORMATTERS } from './formatters';

export function useLogModal(props, emit) {
  const isVisible = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
  });

  const modalTitle = computed(() => {
    if (!props.log) return '';
    const moduleName = MODULE_LABELS[props.log.logName] || props.log.logName;
    const eventName = props.log.eventPt || props.log.event;
    return `Detalhes da Ação - ${moduleName} (${eventName})`;
  });

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
