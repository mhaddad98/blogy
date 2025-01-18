<script setup lang="ts">
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

const page = usePage();
let e = ref(page.props?.errors);

watch(
    () => page.props,
    (newProps) => {
        e.value = newProps.errors;

        for (const key in e.value) {
            form.errors[key] = e.value[key];
        }
    }
);

const props = defineProps({
    categories: Object,
    post: Object,
});

let form = useForm({
    title: props.post?.title,
    content: props.post?.content,
    category: props.post?.categories[0]?.id,
    image: props.post?.image,
    draft: props.post?.draft,
});

const original = {
    title: props.post?.title,
    content: props.post?.content,
    category: props.post?.categories[0]?.id,
    image: props.post?.image,
    draft: props.post?.draft,
};

const preview = ref<string | null>(null);

function editDraft() {
    const changes = {};

    for (const key in original) {
        if (form[key] != original[key]) {
            changes[key] = form[key];
        }
    }
    console.log(changes);

    return router.post(`/post/${props.post?.id}/edit/draft`, {
        ...changes,
    });
}

function submit() {
    return form.post(`/post/${props.post?.id}/edit`);
}

function handleFileChange(event: Event) {
    const fileInput = event.target as HTMLInputElement;
    if (fileInput.files && fileInput.files.length > 0) {
        const file = fileInput.files[0];
        form.image = file;
        preview.value = URL.createObjectURL(file);
    }
}
</script>

<template>
    <div class="flex flex-col items-center justify-center">
        <div
            class="p-5 border min-w-2xl max-w-2xl w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg mt-5"
        >
            <h2 class="text-4xl font-extrabold dark:text-white">Edit Post</h2>

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
                        <div class="mb-5 w-1/2 max-h-[256px] border rounded-md">
                            <img
                                :src="
                                    preview
                                        ? preview
                                        : form.image
                                        ? `/storage/${form.image}`
                                        : '/storage/images/placeholder-image.jpg'
                                "
                                alt="Image Preview"
                                class="object-cover w-full h-full rounded-md"
                            />
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button
                            v-if="post?.draft"
                            type="button"
                            @click="submit"
                            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-blue-800"
                        >
                            Publish
                        </button>
                        <button
                            v-else
                            type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        >
                            Edit
                        </button>
                        <button
                            v-if="post?.draft"
                            type="button"
                            @click="editDraft"
                            class="text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-blue-800"
                        >
                            Edit Draft
                        </button>
                        <Link
                            :href="`/post/${post?.id}`"
                            class="text-blue-700 border border-blue-700 hover:border-blue-900 hover:bg-blue-100 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
