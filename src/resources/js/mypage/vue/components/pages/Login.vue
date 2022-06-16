<template>
  <h1>Login画面に遷移します</h1>
</template>

<script>
import { onBeforeMount } from "@vue/runtime-core";

export default {
  setup() {
    const clientId = "968ee01e-2f04-4a73-ae97-11e80003a5f6";
    const redirectUri = "http://mypage.localhost/auth/callback";
    const responseType = "code";
    const state = "hohgeodjakrjejareljarlejaljrelkaj";

    onBeforeMount(() => {
      console.log("Component is mounted!");

      window.location.href = `http://localhost/oauth/authorize?client_id=${clientId}&redirect_uri=${redirectUri}&response_type=${responseType}&scope&state=${state}`;
    });
  },
};
</script>