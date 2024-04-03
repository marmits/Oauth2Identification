// assets/js/App.vue

<style>
#Oauth {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
}
</style>


<template>
  <Message v-bind:is-connected="SetConnected()" v-bind:titre="titre"></Message>
  <SocialConnect v-bind:is-connected="SetConnected()"></SocialConnect>
  <InfosUser v-bind:is-connected="SetConnected()"> </InfosUser>
  <Logout v-bind:is-connected="SetConnected()"></Logout>
</template>


<script>
import SocialConnect from "./components/SocialConnect.vue";
import Logout from "./components/Logout.vue";
import Message from "./components/Message.vue";
import InfosUser from "./components/InfosUser.vue";

export default {
  name: 'Oauth',
  components: {
    SocialConnect,
    Logout,
    Message,
    InfosUser
  },
  data() {
    return {
      connected: false,
      titre: 'Sign-in',
    }
  },
  methods: {
    SetConnected(){
      let elConnected = document.getElementById('statut_connected')
      if(elConnected !== null){
        this.connected = true
      }
      return this.connected;
    }
  }
};
</script>


