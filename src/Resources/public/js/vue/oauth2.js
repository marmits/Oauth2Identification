export default {
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