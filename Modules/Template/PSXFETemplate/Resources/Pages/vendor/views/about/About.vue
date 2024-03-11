<template>
  <div>
    <ps-content-container>
      <template #content>
        <div class="mt-36 flex flex-col ">
          <div class="flex flex-col">

            <div class=" w-auto cursor-pointer my-2 ">
              <ps-label class="text-center"> {{ aboutUsStore.aboutus?.data?.aboutTitle }} </ps-label>

              <img alt="Placeholder" width="15px" height="10px" class="block w-full h-128 object-cover mt-4"
                v-lazy="{ src: $page.props.uploadUrl + '/' + aboutUsStore.aboutus?.data?.defaultPhoto?.imgPath, loading: $page.props.sysImageUrl + '/loading_gif.gif', error: $page.props.sysImageUrl + '/default_photo.png' }">
              <div class="mt-4">

                <p class="mt-4" v-if="aboutUsStore.aboutus?.data?.aboutDescription != ''">
                  <ps-label v-html="aboutUsStore.aboutus?.data?.aboutDescription"> </ps-label>
                </p>
              </div>
            </div>
          </div>
        </div>
        <!-- google adsense desktop view -->
        <!-- <div class="mx-auto">
          <ps-ad-sense adFormat="horizontal"></ps-ad-sense>
        </div> -->

      </template>

    </ps-content-container>

    <div class="object-cover relative flex justify-center items-center mt-6">
      <div class="h-96 w-full">
        <img class="w-full h-96 object-cover dark:hidden block"
          v-lazy="{ src: $page.props.sysImageUrl + '/connect_with_us_light.png' }" alt="">
        <img class="w-full h-96 object-cover hidden dark:block"
          v-lazy="{ src: $page.props.sysImageUrl + '/connect_with_us_dark.png' }" alt="">
      </div>

      <div class="absolute">
        <ps-label-header-4 class="text-center mt-12 mb-8"> Connect With Us </ps-label-header-4>

        <div class="mx-8 grid grid-cols-2 sm:grid-cols-3 gap-y-8 gap-x-24 pb-10">
          <!-- for phone -->
          <div class=" rtl:space-x-reverse space-x-2 flex items-center mt-2"
            v-if="aboutUsStore.aboutus?.data?.aboutPhone != null && aboutUsStore.aboutus?.data?.aboutPhone != ''">

            <div class="w-6 h-6 rounded flex bg-fePrimary-500 justify-center items-center">
              <ps-icon name="phoneSquare" w="35" h="35" textColor="text-feAchromatic-50 dark:text-feAchromatic-800" />
            </div>

            <p class="px-2 hover:text-fePrimary-500 dark:hover:text-fePrimary-500 dark:text-feSecondary-300">
              <p class="lg:text-sm sm:text-xs text-xxs font-medium"> {{ $t("about__phone") }} </p>
              <p class="leading-normal lg:text-xs sm:text-xxs text-3xs"> {{ aboutUsStore.aboutus?.data?.aboutPhone }}</p>
            </p>
          </div>

          <!-- for website -->
          <div class=" rtl:space-x-reverse space-x-2 flex items-center mt-2"
            v-if="aboutUsStore.aboutus?.data?.aboutWebsite != null && aboutUsStore.aboutus?.data?.aboutWebsite != ''">
            <div class="w-6 h-6 rounded flex justify-center items-center">
              <ps-icon name="globe" w="35" h="35" textColor="text-fePrimary-500" />

            </div>
            <p class="px-2 hover:text-fePrimary-500 dark:hover:text-fePrimary-500 dark:text-feSecondary-300">
              <p class="lg:text-sm sm:text-xs text-xxs font-medium"> {{ $t("about__website") }} </p>
              <p class="leading-normal lg:text-xs sm:text-xxs text-3xs">  {{ aboutUsStore.aboutus?.data?.aboutWebsite }}
              </p>
            </p>
          </div>

          <!-- for facebook -->
          <div class=" rtl:space-x-reverse space-x-2 flex items-center mt-2"
            v-if="aboutUsStore.aboutus?.data?.facebook != null && aboutUsStore.aboutus?.data?.facebook != ''">

            <div class="w-6 h-6 rounded flex justify-center items-center">
              <ps-icon name="facebookSquare" w="36" h="36" textColor="text-fePrimary-500" />
            </div>
            <p class="px-2 hover:text-fePrimary-500 dark:hover:text-fePrimary-500 dark:text-feSecondary-300">
              <p class="lg:text-sm sm:text-xs text-xxs font-medium"> {{ $t("about__facebook") }} </p>
              <p class="leading-normal lg:text-xs sm:text-xxs text-3xs">  {{ aboutUsStore.aboutus?.data?.facebook }}
              </p>
            </p>
          </div>

          <!-- for twitter -->
          <div class=" rtl:space-x-reverse space-x-2 flex items-center mt-2"
            v-if="aboutUsStore.aboutus?.data?.twitter != null && aboutUsStore.aboutus?.data?.twitter != ''">
            <div class="w-6 h-6 rounded flex bg-fePrimary-500 justify-center items-center">
              <ps-icon name="twitterSquare" w="35" h="35" textColor="text-feAchromatic-50 dark:text-feAchromatic-800" />
            </div>
            <p class="px-2 hover:text-fePrimary-500 dark:hover:text-fePrimary-500 dark:text-feSecondary-300">
              <p class="lg:text-sm sm:text-xs text-xxs font-medium">  {{ $t("about__twitter") }} </p>
              <p class="leading-normal lg:text-xs sm:text-xxs text-3xs">  {{ aboutUsStore.aboutus?.data?.twitter }}
              </p>
            </p>
          </div>

          <!-- for instagram -->
          <div class=" rtl:space-x-reverse space-x-2 flex items-center mt-2"
            v-if="aboutUsStore.aboutus?.data?.instagram != null && aboutUsStore.aboutus?.data?.instagram != ''">
            <div class="w-6 h-6 rounded flex bg-fePrimary-500 justify-center items-center">
              <ps-icon name="instagramSquare" w="35" h="35" textColor="text-feAchromatic-50 dark:text-feAchromatic-800" />
            </div>
            <p class="px-2 hover:text-fePrimary-500 dark:hover:text-fePrimary-500 dark:text-feSecondary-300">
              <p class="lg:text-sm sm:text-xs text-xxs font-medium"> {{ $t("about__instagram") }} </p>
              <p class="leading-normal lg:text-xs sm:text-xxs text-3xs">  {{ aboutUsStore.aboutus?.data?.instagram }}
              </p>
            </p>
          </div>

          <!-- for google plus -->
          <div class=" rtl:space-x-reverse space-x-2 flex items-center mt-2"
            v-if="aboutUsStore.aboutus?.data?.googlePlus != null && aboutUsStore.aboutus?.data?.googlePlus != ''">
            <div class="w-6 h-6 rounded flex bg-fePrimary-500 justify-center items-center">
              <ps-icon name="googlePlusSquare" w="35" h="35" textColor="text-feAchromatic-50 dark:text-feAchromatic-800" />
            </div>
            <p class="px-2 hover:text-fePrimary-500 dark:hover:text-fePrimary-500 dark:text-feSecondary-300">
              <p class="lg:text-sm sm:text-xs text-xxs font-medium"> {{ $t("about__google_plus") }} </p>
              <p class="leading-normal lg:text-xs sm:text-xxs text-3xs">  {{ aboutUsStore.aboutus?.data?.googlePlus }}
              </p>
            </p>
          </div>

          <!-- for youtube -->
          <div class=" rtl:space-x-reverse space-x-2 flex items-center mt-2"
            v-if="aboutUsStore.aboutus?.data?.youtube != null && aboutUsStore.aboutus?.data?.youtube != ''">
            <div class="w-6 h-6 rounded flex bg-fePrimary-500 justify-center items-center">
              <ps-icon name="youtubeSquare" w="35" h="35" textColor="text-feAchromatic-50 dark:text-feAchromatic-800" />
            </div>
            <p class="px-2 hover:text-fePrimary-500 dark:hover:text-fePrimary-500 dark:text-feSecondary-300">
              <p class="lg:text-sm sm:text-xs text-xxs font-medium"> {{ $t("about__youtube") }} </p>
              <p class="leading-normal lg:text-xs sm:text-xxs text-3xs">  {{ aboutUsStore.aboutus?.data?.youtube }}
              </p>
            </p>
          </div>

          <!-- for pinterest -->
          <div class=" rtl:space-x-reverse space-x-2 flex items-center mt-2"
            v-if="aboutUsStore.aboutus?.data?.pinterest != null && aboutUsStore.aboutus?.data?.pinterest != ''">
            <div class="w-6 h-6 rounded flex bg-fePrimary-500 justify-center items-center">
              <ps-icon name="pinterestSquare" w="35" h="35" textColor="text-feAchromatic-50 dark:text-feAchromatic-800" />
            </div>
            <p class="px-2 hover:text-fePrimary-500 dark:hover:text-fePrimary-500 dark:text-feSecondary-300">
              <p class="lg:text-sm sm:text-xs text-xxs font-medium"> {{ $t("about__pinterest") }} </p>
              <p class="leading-normal lg:text-xs sm:text-xxs text-3xs">  {{ aboutUsStore.aboutus?.data?.pinterest }}
              </p>
            </p>
          </div>
        </div>
      </div>

    </div>


  </div>
</template>

<script lang='ts'>
import { Head } from '@inertiajs/vue3';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelTitle4 from '@template1/vendor/components/core/label/PsLabelTitle4.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsAdSense from '@template1/vendor/components/core/adsense/PsAdSense.vue';

import PsContentContainer from "@template1/vendor/components/layouts/container/PsContentContainer.vue";
import { useAboutUsStoreState } from "@templateCore/store/modules/aboutus/AboutUsStore";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';

import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';

export default {
  name: "AboutView",
  components: {
    PsContentContainer,
    PsLabel,
    PsLabelTitle4,
    PsLabelCaption,
    PsLabelHeader4,
    PsAdSense,
    PsIcon,

    Head
  },
  layout: PsFrontendLayout,
  data() {
    return {
      fillColor: '#fffff', // Replace with your actual color class
    };
  },
  layout: PsFrontendLayout,

  setup() {
    const aboutUsStore = useAboutUsStoreState();
    const appInfoStore = usePsAppInfoStoreState();
    let psValueStore = PsValueStore();
    const loginUserId = psValueStore.getLoginUserId();
    aboutUsStore.loadAboutUs(loginUserId);

    return {
      aboutUsStore,
      appInfoStore,
      localStorage
    };
  },
};
</script>
