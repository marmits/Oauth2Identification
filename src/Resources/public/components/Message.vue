<template>
  <div v-if="checkError()" :class="`alert alert-${typeAlert} message`" role="alert">
    <h4 class="alert-heading">{{titre}}</h4>
  </div>
</template>


<script setup>
const props = defineProps(['isConnected', 'titre'])

</script>

<script>

export default {
  data() {
    return {
      name: "Message",
      isDisplay: true,
      isError: false,
      typeAlert: 'success',
      props:{
        titre: String,
        isConnected: Boolean
      }
    }
  },
  methods: {
    checkError(){
      return this.isError
    },
    setError(value){
      this.isError = value
      return this
    },
    setTypeAlert(){
      if(this.checkError() === true){
        this.typeAlert = 'danger'
        if(this.isConnected === false){
          this.typeAlert = 'success'
        }
      }
      return this
    }
  }
  ,mounted() {
    let that = this
    if(this.isConnected === false){
      this.setError(true)
    }
    else {
      this.setError(false)
    }
    this.setTypeAlert()
  }
};
</script>

