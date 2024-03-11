<template>
    <ps-button hover="" focus=""
        colors="bg-feAchromatic-50 dark:bg-feAchromatic-900 text-feSecondary-800 dark:text-feSecondary-200 "
        class="me-2 sm:hidden" padding="px-4 py-2" border="border border-1 border-feSecondary-200" @click="toggleShowFilter"
        :disabled="false">
        <ps-icon textColor="text-feSecondary-500 dark:text-feSecondary-200" name="filter" class='cursor-pointer me-1' />
        {{ $t("filter") }}
    </ps-button>
</template>

<script>
// Components
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
// Providers
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";

export default {
    name: 'ItemFilterMobileFilterButton',
    components: {
        PsButton,
        PsIcon
    },
    setup() {
        const itemProvider = useProductStore('list');

        function toggleShowFilter() {
            itemProvider.showPopUpFilter = !itemProvider.showPopUpFilter;
        }

        return {
            toggleShowFilter
        }
    }

}

</script>
