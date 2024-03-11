<template>
    <Head :title="$t('blog_list__blog')"/>
  <ps-content-container>
    <template #content>
        <div class="mt-32 mb-10">
            <!-- breadcrumb Start -->
            <ps-breadcrumb-2 :items="breadcrumb"></ps-breadcrumb-2>
            <!-- breadcrumb end -->
            <div class="w-full mt-6">
               <div v-if="blogStore.blogList?.data != null && initial == false">

                    <!-- Blog Card Start -->
                    <blog-card  :blog="getRandomBlog"></blog-card>
                    <!-- Blog Card End -->

                    <!-- Horizontal Blog Item Start -->
                    <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-9 gap-6 mt-6">
                        <div class="w-full col-span-4 sm:col-span-2 md:col-span-3" v-for="blog in blogStore.blogList.data" :key="blog.id">
                            <ps-route-link
                                :to="{
                                    name: 'fe_blog_detail',
                                    params: { blogId: blog.id ,blogName: blog.name},
                                }"
                            >
                                <blog-horizontal-item :blog="blog" />
                            </ps-route-link>
                        </div>
                    </div>
                    <!-- Horizontal Blog Item End -->
               </div>
               <div v-else-if="blogStore.loading.value == true && initial == false">
                    <div class="grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4 gap-4 sm:gap-3.5 lg:gap-4">
                        <div class="w-full col-span-4 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3" v-for="i in 3" :key="i">
                            <item-horizontal-skeletor-item />
                        </div>
                    </div>
                </div>
                <div v-else-if="initial == false" class="flex text-center content-center flex-col">
                    <ps-no-data @handleRefresh="handleRefresh"
                    :name="'list__blogs_no_result'"></ps-no-data>
                </div>

                <ps-button v-if="blogStore.loading.value == false && initial == false" class="mx-auto mt-6" @click="loadMore" :class="blogStore.isNoMoreRecord.value || blogStore.blogList?.data == null ? 'hidden' : ''">{{ blogStore.blogList?.data != null ? $t("list__load_more") : $t("refresh") }} </ps-button>
                <ps-button v-else-if="initial == false" class="mx-auto mt-6" @click="loadMore" :disabled="true"> {{ $t("list__loading") }}</ps-button>
            </div>

            <div class="">
                <ps-ad-sense adFormat="horizontal"></ps-ad-sense>
            </div>
        </div>
    </template>
  </ps-content-container>
    <!-- google adsense desktop view -->
   <div class="">
      <ps-ad-sense adFormat="horizontal"></ps-ad-sense>
    </div>
    <ps-loading-dialog ref="ps_loading_dialog"/>
</template>

<script lang="ts">

// Vue
import { computed, ref,onMounted } from 'vue';
import { trans } from 'laravel-vue-i18n';
import { Head } from '@inertiajs/vue3';

import PsContentContainer from "@template1/vendor/components/layouts/container/PsContentContainer.vue";
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader4 from '@template1/vendor/components/core/label/PsLabelHeader4.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsAdSense from '@template1/vendor/components/core/adsense/PsAdSense.vue';
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import PsNoData from '@template1/vendor/components/core/noData/PsNoDataList.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';

// Models
import { useBlogStoreState } from "@templateCore/store/modules/blog/BlogStore";

// Modules
import BlogHorizontalItem from "@template1/vendor/components/modules/blog/BlogHorizontalItem.vue";
import BlogCard from "@template1/vendor/components/modules/blog/BlogCard.vue";
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import PsApi from '@templateCore/api/common/PsApi';

export default {
  name: "BlogListView",
  components: {
    PsAdSense,
    PsContentContainer,
    BlogHorizontalItem,
    PsButton,
    PsLabel,
    PsLabelHeader4,
    PsRouteLink,
    PsBreadcrumb2,
    BlogCard,
    PsNoData,
    Head,
    PsLoadingDialog
  },
  layout: PsFrontendLayout,
  props: ['mobileSetting', 'blogs'],
  setup(props) {

    const blogStore = useBlogStoreState();
    const psValueStore = PsValueStore();
    const locationId = ref(psValueStore.locationId);
    const loginUserId = psValueStore.getLoginUserId();
    const initial = ref(true);
    const ps_loading_dialog = ref();

    blogStore.limit = props.mobileSetting?.default_loading_limit ?? 9;

    blogStore.paramHolder.locationId = locationId.value;

    setTimeout(async () => {
        await loadBlogList();
    });
    async function loadBlogList(){
        // blogStore.decisionForDataCalling(loginUserId, blogStore.paramHolder, props.blogs);
        if(await PsApi.checkIsEmpty(props.blogs)){
            await blogStore.resetBlogListProps(props.blogs);
        } else {
            await blogStore.resetBlogList(loginUserId, blogStore.paramHolder);
        }

        ps_loading_dialog.value.closeModal();
        initial.value = false;
    }

    const getRandomBlog = computed(() => getRandBlog());

    function loadMore() {
      blogStore.loadBlogList(loginUserId, blogStore.paramHolder);
    }

    function handleRefresh(){
        blogStore.resetBlogList(loginUserId,blogStore.paramHolder);
    }

    function getRandBlog(){
        // length of blogStore.blogList.data
        const lenghtOfBlog = blogStore.blogList.data.length;
        // get random number
        const randNumber = Math.floor(Math.random() * lenghtOfBlog);

        // get a random blog in blogStore.blogList.data
        const getRandomBlog = blogStore.blogList.data[randNumber] ?? null;

        return getRandomBlog;
    }
    onMounted(() => {
        if(initial.value == true && blogStore.blogList?.data == null){
            ps_loading_dialog.value.openModal();
        }
    });

    return {
      blogStore,
      getRandomBlog,
      loadMore,
      handleRefresh,
      initial,
      ps_loading_dialog
    };
  },
  computed: {
    breadcrumb() {
      return [
        {
          label: trans("ps_nav_bar__home"),
          url: route("dashboard"),
        },
        {
          label: trans("blog_list__blog")+"s",
          color: "text-fePrimary-500",
        },
      ];
    },
  }
};
</script>

<style>
textarea:focus, input:focus{
    outline: none;
}
*:focus {
    outline: none;
}
</style>
