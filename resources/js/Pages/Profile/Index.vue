<script setup lang="ts">
import { Link, usePage } from "@inertiajs/vue3";
import PostCard from "../Post/PostCard.vue";
import Pagination from "../Post/Pagination.vue";
import { onMounted } from "vue";
import SuccessAlert from "../../Shared/SuccessAlert.vue";
import Tabs, { Tab } from "../../Shared/Tabs.vue";

const page = usePage();
const draft = page.url.includes("tab=draft");
const active = !draft;

defineProps({
    user: Object,
    posts: Object,
    activePosts: Number || String,
    draftPosts: Number || String,
    totalViews: Number || String,
});

const tabs: Tab[] = [
    {
        id: 0,
        href: "?tab=active",
        label: "Active Posts",
        active: active,
    },
    {
        id: 1,
        href: "?tab=draft",
        label: "Draft Posts",
        active: draft,
    },
];
</script>

<template>
    <SuccessAlert
        class="my-4"
        v-if="$page.props.flash.message"
        :errorMessage="$page.props.flash.message"
    />
    <div class="mb-3 max-w-7xl mx-auto p-4 flex gap-4">
        <div class="rounded-full w-40 h-40 overflow-hidden border-2">
            <img
                v-if="user?.image"
                :src="`/storage/${user?.image}`"
                class="object-cover w-full h-full"
            />
            <img
                v-else
                src="/storage/images/placeholder-image.jpg"
                class="object-cover w-full h-full"
            />
        </div>
        <div class="px-2 py-6 flex-col my-auto space-y-2">
            <div class="font-bold text-2xl">{{ user?.name }}</div>
            <div class="flex justify-between items-center">
                <div class="font-normal text-md">{{ user?.email }}</div>
                <div
                    v-if="user?.verified"
                    class="font-bold text-xs ml-1 text-blue-600"
                >
                    Verified
                </div>
                <Link
                    v-else
                    href="/profile/verify"
                    class="font-bold text-xs text-green-400 ml-1 hover:underline"
                >
                    Verify Now
                </Link>
            </div>
            <div class="text-sm">
                Joined: <strong>{{ user?.joined }}</strong>
            </div>
        </div>
    </div>
    <div class="bg-slate-200 h-[1px] max-w-7xl mx-auto"></div>

    <div class="mb-3 max-w-4xl mx-auto p-4 flex gap-4 justify-between">
        <div
            class="w-full h-52 flex-col content-center justify-items-center space-y-4 group hover:shadow-lg rounded-md"
        >
            <div class="font-bold">Active posts</div>
            <div class="font-bold text-lg text-blue-600">{{ activePosts }}</div>
            <svg
                class="w-[62px] h-[62px] text-gray-800 dark:text-white group-hover:text-blue-500 transition-colors duration-200"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                fill="none"
                viewBox="0 0 24 24"
            >
                <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.2"
                    d="m10.827 5.465-.435-2.324m.435 2.324a5.338 5.338 0 0 1 6.033 4.333l.331 1.769c.44 2.345 2.383 2.588 2.6 3.761.11.586.22 1.171-.31 1.271l-12.7 2.377c-.529.099-.639-.488-.749-1.074C5.813 16.73 7.538 15.8 7.1 13.455c-.219-1.169.218 1.162-.33-1.769a5.338 5.338 0 0 1 4.058-6.221Zm-7.046 4.41c.143-1.877.822-3.461 2.086-4.856m2.646 13.633a3.472 3.472 0 0 0 6.728-.777l.09-.5-6.818 1.277Z"
                />
            </svg>
        </div>
        <div
            class="w-full h-52 flex-col content-center justify-items-center space-y-4 group hover:shadow-lg rounded-md"
        >
            <div class="font-bold">Draft posts</div>
            <div class="font-bold text-lg text-blue-600">{{ draftPosts }}</div>
            <svg
                class="w-[62px] h-[62px] text-gray-800 dark:text-white group-hover:text-blue-500 transition-colors duration-200"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                fill="none"
                viewBox="0 0 24 24"
            >
                <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.2"
                    d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"
                />
            </svg>
        </div>
        <div
            class="w-full h-52 flex-col content-center justify-items-center space-y-4 group hover:shadow-lg rounded-md"
        >
            <div class="font-bold">Total posts views</div>
            <div class="font-bold text-lg text-blue-600">{{ totalViews }}</div>
            <svg
                class="w-[62px] h-[62px] text-gray-800 dark:text-white group-hover:text-blue-500 transition-colors duration-200"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                fill="none"
                viewBox="0 0 24 24"
            >
                <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.2"
                    d="M8 20V10m0 10-3-3m3 3 3-3m5-13v10m0-10 3 3m-3-3-3 3"
                />
            </svg>
        </div>
    </div>

    <div class="bg-slate-200 h-[1px] max-w-7xl mx-auto"></div>

    <div class="max-w-7xl mx-auto mt-4 px-4">
        <div class="font-bold text-xl">Posts</div>
        <ul
            class="mt-4 flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400"
        >
            <Tabs :tabs />
        </ul>
    </div>
    <div v-if="posts?.data.length > 0" class="max-w-7xl mx-auto">
        <div class="flex gap-4 flex-wrap justify-center mt-4">
            <PostCard v-for="post in posts?.data" :post="post" />
        </div>
        <Pagination :links="posts?.links" />
    </div>
    <div v-else class="max-w-7xl mx-auto mt-4 p-4">
        <p>Nothing To show...</p>
        <Link
            href="/post/create"
            class="font-bold text-blue-500 hover:underline"
            >Create new post</Link
        >
    </div>
</template>
