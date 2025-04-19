import router from '@/routes';
import useAuthStore from '@/store/useAuthStore';
import notify from '@/utils/notify';
import axios from 'axios';
import { Loading } from 'quasar';

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

const http = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  timeout: 10000,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Content-Type': 'application/json',
  },
});

http.interceptors.response.use(
  (response) => response,
  async (error) => {
    Loading.hide();

    if (!error.response) {
      notify('Ocorreu um erro de rede inesperado.', 'negative');
      return Promise.reject(error);
    }

    const timeoutMessage =
      'Não foi possível carregar esta página corretamente, verifique sua conexão com a internet e tente novamente';
    if (error.code === 'ECONNABORTED') {
      notify(timeoutMessage, 'negative');
      return Promise.reject({ message: timeoutMessage });
    }

    let { status, data } = error.response;

    const isValidData = data && typeof data === 'object' && !(data instanceof Blob);
    const message = isValidData ? data?.message || 'Erro inesperado' : 'Erro inesperado';

    handleErrorResponse(status, message, isValidData ? data : {}, error.config);

    return Promise.reject(error);
  },
);

function handleErrorResponse(status, message, data, config) {
  const authStore = useAuthStore();

  const isAuthRoute = ['/api/v1/login', '/api/v1/ldap/login'].some((route) =>
    config.url.includes(route),
  );

  const logoutAndRedirect = () => {
    authStore.logout();
    router.push({ name: 'login' });
  };

  // Trata cada status de erro explicitamente aqui e redirecionar conforme necessário.
  switch (status) {
    case 401:
      notify(message, 'negative');
      const isInvalidCredentials =
        isAuthRoute ||
        message === 'Usuário ou senha inválidos' ||
        message ===
          'O domínio não é válido. Os domínios válidos são @uefs.br, @uefs.local e @discente.uefs.br.';
      if (!isInvalidCredentials) logoutAndRedirect();
      break;

    case 403:
      const errors = [
        'Usuário não ativado',
        'Usuário inativo',
        'Usuário não é servidor UEFS',
      ];
      const errorMessage = data?.error || data?.errors;
      if (errors.includes(errorMessage)) {
        notify(message, 'negative');
      }
      router.go(-1);

      break;

    case 404:
      const errorMessage404 = data?.message.includes('No query results for model')
        ? 'Nenhum registro foi encontrado'
        : data?.message;
      notify(errorMessage404, 'negative');
      break;

    case 408:
      notify('Tempo de solicitação esgotado', 'negative');
      window.location.reload();
      break;

    case 419:
      notify(
        'Sessão expirada. Por favor, atualize a página e tente novamente.',
        'negative',
      );
      router.replace('/');
      break;

    case 422:
      const errors422 = data?.errors || {};

      if (data?.message) {
        notify(data.message, 'negative');
        return;
      }
      const messagesErrors = Object.values(errors422);
      messagesErrors.slice(0, 8).forEach((msg) => notify(msg.toString(), 'negative'));
      break;

    case 429:
      notify(message, 'negative');
      logoutAndRedirect();
      break;

    case 500:
      notify(
        'Erro interno do servidor. Por favor, contate a administração do sistema para mais informações.',
        'negative',
      );
      break;

    default:
      const customMessage =
        message === 'This action is unauthorized.'
          ? 'Você não tem permissão para acessar este recurso'
          : message;

      notify(customMessage, 'negative');
      if (message === 'Unauthorized.' || message === 'This action is unauthorized.') {
        router.replace('/admin/inicio');
      }
      break;
  }
}

export default http;
