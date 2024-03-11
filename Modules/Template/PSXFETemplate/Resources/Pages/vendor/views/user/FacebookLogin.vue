<style scoped>
.button{
  color:white;
  min-width: 150px;
  background-color: #000000a1;
  height: 2.5rem;
  border-radius: 2rem;
  font-weight: 400;
  font-size: 0.8rem;
}
</style> 
<template>
  <div>
    <button class="button" @click="logInWithFacebook"> {{ $t("facebook_login__login_with_facebook") }} </button>
  </div>
</template>
<script lang='ts'>
import { onMounted } from 'vue';
import Axios from 'axios';
import PsUtils from '@templateCore/utils/PsUtils';

export default {
  name:"facebookLoginView",
  setup() {

    onMounted(async () => {
        await loadFacebookSDK(document, "script", "facebook-jssdk");
        await initFacebook();
    });

    async function logInWithFacebook() {
      
        (window as any).FB.login(function(response) {
            if (response.authResponse) {
              
              alert("You are logged in &amp; cookie set!");

              Axios.get('https://graph.facebook.com/v2.12/me?fields=name,first_name,last_name,email&access_token=' + response.authResponse.accessToken, 
                ).then(response => {
                    PsUtils.log(response);

            }).catch(error => {
                PsUtils.log(error.message);
               
            });
            } else {
            alert("User cancelled login or did not fully authorize.");
            }
        });
        return false;
    }
    async function initFacebook() {
 
      (window as any).fbAsyncInit = function() {
         
        (window as any).FB.init({
          appId: "1238645106274146", //You will need to change this
          cookie: true, // This is important, it's not enabled by default
          version: "v13.0"
        });
      };
    }
    async function loadFacebookSDK(d, s, id) {
      let js,
        fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) {
        return;
      }
      js = d.createElement(s);
      js.id = id;
      js.src = "https://connect.facebook.net/en_US/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
    }

    return {
        logInWithFacebook
    }
  }
};
</script>
