<template>
    <nav class="rounded-md w-full ">
        <ol class="list-reset flex">
            <li  class="text-feSecondary-800"><ps-icon v-if="iconName" :name="iconName" class="me-1" :theme="iconColor" /></li>
            <li v-for="(item,index) in items" :key="index" class="text-feSecondary-800 first-line:items-center flex flex-row items-center">

                <ps-label v-if="item.url" :textColor="item.color ? item.color : 'text-feSecondary-800 dark:text-feSecondary-300'" class="flex flex-row">
                    <a :href="item.url" class="text-sm lg:text-sm">{{item.label}}</a>
                    <span v-if="index < (items.length-1)" class="mx-4 text-sm flex items-center justify-center">
                        <!-- <ps-icon w="24" h="24" name="rightChervon" class="hidden lg:flex" /> -->
                        <!-- <ps-icon w="20" h="20" name="rightChervon" class="flex lg:hidden" /> -->
                       /
                    </span>
                </ps-label>
                <ps-label v-else class="flex flex-row">
                    <span :class="item.color ? item.color : 'text-feSecondary-800 dark:text-feSecondary-300'"  class="text-sm   lg:text-base">{{item.label}}</span>
                    <span v-if="index < (items.length-1)" class=" mx-4 text-sm flex items-center justify-center">
                        <!-- <ps-icon name="rightChervon" w="24" h="24" class="hidden lg:flex" /> -->
                        <!-- <ps-icon w="20" h="20" name="rightChervon" class="flex lg:hidden" /> -->
                        /
                    </span>
                </ps-label>

            </li>
        </ol>
        </nav>
</template>
<script>
    import PsLabel from "@template1/vendor/components/core/label/PsLabel.vue";
    import PsIcon from "@template1/vendor/components/core/icons/PsIcon.vue";

    export default {
        name: "PsBreadcrumb2",
        components: {
            PsLabel,
            PsIcon
        },
        props: {
            iconName: {
                type: String,
                default: ""
            },
            iconColor: {
                type: String,
                default: "#4B5563"
            },
            items: {
                type: Array,
                required: true
            }
        }
    }
</script>
