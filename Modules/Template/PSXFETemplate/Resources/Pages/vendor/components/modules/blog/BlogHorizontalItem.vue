<template>
    <div class="rounded-lg cursor-pointer w-full h-full shadow-sm overflow-hidden bg-feAchromatic-50 dark:bg-feAchromatic-800 hover:shadow-md" v-on:click="onClick != null ? onClick(blog) : null">
        <div class="h-56 overflow-hidden">

            <img alt="Placeholder" class="transform transition duration-500 hover:scale-110 w-full h-full object-cover" width="360px" height="100px"
                v-lazy=" { src: $page.props.thumb3xUrl + '/' + blog.defaultPhoto.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                >
        </div>

        <div class="p-4" v-if="blog">
            <ps-label class="h-14 font-semibold text-xl dark:text-feSecondary-200 truncate-2-lines"> {{ blog.name }} </ps-label>
            <div class="flex items-center gap-2 mt-2">
                <ps-label class="font-medium text-base text-feSecondary-400 dark:text-feSecondary-200">Admin</ps-label>
                <ps-label textColor="text-sm font-medium text-feAchromatic-100">{{ $t("|") }}</ps-label>
                <ps-label class="font-normal text-base text-feSecondary-500 dark:text-feSecondary-200">{{ moment(blog.addedDate).format($page.props.dateFormat) }}</ps-label>
            </div>
        </div>
    </div>
</template>

<script>
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import { useBlogStoreState } from "@templateCore/store/modules/blog/BlogStore";
import Blog from '@templateCore/object/Blog';
import moment from "moment";

export default {
    name : "BlogHorizontalItem",
    components : {
        PsLabel
    },
    props : {
        blog : { type : Blog } ,
        onClick : Function,
        dateFormat:{ type : String} ,
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
.truncate-2-lines {
    height: 2.8em;
    /* double the size of line-height */
    line-height: 1.4em;
    overflow: hidden;
    -webkit-box-orient: vertical;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
}
</style>
