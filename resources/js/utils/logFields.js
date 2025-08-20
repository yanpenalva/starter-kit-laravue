// utils/logFields.js
export const LABELS_MAP = new Map([
  ['id', 'ID'],
  ['name', 'Nome'],
  ['email', 'E-mail'],
  ['cpf', 'CPF'],
  ['active', 'Ativo'],
  ['description', 'Descrição'],
  ['created_at', 'Criado em'],
  ['updated_at', 'Atualizado em'],
  ['deleted_at', 'Excluído em'],
  ['createdAt', 'Criado em'],
  ['updatedAt', 'Atualizado em'],
  ['deletedAt', 'Excluído em'],
  ['roles', 'Perfil'],
  ['permissions', 'Permissões'],
  ['slug', 'Apelido'],
]);

export const getFieldLabel = (field) =>
  LABELS_MAP.has(field) ? LABELS_MAP.get(field) : field.replace(/_/g, ' ').toUpperCase();

// --- Normalizers ---
const normalizeActive = (value) => {
  if (value === true || value === 'true' || value === 1 || value === '1') return true;
  if (value === false || value === 'false' || value === 0 || value === '0') return false;
  return null;
};

// --- Formatters ---
const formatBoolean = (booleanValue) => (booleanValue ? 'Sim' : 'Não');

const formatObject = (objectValue) => JSON.stringify(objectValue, null, 2);

// --- Rules ---
const rules = [
  {
    match: (value) => value === null || value === undefined || value === '',
    format: () => '-',
  },
  {
    match: (_value, field) => field === 'active',
    format: (value) => formatBoolean(normalizeActive(value)),
  },
  {
    match: (value) =>
      typeof value === 'boolean' ||
      typeof value === 'number' ||
      typeof value === 'string',
    format: (value) => String(value),
  },
  {
    match: (value) => typeof value === 'object',
    format: formatObject,
  },
];

export const formatValue = (value, field = null) => {
  for (const { match, format } of rules) {
    if (match(value, field)) return format(value);
  }
  return String(value);
};
