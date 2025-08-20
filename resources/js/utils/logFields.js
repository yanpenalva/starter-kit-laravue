export const LABELS_MAP = new Map([
  ['id', 'ID'],
  ['name', 'Nome'],
  ['email', 'E-mail'],
  ['cpf', 'CPF'],
  ['active', 'Ativo'],
  ['description', 'Descrição'],
  ['created_at', 'Criado em'],
  ['updated_at', 'Atualizado em'],
  ['permissions', 'Permissões'],
  ['slug', 'Apelido'],
]);

export const getFieldLabel = (field) =>
  LABELS_MAP.has(field) ? LABELS_MAP.get(field) : field.replace(/_/g, ' ').toUpperCase();

const formatBoolean = (value) => (value ? 'Sim' : 'Não');

const formatDateTime = (value) => {
  const date = new Date(value);
  if (isNaN(date.getTime())) return String(value);

  const formattedDate = date.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });

  const formattedTime = date
    .toLocaleTimeString('pt-BR', {
      hour: '2-digit',
      minute: '2-digit',
    })
    .replace(':', 'h');

  return `${formattedDate} ${formattedTime}min`;
};

const formatObject = (value) => JSON.stringify(value, null, 2);

export const formatValue = (value, field = null) => {
  if (value === null || value === undefined || value === '') return '-';
  if (typeof value === 'boolean') return formatBoolean(value);
  if (field === 'created_at' || field === 'updated_at') return formatDateTime(value);
  if (typeof value === 'object') return formatObject(value);
  return String(value);
};
