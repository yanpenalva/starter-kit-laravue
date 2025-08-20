import { useRoleLogFormatter } from '@/composables/Log/useRoleLogFormatter';
import { LOG_TYPES } from '@/utils/logTypes';

export const FORMATTERS = {
  [LOG_TYPES.ROLES]: useRoleLogFormatter,
};
