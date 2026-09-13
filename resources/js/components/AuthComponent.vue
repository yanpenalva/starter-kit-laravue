<script setup>
import logo from '@/assets/logo.png';
import FooterAuth from '@/components/auth/FooterAuth.vue';
import useAuthenticateConfigPage from '@composables/Authenticate/useAuthenticateConfigPage';

defineProps({
  typePage: {
    type: String,
    default: 'login',
  },
});

const { textPage } = useAuthenticateConfigPage();
</script>
<template>
  <div class="row">
    <section class="gt-xs gt-sm col-md-6">
      <div class="auth__background">
      </div>
    </section>
    <section class="col-xs-12 col-md-6">
      <div class="row justify-center items-center">
        <div class="col-md-8 col-sm-8 col-xs-12 q-pa-xl">
          <div><q-img :src="logo" width="200px" fit="contain" /></div>
          <div class="text-h5 q-mt-lg q-mb-sm">{{ textPage[typePage].title }}</div>
          <div class="q-pb-xs">
            <p class="text-subtitle2">{{ textPage[typePage].message }}</p>
          </div>

          <div class="q-mt-xs">
            <slot></slot>
            <FooterAuth
              :text-page="textPage[typePage]"
              :copyright="textPage['copyright']" />
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<style lang="css">
@import '@css/auth/auth-style.css';
</style>
