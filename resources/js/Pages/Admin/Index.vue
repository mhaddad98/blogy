<script setup lang="ts">
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import UsersDashboard from "./UsersDashboard.vue";
import CategoriesDashboard from "./CategoriesDashboard.vue";

const page = usePage();
const url = page.url;

const props = defineProps({
    users: Object,
    categories: Object,
});

let categoryForm = useForm({
    category: "",
    name: "",
    description: "",
    categoryIndex: 0,
});

function fillCategoryForm(category) {
    categoryForm.category = category.id;
    categoryForm.name = category.name;
    categoryForm.description = category.description;
    categoryForm.categoryIndex = props?.categories?.findIndex(
        (u) => u.id === category.id
    );
}
let newCategoryForm = useForm({
    name: "",
    description: "",
    categoryIndex: 0,
});

function submitEditCategory() {
    // categoryForm.post(`/category`);
    console.log("Edit Category");
}

function submitNewCategory() {
    newCategoryForm.post(`/category`);
}

let activePage = ref("users");
function changeActivePage(page) {
    activePage.value = page;
}
</script>

<template>
    <div class="max-w-7xl mx-auto mt-4 px-4">
        <div class="font-bold text-xl">Admin Dashboard</div>
        <div class="my-5">
            <Link
                href="/admin/users"
                class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
            >
                User Management
            </Link>
            <Link
                href="/admin/categories"
                class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
            >
                Categories Management
            </Link>
            <Link
                href="/admin/posts"
                class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
            >
                Post Management
            </Link>
        </div>

        <slot />

        <div v-if="activePage === 'categories'">
            <CategoriesDashboard :categories />
        </div>

        <div v-else-if="activePage === 'posts'">posts</div>
    </div>
</template>
