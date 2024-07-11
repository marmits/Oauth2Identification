// assets/js/App.vue

<style>
#Oauth {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
}
</style>


<template>
  <div v-if="!connected">
    <Message v-bind:titre="titre" v-bind:type="type"></Message>
  </div>
  <SocialConnect v-bind:is-connected="connected"></SocialConnect>
  <InfosUser v-bind:is-connected="connected"> </InfosUser>
  <Logout v-bind:is-connected="connected"></Logout>
</template>

<script setup>
import {computed, ref } from 'vue'
import SocialConnect from "./components/SocialConnect.vue";
import Logout from "./components/Logout.vue";
import Message from "./components/Message.vue";
import InfosUser from "./components/InfosUser.vue";

const titre = 'Sign-in'
const type = ref('secondary')
const connected = computed(() => {
  let elConnected = document.getElementById('statut_connected')
  if(elConnected !== null){
    return true
  }
  return false

});

</script>





