<template>
<form v-if="display">
  <div class="row">
    <div class="col align-self-center gy-2">
      <BoutSocial type="google"></BoutSocial>
    </div>
    <div class="col align-self-center gy-2">
      <BoutSocial type="github"></BoutSocial>
    </div>
  </div>
</form>
</template>

<script setup>
const props = defineProps(['isConnected'])

</script>

<script>

import BoutSocial from "./ButtonSocial.vue";

export default {
  data() {
    return {
      display:true,
      props:{
        isConnected: Boolean
      }
    };
  },
  components: {
    BoutSocial,
  }
  ,mounted() {
    if(this.isConnected === true){
      this.display = false
    }
  }
};

</script>