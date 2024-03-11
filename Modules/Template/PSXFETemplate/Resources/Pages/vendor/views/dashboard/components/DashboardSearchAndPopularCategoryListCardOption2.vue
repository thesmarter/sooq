<template>
    <div class="mt-24 w-full">
            <div class="bg-feAchromatic-900 relative">
                <div class="w-full h-[680px] sm:h-[680px] object-cover relative flex justify-center items-center">
                    <img alt="Placeholder" class="w-full opacity-60 h-[680px] sm:h-[680px] object-cover"
                        v-lazy=" { src: $page.props.uploadUrl + '/'+ bannerImgPath , error: $page.props.sysImageUrl+'/default_photo.png', lifecycle: lazyOptions.lifecycle }"
                    >
                    <!-- <img alt="Placeholder" class="w-full opacity-60 h-[530px] sm:h-[485px] object-cover"
                        v-lazy=" { src: $page.props.uploadUrl + '/'+appInfoStore.appInfo.data?.frontendConfigSetting?.frontendBanner?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png', lifecycle: lazyOptions.lifecycle }"
                    > -->
                    <div v-if="is_banner_loading" class="opacity-60 w-full h-full flex flex-col justify-center items-center gap-10 absolute bg-gray-300">
                        <span class="loader"></span>
                        <span class="text-3xl text-gray-500 font-semibold">Loading</span>
                    </div>
                </div>
                <div class="absolute top-0 w-full h-full flex flex-col justify-center gap-5 sm:gap-8">
                    <div class="text-center">
                        <ps-label class="text-xl font-semibold mb-4 sm:text-4xl xl:text-5xl"
                            textColor="text-fePrimary-50">{{ $t("home__banner_header") }}</ps-label>
                        <ps-label class="text-base font-normal px-10" textColor="text-fePrimary-50">{{
                            $t("home__banner_desc") }}</ps-label>
                    </div>
                    <div class="bg-feAchromatic-50 dark:bg-feSecondary-800 rounded-3xl px-4 py-4 sm:py-2 mx-auto">
                        <search-for-large-screem />
                    </div>
                    <div class="">
                        <ps-label class="text-center text-sm font-normal mb-4" textColor="text-fePrimary-50">{{
                            $t("home__popular_categories") }}</ps-label>
                        <div class="flex justify-center flex-wrap gap-x-1 sm:gap-x-1 gap-y-1"
                            v-if="popularCategoryStore.itemList?.data != null">
                            <div v-for="category in popularCategoryStore.itemList.data.slice(0, 6)" :key="category.catId"
                                class="">
                                <ps-route-link :to="{
                                    name: showSubCat ? 'fe_sub_category' : 'fe_item_list',
                                    query: {
                                        cat_id: category.catId,
                                        cat_name: category.catName,
                                        status: 1
                                    }
                                }" @click="updateCategoryTouchCount(category.catId)">
                                    <div class="text-center cursor-pointer w-26 overflow-hidden bg-feAchromatic-50 dark:bg-feSecondary-700
                                        border border-feAchromatic-400 sm:rounded-md lg:rounded-md hover:bg-fePrimary-50 hover:border-fePrimary-500 hover:dark:bg-fePrimary-900">
                                    <div class="relative flex justify-center">
                                        
                                        <img alt="Placeholder" 
                                            class="w-10 h-10 pt-2 transform transition duration-500 hover:text-fePrimary-500 object-contain"
                                            v-lazy=" { src: $page.props.thumb2xUrl + '/' + category.defaultIcon.imgPath, 
                                                error: $page.props.sysImageUrl+'/default_photo.png' }">
                                        
                                        </div>

                                        <ps-label class="text-sm pt-1 pb-4 font-normal" 
                                            textColor="text-fePrimary-500"> 
                                                {{ category.catName.length > 8 ? category.catName.slice(0,8)+'...' : category.catName }} 
                                        </ps-label>
                                    </div>
                                </ps-route-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
// Libs
import { ref, reactive, onMounted } from 'vue';
// Components
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import SearchForLargeScreem from "@template1/vendor/views/search/SearchForLargeScreenOption2.vue";
// Providers
import { PsValueStore } from "@templateCore/store/modules/core/PsValueStore";
import { useCategoryStoreState } from "@templateCore/store/modules/category/CategoryStore";
import { useTouchCountStoreState } from '@templateCore/store/modules/category/TouchCountStore';
//Holder
import TouchCountParameterHolder from '@templateCore/object/holder/TouchCountParameterHolder';

export default {
    name: 'DashboardSearchAndPopularCategoryListCardOption2',
    components: {
        PsLabel,
        PsButton,
        PsRouteLink,
        PsIcon,
        SearchForLargeScreem
    },
    props: {
        bannerImgPath : {
            type : String,
            default : ''
        },
        limit: {
            type: Number,
            default: 12
        },
        showSubCat: {
            type: Boolean,
            default: true
        }
    },
    setup(props) {
        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();

        const is_banner_loading = ref(true);
        const lazyOptions = reactive({
            lifecycle: {
                loading: () => {
                    is_banner_loading.value = true;
                },
                error: () => {
                    is_banner_loading.value = false;
                },
                loaded: () => {
                    is_banner_loading.value = false;
                }
            }
        });

        //touch count
        const touchCountStore = useTouchCountStoreState();
        const touchCountHolder = new TouchCountParameterHolder();
        touchCountHolder.typeName = 'category';
        touchCountHolder.userId = loginUserId;

        //category
        const popularCategoryStore = useCategoryStoreState('dashboard_popular_cat');
        popularCategoryStore.limit = props.limit;
        popularCategoryStore.paramHolder.keyword = '';
        popularCategoryStore.paramHolder.orderType = 'desc';
        popularCategoryStore.paramHolder.orderBy = 'category_touch_count';

        function updateCategoryTouchCount(catId) {
            touchCountHolder.typeId = catId;
            touchCountStore.postTouchCount(loginUserId, touchCountHolder);
        }

        onMounted(() => {
            popularCategoryStore.resetCategoryList(loginUserId, popularCategoryStore.paramHolder);
        });

        return {
            lazyOptions,
            is_banner_loading,
            popularCategoryStore,
            updateCategoryTouchCount
        }
    }

}
</script>

<style scoped>
.loader{
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 15px solid #4f5a84;
    border-bottom-color: transparent;
    animation: animate 1.2s linear infinite;
}

@keyframes animate{
    0%{
        transform: rotate(0deg);
    }
    100%{
        transform: rotate(360deg);
    }
}

</style>
