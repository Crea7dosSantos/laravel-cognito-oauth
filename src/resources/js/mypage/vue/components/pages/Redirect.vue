<template>
  <h1>Redirect</h1>
</template>

<script>
import { onMounted } from "@vue/runtime-core";
import { useRoute } from "vue-router";
import router from "../../router";
import axios from "axios";
import Cookie from "js-cookie";

export default {
  setup() {
    const clientId = process.env.MIX_SPA_CLIENT_ID;
    const clientSecret = process.env.MIX_SPA_CLIENT_SECRET_ID;
    const redirectUri = "http://mypage.localhost/auth/callback";
    const route = useRoute();
    const code = route.query.code;
    console.log(route.query);

    onMounted(() => {
      console.log("Component is mounted!");

      axios
        .post("http://localhost/oauth/token", {
          grant_type: "authorization_code",
          client_id: clientId,
          client_secret: clientSecret,
          redirect_uri: redirectUri,
          code: code,
        })
        .then((response) => {
          console.log(response.data);
          Cookie.set("access_token", response.data.access_token);
          Cookie.set("refresh_token", response.data.refresh_token);

          router.push({ name: "hello" });
        });
    });
  },
};
</script>