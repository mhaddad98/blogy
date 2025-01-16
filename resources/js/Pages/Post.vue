<script setup lang="ts">
import SubTitle from "../Shared/SubTitle.vue";

import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import { Link } from "@inertiajs/vue3";
import PostCard from "./Post/PostCard.vue";

defineProps({
    post: Object,
    relatedPosts: Object,
});

onMounted(() => {
    initFlowbite();
});
</script>

<template>
    <div class="max-w-7xl mx-auto p-4">
        <div class="rounded-md w-full h-[600px] overflow-hidden mb-3">
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
        <div v-if="post?.draft" class="text-xs mb-2">
            <div class="h-1 mb-1 bg-blue-700"></div>
            Draft
        </div>
        <div class="flex items-center gap-x-2 justify-between">
            <SubTitle>{{ post?.title }}</SubTitle>
            <div class="flex-col text-sm min-w-fit space-y-2">
                <div>
                    Date:
                    <strong class="text-blue-500">{{ post?.date }}</strong>
                </div>
                <div>
                    Update:
                    <strong class="text-blue-500">{{ post?.updated }}</strong>
                </div>
            </div>
        </div>
        <div class="mt-4 font-bold">
            <Link
                :href="`/post/user/${post?.authorId}`"
                class="text-gray-900 dark:text-gray-200 hover:text-blue-500"
            >
                {{ post?.authorName }}
            </Link>
        </div>
        <div class="mt-6">{{ post?.content }}</div>

        <div class="flex gap-2 items-center">
            <div class="mt-2 font-bold text-blue-400">Categories :</div>
            <div class="mt-3" v-for="category in post?.categories">
                <Link
                    :href="`/categories/${category.id}`"
                    class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300"
                >
                    {{ category?.name }}
                </Link>
            </div>
            <div class="flex gap-x-2 ml-auto">
                <Link
                    v-if="post?.canEdit"
                    :href="`/post/${post.id}/edit`"
                    class="mt-2 font-bold bg-blue-700 hover:bg-blue-950 text-white text-md ml-auto me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300"
                >
                    Edit Post
                </Link>
                <Link
                    v-if="post?.canDelete"
                    :href="`/post/${post.id}`"
                    method="delete"
                    class="mt-2 font-bold bg-red-700 hover:bg-red-950 text-white text-md ml-auto me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300"
                >
                    Delete Post
                </Link>
            </div>
        </div>

        <div class="flex-col items-center mt-6">
            <SubTitle>Related Posts</SubTitle>
            <div
                class="flex gap-4 flex-wrap justify-center mt-4"
                v-if="relatedPosts.length > 0"
            >
                <PostCard v-for="post in relatedPosts" :post="post" />
            </div>
            <p v-else class="mt-4">Nothing To show...</p>
        </div>
    </div>
</template>
