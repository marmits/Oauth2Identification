
class User{

    constructor() {

    }

    async setUserInfos(){
        const response = await fetch("api/user/datas", {"method": "GET"});
        return await response.json();
    }

}

export const UserOauth = new User()

