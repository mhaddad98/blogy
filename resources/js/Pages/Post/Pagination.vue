<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
defineProps({
    links: Object,
});
</script>

<template>
    <nav class="flex justify-end m-4">
        <ul class="flex items-center -space-x-px h-8">
            <li v-for="link in links">
                <Link
                    :href="link.url ?? '#'"
                    v-html="link.label"
                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight bg-white border border-gray-300 rounded-sm hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                    :class="
                        link.active
                            ? 'text-blue-700 font-bold text-md'
                            : 'text-gray-600 text-sm'
                    "
                />
            </li>
        </ul>
    </nav>
</template>
