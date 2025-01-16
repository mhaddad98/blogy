<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { PropType } from "vue";

export type Tab = {
    id: number;
    href: string;
    active: boolean;
    label: string;
};

defineProps({
    tabs: {
        type: Array as PropType<Tab[]>,
        required: true,
    },
});
</script>

<template>
    <li class="me-2" v-for="tab in tabs" :key="tab.id">
        <Link
            :href="tab.href"
            aria-current="page"
            :class="
                tab.active
                    ? 'inline-block p-4 text-blue-600 bg-gray-100 rounded-t-lg active dark:bg-gray-800 dark:text-blue-500'
                    : 'inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300'
            "
            >{{ tab.label }}</Link
        >
    </li>
</template>
