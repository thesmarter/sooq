<template>
    <Head :title="$t('blogblog_list__blogDetail')"/>
    <ps-content-container>
        <template #content>
            <div class="lg:mt-36 sm:mt-36 mt-28">
                <ps-breadcrumb-2 :items="breadcrumb" class="" />

                <div class="flex flex-col justify-items-center">
                    <div class="lg:my-12 mt-7 mb-12">
                        <div class="text-center sm:mb-10 mb-6 grid justify-items-center">
                            <ps-label-header-3 textColor="dark:text-feSecondary-200 text-feSecondary-800" class="w-full lg:w-2/3 truncate-3-lines font-semibold sm:mb-8 mb-4"> {{blogStore.blog?.data?.name}} </ps-label-header-3>
                            <p class="text-sm font-medium text-feSecondary-500 dark:text-feSecondary-400">Admin <span class="mx-6">|</span>
                            {{moment(blogStore.blog?.data?.addedDate).format($page.props.dateFormat)}}
                            </p>
                        </div>

                        <div class=" w-auto cursor-pointer grid justify-items-center">
                            <img alt="Placeholder" width="300px" height="200px" class="block w-full rounded-lg lg:h-96 h-56 sm:h-72  object-cover"
                            v-lazy=" { src: $page.props.uploadUrl + '/' + blogStore.blog?.data?.defaultPhoto?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                            >
                            <div class="lg:mt-8 mt-6 w-full lg:w-2/3">
                                <p v-if="blogStore.blog.data != null">
                                    <span class=" font-normal font-18 text-lg text-feSecondary-600 dark:text-feSecondary-200" v-html="blogStore.blog.data.description"> </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </ps-content-container>
     <!-- google adsense desktop view -->
    <div class="w-mobile sm:w-median lg:w-large h-auto mx-auto">
        <ps-ad-sense adFormat="horizontal"></ps-ad-sense>
    </div>
</template>

<script lang="ts">
//Vue
// import { useRoute } from 'vue-router';
import { Head } from "@inertiajs/vue3";

import PsContentContainer from "@template1/vendor/components/layouts/container/PsContentContainer.vue";
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsLabelHeader3 from '@template1/vendor/components/core/label/PsLabelHeader3.vue';
import PsAdSense from '@template1/vendor/components/core/adsense/PsAdSense.vue';

//Models
import { useBlogStoreState } from '@templateCore/store/modules/blog/BlogStore';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { trans } from 'laravel-vue-i18n';
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import moment from 'moment';
import { Head } from '@inertiajs/vue3';
import PsApi from '@templateCore/api/common/PsApi';

export default {
    name : "BlogDetailView",
    components : {
        PsBreadcrumb2,
        PsContentContainer,
        PsRouteLink,
        PsLabelHeader3,
        PsAdSense,
        Head
    },
    props:['blog'],
    layout: PsFrontendLayout,
    setup(props) {
        // const route = useRoute();
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        // Inject Provider
        const blogStore = useBlogStoreState();
        if(PsApi.checkIsEmpty(props.blog)){
            blogStore.loadBlogProps(props.blog);
        } else {
            blogStore.loadBlog(props.blog.blogId,loginUserId);
        }
        
        return {
            blogStore,
            moment
        }
    },
    computed: {
        breadcrumb() {
            return [
                {
                    label: trans('item_list__home'),
                    url: route('dashboard')
                },
                {
                    label: trans('blogblog_list__blog'),
                    url: route('fe_blog'),
                },
                {
                    label: trans('blogblog_list__blogDetail'),
                    color: "text-fePrimary-500"
                }
            ]
        }
    },
}
</script>
<style scoped>
.truncate-1-lines {
    height: 1.7em;
    /* double the size of line-height */
    line-height: 1.4em;
    overflow: hidden;
    -webkit-box-orient: vertical;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
}
.truncate-3-lines {
    overflow: hidden;
    -webkit-box-orient: vertical;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
}
</style>

