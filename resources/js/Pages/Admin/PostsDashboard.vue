<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import Tabs, { Tab } from "../../Shared/Tabs.vue";
import DangerAlert from "../../Shared/DangerAlert.vue";
import Table from "../../Shared/Table.vue";
import SuccessAlert from "../../Shared/SuccessAlert.vue";
import Index from "./Index.vue";
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";

const page = usePage();
const url = page.url;

const deleted = url.includes("deleted");
const active = !deleted;

defineProps({
    posts: Object,
});

onMounted(() => {
    initFlowbite();
});

const tabs: Tab[] = [
    {
        id: 0,
        href: "?active=true",
        label: "Active Posts",
        active: active,
    },
    {
        id: 1,
        href: "?deleted=true",
        label: "Deleted Posts",
        active: deleted,
    },
];

const tableHead = [
    "Title",
    "Content",
    "Author",
    "Draft",
    "Views",
    "Edit",
    "Delete",
];
</script>

<template>
    <Index>
        <div class="max-w-7xl mx-auto mt-4 px-4">
            <ul
                class="mt-4 flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400"
            >
                <Tabs :tabs />
            </ul>

            <slot />

            <DangerAlert
                v-if="$page.props.errors['main']"
                :errorMessage="$page.props.errors['main']"
            />
            <SuccessAlert
                v-if="$page.props.flash.message"
                :errorMessage="$page.props.flash.message"
            />
            <div v-if="posts?.length > 0" class="max-w-7xl mx-auto my-5">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <Table
                        tableHeading="Active Posts"
                        tableSubHeading="The Posts List"
                        :tableHead
                        :tableRows="
                            posts?.map((post) => {
                                return {
                                    Title: {
                                        body: post.title,
                                        classCondition: true,
                                        classTrue: 'font-bold',
                                        classFalse: '',
                                    },
                                    Content: {
                                        body: post.excerpt,
                                        classCondition: null,
                                        classTrue: '',
                                        classFalse: '',
                                    },
                                    Author: {
                                        body: post.authorName,
                                        classCondition: null,
                                        classTrue: '',
                                        classFalse: '',
                                    },
                                    Draft: {
                                        body: post.draft ? 'Yes' : 'No',
                                        classCondition: post.draft,
                                        classTrue: 'text-blue-500 font-bold',
                                        classFalse: '',
                                    },
                                    Views: {
                                        body: post.views,
                                        classCondition: post.views > 0,
                                        classTrue: 'text-blue-500 font-bold',
                                        classFalse: '',
                                    },
                                    Edit: {
                                        href: !post.deleted
                                            ? `/post/${post.id}/edit`
                                            : null,
                                        body: !post.deleted
                                            ? 'Edit'
                                            : 'Restore To Edit',
                                        classCondition: null,
                                        classTrue: '',
                                        classFalse: '',
                                    },
                                    Delete: {
                                        href: `posts/${post.id}`,
                                        method: !post.deleted
                                            ? 'delete'
                                            : 'patch',
                                        body: !post.deleted
                                            ? `Delete
                                        `
                                            : `Restore
                                        `,
                                        classCondition: !post.deleted,
                                        classTrue:
                                            'font-medium text-red-600 dark:text-red-500 hover:underline',
                                        classFalse:
                                            'font-medium text-green-600 dark:text-green-500 hover:underline',
                                    },
                                };
                            })
                        "
                    />
                </div>
            </div>
            <div v-else class="max-w-7xl mx-auto mt-4 p-4">
                <p>Nothing To show...</p>
            </div>
        </div>
    </Index>
</template>
