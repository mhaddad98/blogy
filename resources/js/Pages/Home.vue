<script setup lang="ts">
import PageTitle from "../Shared/PageTitle.vue";
import SubTitle from "../Shared/SubTitle.vue";
import PostCard from "./Post/PostCard.vue";
import Pagination from "./Post/Pagination.vue";
import { onMounted, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import debounce from "lodash/debounce";

const props = defineProps({
    posts: Object,
    categories: Object,
    filters: Object,
});

let filter = ref(
    props.filters?.categoryId
        ? {
              label: props.filters?.categoryId["categoryId"],
              id: props.filters?.categoryId["categoryId"],
          }
        : { label: "All", id: "" }
);

let searchPhrase = ref(props.filters?.searchPhrase ?? "");

function filterCategory(category) {
    filter.value = {
        label: category.label ?? category.name,
        id: category.id,
    };
}
watch(
    [filter, searchPhrase],
    debounce(([newFilter, newSearch]) => {
        router.get(
            "/",
            { categoryId: newFilter.id, searchPhrase: newSearch },
            { preserveState: true }
        );
    }, 500)
);

onMounted(() => {
    initFlowbite();
});
</script>

<template>
    <div class="max-w-7xl mx-auto p-4">
        <PageTitle>Home</PageTitle>

        <div class="flex h-12 justify-between">
            <SubTitle>Posts</SubTitle>
            <div class="flex gap-x-3 items-center">
                <div class="relative">
                    <div
                        class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"
                    >
                        <svg
                            class="w-4 h-4 text-gray-500 dark:text-gray-400"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 20 20"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                            />
                        </svg>
                    </div>
                    <input
                        type="search"
                        id="default-search"
                        class="block w-full p-3 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search..."
                        v-model="searchPhrase"
                    />
                </div>

                <div class="flex">
                    <button
                        id="dropdownDefaultButton"
                        data-dropdown-toggle="dropdown"
                        class="text-black border min-w-44 justify-between border-blue-200 hover:border-blue-800 focus:ring-1 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button"
                    >
                        {{ filter.label }}
                        <svg
                            class="w-2.5 h-2.5 ms-3"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m1 1 4 4 4-4"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Dropdown menu -->
                <div
                    id="dropdown"
                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700"
                >
                    <ul
                        class="py-2 text-sm text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownDefaultButton"
                    >
                        <li
                            @click="filterCategory({ label: 'All', id: '' })"
                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                        >
                            All
                        </li>
                        <li
                            v-for="category in categories"
                            :key="category.id"
                            @click="filterCategory(category)"
                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                        >
                            {{ category.name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex gap-4 flex-wrap justify-center mt-4">
            <PostCard v-for="post in posts?.data" :key="post.id" :post="post" />
        </div>
        <Pagination :links="posts?.links" />
    </div>
</template>
