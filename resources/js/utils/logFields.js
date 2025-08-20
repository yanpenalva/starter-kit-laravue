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

const formatBoolean = (booleanValue) => (booleanValue ? 'Sim' : 'Não');

const formatDateTime = (dateValue) => {
  const date = new Date(dateValue);
  if (isNaN(date.getTime())) return String(dateValue);

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

const formatObject = (objectValue) => JSON.stringify(objectValue, null, 2);

const rules = [
  {
    match: (value) => value === null || value === undefined || value === '',
    format: () => '-',
  },
  {
    match: (value) => typeof value === 'boolean',
    format: formatBoolean,
  },
  {
    match: (_value, field) => field === 'created_at' || field === 'updated_at',
    format: formatDateTime,
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
