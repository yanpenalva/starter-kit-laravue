import { useRoleLogFormatter } from '@/composables/Log/useRoleLogFormatter';
import { useUserLogFormatter } from '@/composables/Log/useUserLogFormatter';
import { LOG_TYPES } from '@/utils/logTypes';

export const FORMATTERS = {
  [LOG_TYPES.ROLES]: useRoleLogFormatter,
  [LOG_TYPES.USERS]: useUserLogFormatter,
};
