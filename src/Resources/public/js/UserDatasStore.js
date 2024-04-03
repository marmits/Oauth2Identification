import { reactive } from 'vue'

class UserDatasStore{

    constructor() {
        this.state = {
            'userInfos': ''
        }
    }

    setState(value){
        this.state.userInfos = value
        return this;
    }

    getState(){
        return this.state;
    }
}
let userDatas = new UserDatasStore();

export const store = reactive(userDatas)
