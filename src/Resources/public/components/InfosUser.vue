<template>

  <div v-if="isConnected">
    <h1>Private Page</h1>
    <p v-if="!datas">Loading Oauth user informations...</p>
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
import { UserOauth } from '../js/User'
const props = defineProps(['isConnected'])
import {ref} from 'vue'
const datas = ref(null)
  if (props.isConnected) {
    const getUserInfos = async() => {
      const response = await UserOauth.setUserInfos()
      datas.value = response
      const event = new CustomEvent("oauthUserInfos", { detail: response });
      document.dispatchEvent(event);
    }
    getUserInfos()
  }
</script>

<script>
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
      that.displayInfos = that.displayInfos === false;
      return this
    }
  }
}
</script>

