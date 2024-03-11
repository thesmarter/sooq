<template>
    <div class="relative w-full flex shadow-sm mb-4 rounded-lg hover:rounded-lg items-end ">
        <div class="overflow-hidden w-full h-96" >
            <img class="transform transition duration-500 hover:scale-110 w-full h-full rounded-lg hover:rounded-lg object-cover" style="cursor: auto;"
            v-lazy=" { src: $page.props.uploadUrl + '/' + blog?.defaultPhoto?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }">
        </div>
        <div class="w-full h-full rounded-lg bg-black absolute opacity-50"></div>
        <div class="absolute w-full lg:w-4/5 px-4 sm:px-6 pb-2" style="cursor: auto;">
            <ps-label class="truncate-1-lines text-feAchromatic-50 font-semibold mt-2 sm:mt-2 text-2xl" textColor="text-feAchromatic-50" style="cursor: auto;">
                {{ blog.name }}
            </ps-label>
            <div class="flex mb-3">
                <span class="flex text-xs sm:text-sm pe-3 rtl:space-x-reverse space-x-2s text-feAchromatic-100">
                    Admin
                </span>
                <span class="flex text-xs sm:text-sm ms-3 ps-5 border-s-2  rtl:space-x-reverse space-x-2s text-feAchromatic-100">
                    {{ moment(blog.addedDate).format($page.props.dateFormat) }}
                </span>
            </div>
            <p class="truncate-2-lines leading-relaxed mb-4 text-base sm:text-lg text-feAchromatic-100"> {{blog.description}} </p>
            <ps-route-link :to="{ name: 'fe_blog_detail', params: { blogId: blog.id, blogName: blog.name  }}">
                <ps-button class="mb-3" rounded="rounded" colors="bg-feAchromatic-50 dark:bg-feAchromatic-800 dark:text-feAchromatic-200 hover:text-feAchromatic-50" border="border border-feAchromatic-300 dark:border-feAchromatic-500">{{ $t('blog__read_more') }}</ps-button>
            </ps-route-link>
        </div>
    </div>
</template>

<script lang="ts">
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import { useBlogStoreState } from "@templateCore/store/modules/blog/BlogStore";
import Blog from '@templateCore/object/Blog';
import moment from "moment";

export default {
    name : "BlogHorizontalItem",
    components : {
        PsLabel,
        PsButton,
        PsRouteLink
    },
    props : {
        blog : { type : Blog } ,
        onClick : Function,
        dateFormat:{ type : String,default: 'YYYY/MM/DD' } ,
    },
    setup() {
        // Inject Provider
        const blogStore = useBlogStoreState();
        return {
            blogStore,
            moment
        }
    }
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
.truncate-2-lines {
    height: 3em;
    /* double the size of line-height */
    line-height: 1.4em;
    overflow: hidden;
    -webkit-box-orient: vertical;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
}
</style>

