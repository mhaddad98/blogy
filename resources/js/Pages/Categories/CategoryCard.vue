<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import { onMounted } from "vue";

export type Category = {
    id: number;
    name: String;
    description: String;
};
defineProps<{ category: Category }>();

onMounted(() => {
    initFlowbite();
});
</script>

<template>
    <div
        class="flex flex-col p-6 w-full md:w-[300px] bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 hover:border-blue-600 transition-colors duration-300"
    >
        <h5
            class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
        >
            {{ category.name }}
        </h5>

        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
            {{ category.description }}
        </p>

        <Link
            :href="`/categories/${category.id}`"
            class="mt-auto inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
        >
            Open Category
            <svg
                class="rtl:rotate-180 w-3.5 h-3.5 ms-2"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 14 10"
            >
                <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M1 5h12m0 0L9 1m4 4L9 9"
                />
            </svg>
        </Link>
    </div>
</template>
