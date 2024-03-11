<template>

        <img alt="Placeholder"  height="auto"  class="w-100  object-cover  rtl:space-x-reverse space-x-48"
                                v-lazy=" { src: $page.props.sysImageUrl + '/undraw.png', loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                            >
                                <ps-label textColor="text-feSecondary-500 dark:text-feAchromatic-50 lg:text-xl sm:text-lg text-base font-medium" class="mt-10 flex-grow-0 mx-auto"> {{ $t(name) }} </ps-label>
                                <ps-button class="w-60 mx-auto mt-8" @click="refresh" > {{ $t("list__refresh") }} </ps-button>

</template>

<script lang="ts">
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';

export default {
    name: 'PsNavMenu',
    components: {
        PsButton,
        PsLabel
    },
    props: {
        name : {
            type: String,
            default : 'no_data'
        },
    },
    setup(props,{emit}) {

        function refresh(){
            emit('handleRefresh')
        }
        return {
            refresh,
        }
    }
}
</script>PsLink
