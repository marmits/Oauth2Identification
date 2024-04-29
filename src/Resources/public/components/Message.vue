<template>
  <div :class="`alert alert-${type} message`" role="alert">
    <h4 class="alert-heading">{{titre}}</h4>
  </div>
</template>


<script setup>
const props = defineProps(['titre', 'type'])
</script>



