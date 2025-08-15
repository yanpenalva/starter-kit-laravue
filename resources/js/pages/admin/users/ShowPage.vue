<script setup>
import Form from '@/components/users/FormView.vue';
import useUserStore from '@/store/useUserStore';
import { useQuasar } from 'quasar';
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const $q = useQuasar();
const userStore = useUserStore();
const user = ref(null);

const loadUserData = async () => {
  try {
    $q.loading.show();
    await userStore.consult(route.params.id);

    user.value = userStore.user;
  } finally {
    $q.loading.hide();
  }
};

onMounted(loadUserData);
</script>

<template>
  <div>
    <div class="row items-center q-mb-md">
      <div class="col-md-4 q-mb-md">
        <span class="text-weight-bold title__page--style">
          Visualizar os dados do seu perfil
        </span>
      </div>
    </div>
    <q-card class="q-pa-md">
      <q-card-section>
        <Form v-if="user" :user="user" />
        <div v-else class="text-subtitle1 text-center q-my-md">Carregando...</div>
      </q-card-section>
    </q-card>
  </div>
</template>
