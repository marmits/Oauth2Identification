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

  <Message></Message>
  <SocialConnect></SocialConnect>
  <Logout></Logout>

  <input type="text" v-model="name">
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
      hello: ''
    }
  },
  methods: {
    refreshHello() {

      if (this.name) {
        fetch("/api/test/" + this.name, {"method": "GET"})
            .then(response => response.json())
            .then(result => this.hello = result);
      }
    }

  }
};
</script>


