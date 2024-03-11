<template>
    <Head :title="$t('item_entry__title')"/>
    <item-upload-1 v-if="isShow || itemId != '0'" :authUser="$page.props.authUser" :thumb2xUrl="$page.props.thumb2xUrl" :dir="$page.props.dir" :sysImageUrl="$page.props.sysImageUrl" :itemId="itemId" :categoryId="query.categoryId" :categoryName="query.categoryName"/>
    <choose-category :mobileSetting="mobileSetting" v-if="!isShow && itemId == '0'"/>
</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import { defineComponent, onMounted ,ref} from 'vue'
import ItemUpload1 from './ItemUpload1.vue'

import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import ChooseCategory from './components/ChooseCategory.vue';

export default defineComponent({
    name: "ItemEntry",
    components:{
        ItemUpload1,
        Head,
        ChooseCategory
    },
    props: ['query', 'backendSetting' , 'categoryId' , 'categoryName', 'mobileSetting'],
    layout: PsFrontendLayout,
    setup(props) {
        const itemId = ref('0');
        const isShow = ref();
        if(props.query && props.query.itemId){
            itemId.value = props.query.itemId;
        }

        if (props.query.categoryId === undefined) {
            isShow.value = false;
        } else {
            isShow.value = true;
        }

        return {
            itemId,
            isShow
        }

    },
})
</script>
