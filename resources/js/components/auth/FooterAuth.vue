<script setup>
import useGlobal from '@/utils/global.js';

const { appVersion } = useGlobal();

defineProps({
  textPage: Object,
  copyright: String,
});
</script>

<template>
  <div class="row justify-between q-mt-xs">
    <div class="col-12 text--font-13 q-px-md">
      <span class="text-info q-mr-xs">
        {{ textPage.textFooter }}
      </span>
      <RouterLink :to="textPage.routeFooter" class="link--style" color="primary">
        {{ textPage.labelFooter }}
      </RouterLink>
      <div class="q-mt-sm text--font-13 text-center text-info">
        <p class="q-pa-xs">{{ copyright }} - {{ appVersion }}</p>
      </div>
    </div>
  </div>
</template>
