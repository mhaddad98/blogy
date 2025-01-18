<script setup lang="ts">
import SubTitle from "../Shared/SubTitle.vue";
import PostCard from "./Post/PostCard.vue";
import Pagination from "./Post/Pagination.vue";
import { onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import { Link } from "@inertiajs/vue3";

const page = usePage();
defineProps({
    posts: Object,
    category: Object,
    postsCount: Number,
});
function orderBy(orderBy) {
    router.get(page.url, { orderBy });
}
onMounted(() => {
    initFlowbite();
});
</script>

<template>
    <div class="max-w-7xl mx-auto p-4">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol
                class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse"
            >
                <li class="inline-flex items-center">
                    <Link
                        href="/"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white"
                    >
                        <svg
                            class="w-3 h-3 me-2.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"
                            />
                        </svg>
                        Home
                    </Link>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg
                            class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 6 10"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m1 9 4-4-4-4"
                            />
                        </svg>
                        <Link
                            href="/categories"
                            class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white"
                            >Categories</Link
                        >
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg
                            class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 6 10"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m1 9 4-4-4-4"
                            />
                        </svg>
                        <span
                            class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                            >{{ category?.name }}</span
                        >
                    </div>
                </li>
            </ol>
        </nav>

        <SubTitle>{{ category?.name }}</SubTitle>
        <p>{{ category?.description }}</p>
        <div v-if="postsCount || 0 > 0">
            <div class="flex justify-between mt-8">
                <SubTitle>Posts</SubTitle>
                <div class="flex items-center gap-x-2">
                    <div class="flex-col">
                        <p>Posts Count</p>
                        <p>
                            <strong>{{ postsCount }}</strong>
                        </p>
                    </div>

                    <div class="flex">
                        <button
                            id="dropdownDefaultButton"
                            data-dropdown-toggle="dropdown"
                            class="text-black border min-w-44 justify-between border-blue-200 hover:border-blue-800 focus:ring-1 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            type="button"
                        >
                            orderBy
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
                                @click="orderBy('created_at')"
                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                            >
                                Date
                            </li>
                            <li
                                @click="orderBy('views')"
                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                            >
                                Popularity
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="flex gap-4 flex-wrap justify-center mt-4">
                <PostCard v-for="post in posts?.data" :post="post" />
            </div>
            <Pagination :links="posts?.links" />
        </div>

        <div v-else>
            <SubTitle>No Post Yet on this category</SubTitle>
        </div>
    </div>
</template>
