<template>

    <div class="d-flex justify-content-center">
      <div class="btn-group" role="group" aria-label="Basic mixed styles example">
        <a v-bind:id="type" class="btn btn-warning connect_social" :href="`${type}_authorize`" role="button"><h2><i :class="`fa-brands fa-${type}`"></i></h2></a>
      </div>
    </div>

</template>

<script setup>
const props = defineProps(['type'])
</script>

