
import BtSocialConnect from "../../templates/SocialConnect.vue";

export default {
    components: {
        SocialConnect,
    },
    data() {
        return {
            name: '',
            hello: ''
        }
    },
    methods: {
        refreshHello() {

            if (this.name) {
                fetch("/api/test/" + this.name, {"method": "GET"})
                    .then(response => response.json())
                    .then(result => this.hello = result);
            }
        }

    }
};