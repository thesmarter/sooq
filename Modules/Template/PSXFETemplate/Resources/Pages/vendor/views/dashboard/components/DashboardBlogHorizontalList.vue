<template>
    <div v-if="blogStore.hasData()">
        <div class="flex justify-between items-center mt-10 mb-6">
                <ps-label-header-5 class="font-semibold">{{ $t("home__fe_blogs") }}</ps-label-header-5>
                <ps-route-link :to="{ name: 'fe_blog' }">
                    <ps-button class="flex flex-row" padding="p-2 sm:px-4 sm:py-2" shadow="shadow-sm" rounded="rounded"
                        hover="" focus="" border="border border-feSecondary-200 dark:border-feSecondary-800"
                        colors="bg-feAchromatic-50 text-feSecondary-800 dark:bg-feSecondary-800 dark:text-feSecondary-200">
                        <ps-label class="hidden sm:inline">{{ $t("list_fe__view_all_label") }}</ps-label>
                        <ps-icon class="sm:ms-2 block rtl:hidden" textColor="dark:text-feSecondary-200" name="rightChervon"
                            h="24" w="24" />
                        <ps-icon class="sm:ms-2 hidden rtl:block" textColor="dark:text-feSecondary-200" name="leftChervon"
                            h="24" w="24" />
                    </ps-button>
                </ps-route-link>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-7">
                <div class="hidden lg:block" v-for="blog in blogStore.blogList?.data?.slice(0, 3)" :key="blog.id">
                    <ps-route-link 
                        :to="{ name: 'fe_blog_detail', params: { blogId: blog.id, blogName: blog.name } }">
                        <blog-horizontal-item 
                            :blog="blog" />
                    </ps-route-link>
                </div>
                <div class="block lg:hidden" v-for="blog in blogStore.blogList?.data?.slice(0, 2)" :key="blog.id">
                    <ps-route-link :to="{ name: 'fe_blog_detail', params: { blogId: blog.id, blogName: blog.name } }">
                        <blog-horizontal-item 
                            :dateFormat="$page.props.dateFormat" 
                            :blog="blog" />
                    </ps-route-link>
                </div>
            </div>
    </div>
</template>

<script>
// Libs
import { onMounted } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelHeader5 from '@template1/vendor/components/core/label/PsLabelHeader5.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import BlogHorizontalItem from "@template1/vendor/components/modules/blog/BlogHorizontalItem.vue";
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useBlogStoreState } from "@templateCore/store/modules/blog/BlogStore";

export default {
    name: 'DashboardBlogHorizontalList',
    components: {
        PsLabel,
        PsLabelHeader5,
        PsButton,
        PsRouteLink,
        PsIcon,
        BlogHorizontalItem
    },
    setup() {
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const blogStore = useBlogStoreState('dashboard_blog');
        blogStore.paramHolder.locationId = localStorage.locationId ?? '';

        onMounted(() => {
            blogStore.resetBlogList(loginUserId, blogStore.paramHolder);
        });

        return {
            blogStore
        }
    }

}
</script>
