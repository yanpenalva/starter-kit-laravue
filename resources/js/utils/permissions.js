const ROLE_PERMISSION = {
  CREATE: 'roles.create',
  LIST: 'roles.list',
  EDIT: 'roles.edit',
  VIEW: 'roles.view',
  DELETE: 'roles.delete',
};

const USER_PERMISSION = {
  CREATE: 'users.create',
  LIST: 'users.list',
  UPDATE: 'users.update',
  SHOW: 'users.show',
  DELETE: 'users.delete',
};

const LOG_PERMISSION = {
  LIST: 'activity_logs.list',
};

export { LOG_PERMISSION, ROLE_PERMISSION, USER_PERMISSION };
