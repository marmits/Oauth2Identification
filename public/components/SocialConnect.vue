<template>



<form v-if="display">
  <div class="row">
    <div class="col align-self-center gy-2">
      <ButtonSocial v-bind:type="'google'"></ButtonSocial>
    </div>
    <div class="col align-self-center gy-2">
      <ButtonSocial v-bind:type="'github'"></ButtonSocial>
    </div>
  </div>
</form>
</template>

<script setup>
import ButtonSocial from "./ButtonSocial.vue";
const props = defineProps(['isConnected'])
import {computed, ref, onMounted  } from 'vue'

const display = computed(() => {
  if(props.isConnected){
    return false
  }
  return true
})
</script>
