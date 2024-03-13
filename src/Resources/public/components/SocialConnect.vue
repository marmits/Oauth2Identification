<template>
<form>
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

<script>

import BoutSocial from "./ButtonSocial.vue";

export default {
  data() {
    return {

    };
  },
  components: {
    BoutSocial,
  }
};

</script>