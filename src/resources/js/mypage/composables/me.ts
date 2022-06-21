import { ref } from 'vue'
import axios from "axios"
import Cookies = require('js-cookie')
import router from "../vue/router"

export default function useMe() {
    const me = ref([])

    const getMe = async () => {
        try {
            axios.defaults.withCredentials = true;
            const response = await axios.get('http://api.localhost/me', {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: "Bearer " + Cookies.get("access_token"),
                },
                responseType: "json"
            });
            console.log(response.data)
            me.value = response.data;
        } catch (e: any) {
            console.log(e.response.data.message);
            router.push({ name: "login" });
        }
    }

    return {
        me,
        getMe
    }
}