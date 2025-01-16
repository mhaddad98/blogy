<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import { onMounted } from "vue";
export type Post = {
    id: number;
    title: String;
    excerpt: String;
    authorName: String;
    authorId: String;
    image: String;
    categories: { id: Number; name: String }[];
    date: String;
};
const props = defineProps<{ post: Post }>();

onMounted(() => {
    initFlowbite();
});
</script>

<template>
    <div
        class="flex flex-col p-6 w-full md:w-[300px] bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 hover:border-blue-600 transition-colors duration-300"
    >
        <div class="mb-3">
            <div class="rounded-md w-full h-64 bg-slate-300 overflow-hidden">
                <img
                    v-if="post?.image"
                    :src="`/storage/${post?.image}`"
                    class="object-cover w-full h-full"
                />
                <img
                    v-else
                    src="/storage/images/placeholder-image.jpg"
                    class="object-cover w-full h-full"
                />
            </div>
        </div>
        <div v-if="post.draft" class="text-xs mb-2">
            <div class="h-1 mb-1 bg-blue-700"></div>
            Draft
        </div>
        <h5
            class="mb-2 text-md font-bold tracking-tight text-gray-900 dark:text-white"
        >
            {{ post.title }}
        </h5>
        <div class="flex justify-between">
            <Link
                :href="`/post/user/${post.authorId}`"
                class="mb-3 text-sm font-bold text-gray-900 dark:text-gray-200 hover:text-blue-500"
            >
                {{ post.authorName }}
            </Link>
            <p class="mb-3 text-sm font-light text-gray-700 dark:text-gray-400">
                {{ post.date }}
            </p>
        </div>
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
            {{ post.excerpt }}
        </p>

        <div class="flex gap-2 mb-2">
            <span v-for="category in post.categories">
                <Link
                    :href="`/categories/${category.id}`"
                    class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300"
                >
                    {{ category.name }}
                </Link>
            </span>
        </div>
        <Link
            :href="`/post/${post.id}`"
            class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 mt-auto"
        >
            Open Post
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
