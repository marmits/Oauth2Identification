<template>
  <div v-if="isConnected">
    <h1>Private Page</h1>
    <p v-if="!datas">Chargement...</p>
    <ul v-else>
      <li><img class="picture" :src="`${datas['avatar_url']}`" /></li>
      <li><button @click="setDisplayInfos" id="private_info" type="button" class="btn btn-primary mb-3">Infos From API</button></li>
      <li><span>{{ datas['name'] }}</span></li>
      <li><span>{{ datas['email'] }}</span></li>
    </ul>

    <div  v-show="!!displayInfos" class="infos_user table-responsive">
      <table  v-if="datas"  class="table">
        <tbody>
        <tr v-for="(value, index) in datas">
          <th scope="col">{{index}}</th>
          <th scope="col">{{value}}</th>
        </tr>
        </tbody>
      </table>
    </div>
  </div>

</template>

<script setup>
const props = defineProps(['isConnected'])
import { ref} from 'vue'
const datas = ref(null)
async function fetchData() {
  datas.value = null
  const res = await fetch("/api/user/datas", {"method": "GET"})
  datas.value = await res.json()
}
if(props.isConnected) {
  fetchData()
}

</script>

<script>
import { ref } from 'vue'
export default {
  data() {
    return {
      displayInfos: false,
      props:{
        isConnected: Boolean
      }
    }
  },
  methods: {
    setDisplayInfos(){
      let that = this
      if(that.displayInfos === false){
        that.displayInfos = true
      } else {
        that.displayInfos = false
      }
      return this
    }
  }

}
</script>

