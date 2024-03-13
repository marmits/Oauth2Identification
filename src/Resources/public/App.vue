// assets/js/App.vue

<style>
#app {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>


<template>

  <Message v-bind:is-connected="SetConnected()" v-bind:titre="titre"></Message>
  <SocialConnect v-bind:is-connected="SetConnected()"></SocialConnect>
  <Logout v-bind:is-connected="SetConnected()"></Logout>

  <input  type="text" v-model="name">
  <button @click="refreshHello">Demander un bonjour !</button>
  <h1 v-show="!!hello">{{ hello }}</h1>
</template>


<script>
import SocialConnect from "./components/SocialConnect.vue";
import Logout from "./components/Logout.vue";
import Message from "./components/Message.vue";

export default {
  name: 'app',
  components: {
    SocialConnect,
    Logout,
    Message
  },
  data() {
    return {
      name: '',
      hello: '',
      connected: false,
      titre: 'Sign-in'
    }
  },
  methods: {
    refreshHello() {
      if (this.name) {
        fetch("/api/test/" + this.name, {"method": "GET"})
            .then(response => response.json())
            .then(result => this.hello = result);
      }
    },
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


