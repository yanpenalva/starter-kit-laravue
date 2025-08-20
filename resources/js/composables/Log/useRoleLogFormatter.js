import { useGenericLogFormatter } from './useGenericLogFormatter';

const ROLE_FIELDS = {
  create: [
    { key: 'id', compare: false },
    { key: 'name', compare: false },
    { key: 'slug', compare: false },
    { key: 'description', compare: false },
    { key: 'created_at', compare: false },
  ],
  update: [
    { key: 'id', compare: false },
    { key: 'name', compare: true },
    { key: 'description', compare: true },
    { key: 'permissions', compare: true },
    { key: 'updated_at', compare: false },
  ],
  delete: [
    { key: 'id', compare: false },
    { key: 'name', compare: false },
    { key: 'slug', compare: false },
    { key: 'description', compare: false },
    { key: 'deleted_at', compare: false },
  ],
};

const normalizeRoleData = (data = {}) => ({
  id: data.id,
  name: data.name,
  slug: data.slug,
  description: data.description ?? '-',
  created_at: data.created_at,
  updated_at: data.updated_at,
  deleted_at: data.deleted_at,
  permissions:
    Array.isArray(data.permissions) && data.permissions.length > 0
      ? data.permissions.map((p) => p.name ?? String(p)).join(', ')
      : data.permissions
        ? String(data.permissions)
        : undefined,
});

export const useRoleLogFormatter = (log) =>
  useGenericLogFormatter(log, {
    titles: {
      create: 'Perfil Criado',
      update: 'Perfil Atualizado',
      delete: 'Perfil Excluído',
    },
    fields: ROLE_FIELDS,
    normalize: normalizeRoleData,
  });
