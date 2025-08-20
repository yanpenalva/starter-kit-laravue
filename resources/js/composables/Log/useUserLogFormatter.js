import { useGenericLogFormatter } from './useGenericLogFormatter';

const USER_FIELDS = {
  create: [
    { key: 'id', compare: false },
    { key: 'name', compare: false },
    { key: 'email', compare: false },
    { key: 'cpf', compare: false },
    { key: 'active', compare: false },
    { key: 'created_at', compare: false },
  ],
  update: [
    { key: 'id', compare: false },
    { key: 'name', compare: true },
    { key: 'email', compare: true },
    { key: 'cpf', compare: true },
    { key: 'active', compare: true },
    { key: 'updated_at', compare: false },
  ],
  delete: [
    { key: 'id', compare: false },
    { key: 'name', compare: false },
    { key: 'email', compare: false },
    { key: 'cpf', compare: false },
    { key: 'active', compare: false },
    { key: 'deleted_at', compare: false },
  ],
};

const normalizeUserData = (data = {}) => ({
  id: data.id,
  name: data.name,
  email: data.email,
  cpf: data.cpf,
  active: data.active,
  created_at: data.created_at,
  updated_at: data.updated_at,
  deleted_at: data.deleted_at,
});
export const useUserLogFormatter = (log) =>
  useGenericLogFormatter(log, {
    titles: {
      create: 'Usuário Criado',
      update: 'Usuário Atualizado',
      delete: 'Usuário Excluído',
    },
    fields: USER_FIELDS,
    normalize: normalizeUserData,
  });
