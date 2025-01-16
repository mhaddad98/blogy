<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
let form = useForm({
    current: "",
    password: "",
    password_confirmation: "",
});

function submit() {
    form.post("/changepassword");
}

const props = defineProps();
</script>

<template>
    <div class="flex flex-col items-center justify-center h-screen">
        <div
            class="p-5 border min-w-2xl max-w-2xl w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg mt-5"
        >
            <h2 class="text-4xl font-extrabold dark:text-white">
                Change password
            </h2>
            <p
                v-if="$page.props.errors['main']"
                class="mt-2 text-sm text-red-600 dark:text-red-500"
            >
                {{ $page.props.errors["main"] }}
            </p>

            <div class="p-4">
                <form class="mx-auto" @submit.prevent="submit">
                    <div class="mb-5">
                        <label
                            for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >Current Password</label
                        >
                        <input
                            type="password"
                            id="current"
                            v-model="form.current"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="********"
                        />
                        <p
                            v-if="form.errors.current"
                            class="mt-2 text-sm text-red-600 dark:text-red-500"
                        >
                            {{ form.errors.current }}
                        </p>
                    </div>
                    <div class="mb-5">
                        <label
                            for="password"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >Password</label
                        >
                        <input
                            v-model="form.password"
                            type="password"
                            id="password"
                            placeholder="********"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        />
                        <p
                            v-if="form.errors.password"
                            class="mt-2 text-sm text-red-600 dark:text-red-500"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>
                    <div class="mb-5">
                        <label
                            for="password_confirmation"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >Confirm Password</label
                        >
                        <input
                            type="password"
                            v-model="form.password_confirmation"
                            id="password_confirmation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="********"
                        />
                    </div>

                    <button
                        type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    >
                        Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
