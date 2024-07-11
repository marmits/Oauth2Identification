<template>

  <div v-if="isConnected">
    <h1>Private Page</h1>
    <div v-if="!datas" class="loadingspinner">
      <div class="spinner-border text-primary m-2" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading Oauth user informations..</span>
      </div>
    </div>

    <ul v-else>
      <li><img class="picture" :src="`${datas['picture']}`" /></li>
      <li><button @click.prevent="toggle" id="private_info" type="button" class="btn btn-primary mb-3">Infos From API</button></li>
      <li><span>{{ datas['name'] }}</span></li>
      <li><span>{{ datas['email'] }}</span></li>
    </ul>

    <div v-show="active" class="infos_user table-responsive">
      <table v-if="datas"  class="table">
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
import {ref, watch} from 'vue'
const datas = ref(null)
const active = ref(false)

function toggle(){
  active.value = !active.value
}

if(props.isConnected) {
    const getUserInfos = async() => {
      const response = await UserOauth.setUserInfos()
      datas.value = response
      const event = new CustomEvent("oauthUserInfos", { detail: response })
      document.dispatchEvent(event)
    }
    getUserInfos()
}


</script>


