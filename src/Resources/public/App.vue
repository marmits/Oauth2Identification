// assets/js/App.vue

<script src="./js/vue/oauth2.js">

</script>

<template>
  <input type="text" v-model="name">
  <button @click="refreshHello">Demander un bonjour !</button>
  <h1 v-show="!!hello">{{ hello }}</h1>
</template>