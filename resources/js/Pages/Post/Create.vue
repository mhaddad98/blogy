<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

let form = useForm({
    title: "",
    content: "",
    category: "",
    image: null as File | null,
});
const preview = ref<string | null>(null);

function submit() {
    form.post("/post");
}
function submitDraft() {
    form.post("/post/draft");
}
function handleFileChange(event: Event) {
    const fileInput = event.target as HTMLInputElement;
    if (fileInput.files && fileInput.files.length > 0) {
        const file = fileInput.files[0];
        form.image = file;
        preview.value = URL.createObjectURL(file);
    }
}
defineProps({
    categories: Object,
});
</script>

<template>
    <div class="flex flex-col items-center justify-center">
        <div
            class="p-5 border min-w-2xl max-w-2xl w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg mt-5"
        >
            <h2 class="text-4xl font-extrabold dark:text-white">New Post</h2>
            <p
                v-if="form.errors['main']"
                class="mt-2 text-sm text-red-600 dark:text-red-500"
            >
                {{ form.errors["main"] }}
            </p>

            <div class="p-4">
                <form
                    class="mx-auto"
                    @submit.prevent="submit"
                    enctype="multipart/form-data"
                >
                    <div class="mb-5">
                        <label
                            for="title"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >Title</label
                        >
                        <input
                            type="text"
                            id="title"
                            v-model="form.title"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Post Tile"
                        />
                        <p
                            v-if="form.errors.title"
                            class="mt-2 text-sm text-red-600 dark:text-red-500"
                        >
                            {{ form.errors.title }}
                        </p>
                    </div>
                    <div class="mb-5">
                        <label
                            for="content"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >Body</label
                        >
                        <textarea
                            v-model="form.content"
                            id="content"
                            rows="4"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Your Post Content Body Goes Here..."
                        ></textarea>

                        <p
                            v-if="form.errors.content"
                            class="mt-2 text-sm text-red-600 dark:text-red-500"
                        >
                            {{ form.errors.content }}
                        </p>
                    </div>
                    <div class="mb-5">
                        <label
                            for="category"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >Select a category</label
                        >
                        <select
                            id="category"
                            v-model="form.category"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option selected value="null" disabled>
                                Choose a category
                            </option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.category"
                            class="mt-2 text-sm text-red-600 dark:text-red-500"
                        >
                            {{ form.errors.category }}
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <div class="mb-5 w-1/2">
                            <label
                                for="postImage"
                                class="flex flex-col items-center justify-center w-full h-64 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                            >
                                <div
                                    class="flex flex-col items-center justify-center p-4"
                                >
                                    <svg
                                        class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 20 16"
                                    >
                                        <path
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                                        />
                                    </svg>
                                    <p
                                        class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        <span class="font-semibold"
                                            >Click to upload</span
                                        >
                                        or drag and drop
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        PNG Or JPG (MAX. 2 MB)
                                    </p>
                                </div>
                                <input
                                    id="postImage"
                                    type="file"
                                    class="hidden"
                                    @change="handleFileChange"
                                />
                            </label>
                            <p
                                v-if="form.errors.image"
                                class="mt-2 text-sm text-red-600 dark:text-red-500"
                            >
                                {{ form.errors.image }}
                            </p>
                        </div>
                        <div class="mb-5 w-1/2 max-h-[256px]">
                            <img
                                :src="
                                    preview ??
                                    '/storage/images/placeholder-image.jpg'
                                "
                                alt="Image Preview"
                                class="object-cover w-full h-full rounded-md"
                            />
                        </div>
                    </div>
                    <div class="flex gap-x-3 justify-end">
                        <button
                            type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        >
                            Create
                        </button>
                        <button
                            type="button"
                            @click="submitDraft"
                            class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                        >
                            Draft
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
