export const MODAL_TYPES = Object.freeze({
  CREATE: 'create',
  UPDATE: 'update',
  DELETE: 'delete',
});

export const LOG_TYPES = Object.freeze({
  ROLES: 'roles',
  USERS: 'users',
});

export const MODULE_LABELS = Object.freeze({
  [LOG_TYPES.USERS]: 'Usuários',
  [LOG_TYPES.ROLES]: 'Perfis',
});
