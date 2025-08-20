import { MODAL_TYPES } from '@/utils/logTypes';

export function useGenericLogFormatter(log, config) {
  const attributes = log.properties?.attributes || {};

  const normalize = (data = {}) =>
    typeof config.normalize === 'function' ? config.normalize(data) : data;

  const formatByEvent = new Map([
    [
      'create',
      () => ({
        type: MODAL_TYPES.CREATE,
        title: config.titles?.create || 'Registro Criado',
        fields: config.fields?.create || [],
        data: normalize(attributes),
      }),
    ],
    [
      'update',
      () => ({
        type: MODAL_TYPES.UPDATE,
        title: config.titles?.update || 'Registro Atualizado',
        fields: config.fields?.update || [],
        before: normalize(attributes.before || {}),
        after: normalize(attributes.after || {}),
      }),
    ],
    [
      'delete',
      () => ({
        type: MODAL_TYPES.DELETE,
        title: config.titles?.delete || 'Registro Excluído',
        fields: config.fields?.delete || [],
        data: normalize(attributes.before || {}),
      }),
    ],
  ]);

  return formatByEvent.has(log.event) ? formatByEvent.get(log.event)() : null;
}
