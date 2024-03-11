<template>
    <ps-modal ref="psmodal" :isClickOut="false" maxWidth="410px" theme="rounded-lg p-6 bg-feAchromatic-50 dark:bg-feSecondary-800" >
        <template #title>
            <div class="flex items-center justify-between gap-4 mb-6">
                <ps-label textColor="text-xl font-semibold text-feSecondary-800 dark:text-feSecondary-200">  {{ $t("share_with_social_modal__share_with") }} </ps-label>
                <ps-icon textColor="text-feSecondary-800 dark:text-feSecondary-200" @click="psmodal.toggle(false)" name="cross" w="22" h="22" viewBox="0 0 20 20"/> 
            </div>
        </template>
        <template #body>
            <div class="w-full flex items-center justify-between">
                <ps-link :url="facebookURL">
                    <div class="flex items-center w-20 pl-3.5 pb-1 h-20 gap-4 mb-5 border border-feSecondary-100 dark:border-feSecondary-400 rounded-full">
                        <ps-icon textColor="text-feBrand-facebook dark:text-feBrand-facebook" name="share-facebook-social" w="46" h="44" viewBox="0 0 44 44"/>
                    </div>
                </ps-link>

                <ps-link :url="twitterURL">
                    <div class="flex items-center w-20 pl-4 h-20 gap-4 mb-5 border border-feSecondary-100 dark:border-feSecondary-400 rounded-full">
                        <ps-icon textColor="text-feBrand-twitter dark:text-feBrand-twitter" name="share-twitter" w="44" h="44" viewBox="0 0 44 44"/>
                    </div>
                </ps-link>


                <ps-link :url="linkedinURL">
                    <div class="flex items-center w-20 pl-4 h-20 gap-4 mb-5 border border-feSecondary-100 dark:border-feSecondary-400 rounded-full">
                        <ps-icon textColor="text-feBrand-linkedin dark:text-feBrand-linkedin" name="share-linkedin" w="42" h="44" viewBox="0 0 44 44"/>
                    </div>
                </ps-link>
            </div>
            <ps-label textColor="font-medium text-base text-feSecondary-800 dark:text-feSecondary-200">  {{ $t("share_with_social_modal__copy_link") }} </ps-label>
    
        </template>
        <template #footer>
            <div class="flex justify-end">
                <div class="flex items-center justify-start shadow-sm w-full h-11 relative border border-feSecondary-100 dark:border-feSecondary-400 rounded-lg p-1">
                    <ps-icon textColor="text-feSecondary-500 dark:text-feSecondary-200 pb-1" name="copy-link" w="26" h="22" viewBox="0 0 20 20"/> 
                    <ps-label textColor="font-medium text-base text-feSecondary-400 dark:text-feSecondary-200 pl-2 pb-1"> {{ url }} </ps-label>
                </div>
                <ps-button @click="copy" colors="font-medium text-base h-11 rounded-lg bg-fePrimary-500 text-feSecondary-50 dark:bg-fePrimary-500 dark:text-feSecondary-200 ml-2" 
                    hover="dark:hover:bg-feSecondary-700"> {{ "Copy" }} </ps-button>

            </div>
        </template>
    </ps-modal>
    <ps-success-dialog ref="success" />
</template>

<script lang='ts'>
import { defineComponent, ref } from 'vue';
import PsModal from '@template1/vendor/components/core/modals/PsModal.vue';
import PsLabelTitle from '@template1/vendor/components/core/label/PsLabelTitle.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLink from '@template1/vendor/components/core/link/PsLink.vue'
import PsSuccessDialog from '@template1/vendor/components/core/dialog/PsSuccessDialog.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import { trans } from 'laravel-vue-i18n'

export default defineComponent({
    name : "ShareToSocialModal",
    components: {
        PsModal,
        PsIcon,
        PsLabelTitle,
        PsLabel,
        PsButton,
        PsLink,
        PsSuccessDialog
    },
    setup() {
        const psmodal = ref();
        const url = ref();
        const facebookURL = ref();
        const twitterURL = ref();
        const linkedinURL = ref();
        const success = ref();

        function openModal(link, id, title){

            url.value = link;
            facebookURL.value=  'http://www.facebook.com/sharer/sharer.php?u='+ link +'&title='+title;
            twitterURL.value =  'https://twitter.com/intent/tweet?text=' + title + '&url=' + link;
            linkedinURL.value = 'http://www.linkedin.com/shareArticle?mini=true&url=' + link + '&title=' + title;
            psmodal.value.toggle(true);
        }
        async function copy() {
            await navigator.clipboard.writeText(url.value);
            psmodal.value.toggle(false);
            success.value.openModal(
                trans('item_detail__copy_success'),
                trans('item_detail__link_copied_to_clipboard')
            );
        }

        return {
            psmodal,
            openModal,
            facebookURL,
            twitterURL,
            linkedinURL,
            copy,
            url,
            success
         }
    },
})
</script>
