<template>
  <label class="text-weight-bold">
    <slot />
    <span v-if="required" class="text-negative q-ml-sm">*</span>
  </label>
</template>

<script setup>
defineProps({
  required: { type: Boolean, default: false }
});
</script>
