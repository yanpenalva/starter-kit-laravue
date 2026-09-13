import { myProfile } from '@/services/AuthenticateService';
import UserService from '@/services/UserService';
import { defineStore } from 'pinia';
import { LocalStorage } from 'quasar';

const useAuthStore = defineStore('auth', {
  state: () => ({
    permissions: null,
    roles: null,
    contributor: null,
    users: [],
    user: null,
    errors: null,
    activeTab: 'external',
    externalCredentials: {
      email: '',
      password: '',
      remember: false,
    },
    ldapCredentials: {
      name: '',
      password: '',
      remember: false,
    },
  }),
  persist: {
    key: 'auth',
    storage: localStorage,
    paths: ['permissions', 'roles', 'contributor', 'user', 'activeTab'],
  },
  getters: {
    isUserLoggedIn(state) {
      return Boolean(state.user);
    },
    getPermissions() {
      return this.permissions;
    },
    getRoles() {
      return this.roles;
    },
    getUsers() {
      return this.users;
    },
    getUser() {
      return this.user ?? JSON.parse(localStorage.getItem('auth'))?.user;
    },
    getErrors() {
      return this.errors;
    },
    getActiveTab(state) {
      return state.activeTab;
    },
  },
  actions: {
    async list(params) {
      const data = await UserService.index(params);
      this.users = data;
    },
    setCredentials({ user }) {
      if (!user || !user.id) {
        throw new Error('Usuário inválido ou não autenticado');
      }
      this.user = {
        id: user.id,
        name: user.name,
        email: user.email,
      };
    },

    logout() {
      this.$reset();
      LocalStorage.clear();
      localStorage.clear();
    },
    async setProfile() {
      const { data } = await myProfile();
      this.permissions = data?.permissions;
      this.roles = data?.roles;
      this.contributor = data?.contributor;
    },
    setActiveTab(tab) {
      this.activeTab = tab;
    },
    setExternalCredentials(credentials) {
      this.externalCredentials = credentials;
    },
    setLdapCredentials(credentials) {
      this.ldapCredentials = credentials;
    },
    clearExternalCredentials() {
      this.externalCredentials = { email: '', password: '', remember: false };
    },
    clearLdapCredentials() {
      this.ldapCredentials = { name: '', password: '', remember: false };
    },
    clearCredentialsOnTabChange() {
      this.activeTab === 'external'
        ? this.clearLdapCredentials()
        : this.clearExternalCredentials();
    },
  },
});

export default useAuthStore;
