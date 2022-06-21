<template>
  <div>
    <div class="w-1/2 mx-auto">
      <div
        class="
          w-full
          shadow-2xl
          subpixel-antialiased
          rounded
          h-64
          bg-black
          border-black
          mx-auto
        "
      >
        <div
          class="
            flex
            items-center
            h-6
            rounded-t
            bg-gray-100
            border-b border-gray-500
            text-center text-black
          "
          id="headerTerminal"
        >
          <div
            class="
              flex
              ml-2
              items-center
              text-center
              border-red-900
              bg-red-500
              shadow-inner
              rounded-full
              w-3
              h-3
            "
            id="closebtn"
          ></div>
          <div
            class="
              ml-2
              border-yellow-900
              bg-yellow-500
              shadow-inner
              rounded-full
              w-3
              h-3
            "
            id="minbtn"
          ></div>
          <div
            class="
              ml-2
              border-green-900
              bg-green-500
              shadow-inner
              rounded-full
              w-3
              h-3
            "
            id="maxbtn"
          ></div>
          <div class="mx-auto pr-16" id="terminaltitle">
            <p class="text-center text-sm">Terminal</p>
          </div>
        </div>
        <div
          class="pl-1 pt-1 h-auto text-green-200 font-mono text-xs bg-black"
          id="console"
        >
          <p class="pb-1">Last login: Wed Sep 25 09:11:04 on ttys002</p>
          <p class="pb-1">LaravelCognitoOauth:Devprojects {{ me.name }}$</p>
          <p class="pb-1">└─▪cognito_sub: {{ me.cognito_sub }}</p>
          <p class="pb-1">└─▪cognito_google_sub: {{ me.cognito_google_sub }}</p>
          <p class="pb-1">└─▪cognito_apple_sub: {{ me.cognito_apple_sub }}</p>
          <p class="pb-1">└─▪expired_at: {{ me.expired_at }}</p>
        </div>
      </div>
    </div>
    <div class="w-1/2 mx-auto mt-10">
      <button
        type="submit"
        class="
          bg-white
          hover:bg-gray-100
          text-gray-800
          font-semibold
          py-2
          px-4
          border border-gray-400
          rounded
          shadow
        "
        @click="logout"
      >
        Logout
      </button>
    </div>
  </div>
</template>

<script>
import { onMounted } from "@vue/runtime-core";
import useMe from "../../../composables/me";

export default {
  setup() {
    const { me, getMe } = useMe();
    onMounted(getMe);

    function logout() {
      console.log("called logout function");
      const url = process.env.MIX_APP_URL;
      const logoutUri = "http://mypage.localhost/login";

      window.location.href = `${url}/logout?logout_uri=${logoutUri}`;
    }

    return { logout, me };
  },
};
</script>