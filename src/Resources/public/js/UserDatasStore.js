import { reactive } from 'vue'

class UserDatas{

    constructor() {
        this.state = {
            'userInfos': '',
            'valid': false,
            'options' : []
        }
    }

    getState(){
        return this.state;
    }

    setUserInfos(value){
        this.state.userInfos = value
        return this;
    }

    getUserInfos(){
        return this.state.userInfos;
    }


}

export const UserDatasStore = reactive(new UserDatas())
