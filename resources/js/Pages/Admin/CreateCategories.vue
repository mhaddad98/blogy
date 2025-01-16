<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import Index from "./Index.vue";
import Input from "../../Shared/Forms/Input.vue";
import SuccessAlert from "../../Shared/SuccessAlert.vue";

let form = useForm({
    name: "",
    description: "",
});

function submit() {
    form.post("/admin/categories");
}
</script>

<template>
    <Index>
        <div class="flex flex-col items-center justify-center">
            <div
                class="p-5 border min-w-2xl max-w-2xl w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg mt-5"
            >
                <h2 class="text-4xl font-extrabold dark:text-white">
                    Add New Category
                </h2>
                <SuccessAlert
                    v-if="$page.props.flash.message"
                    :errorMessage="$page.props.flash.message"
                />

                <div class="p-4">
                    <form class="mx-auto" @submit.prevent="submit">
                        <div class="mb-5">
                            <Input
                                type="text"
                                name="name"
                                label="Name"
                                placeholder="example name"
                                v-model="form.name"
                                :error="form.errors.name"
                            />
                        </div>
                        <div class="mb-5">
                            <Input
                                type="text"
                                name="description"
                                label="Description"
                                placeholder="Description"
                                v-model="form.description"
                                :error="form.errors.description"
                            />
                        </div>

                        <button
                            type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        >
                            Create
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </Index>
</template>
