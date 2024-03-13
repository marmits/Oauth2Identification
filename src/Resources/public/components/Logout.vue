<template>
  <form v-if="isConnected">
    <div class="mb-3">
      <div class="btn-group" role="group" aria-label="">
        <a class="btn btn-danger" href="logout" role="button">Logout</a>
      </div>
    </div>
  </form>


</template>

<script setup>
const props = defineProps(['isConnected'])

</script>

<script>

export default {
  data() {
    return {
      who: "World",
      props:{
        isConnected: Boolean
      }
    };
  },
};


</script>