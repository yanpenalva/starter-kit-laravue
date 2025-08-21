export default function useAuthenticateConfig() {
  const textPage = {
    login: {
      title: 'Acesse o sistema',
      message: 'Faça login ou registre-se para começar',
      routeFooter: '/cadastro',
      labelFooter: 'Cadastre-se',
      textFooter: 'Ainda não tem conta?',
    },
    register: {
      title: 'Criar conta',
      message: 'Registre-se e comece a usar o sistema aqui.',
      routeFooter: '/',
      labelFooter: 'Clique aqui',
      textFooter: 'Voltar para o login?',
    },
    resetPassword: {
      title: 'Alteração de senha',
      message: 'Digite sua nova senha para ter acesso ao sistema',
      routeFooter: '/',
      labelFooter: 'Clique aqui',
      textFooter: 'Voltar para o login?',
    },
    forgotPassword: {
      title: 'Esqueceu a senha?',
      message:
        'Digite o seu e-mail que enviaremos um link para realizar o processo de redefinição de senha',
      routeFooter: '/',
      labelFooter: 'Clique aqui',
      textFooter: 'Voltar para o login?',
    },
    copyright: '© Desenvolvido e Mantido pelo Escritório de Projetos e  Processos',
  };

  return {
    textPage,
  };
}
