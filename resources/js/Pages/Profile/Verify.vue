<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import Input from "../../Shared/Forms/Input.vue";
let form = useForm({
    otp: "",
});

function submit() {
    form.post("/profile/verify");
}

const props = defineProps();
</script>

<template>
    <div class="flex flex-col items-center justify-center">
        <div
            class="p-5 border min-w-2xl max-w-2xl w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg mt-5"
        >
            <h2 class="text-4xl font-extrabold dark:text-white">
                Verify Your Email Now
            </h2>
            <p
                v-if="$page.props.errors['main']"
                class="mt-2 text-sm text-red-600 dark:text-red-500"
            >
                {{ $page.props.errors["main"] }}
            </p>

            <div class="p-4">
                <form class="mx-auto" @submit.prevent="submit">
                    <div class="flex gap-3 items-center">
                        <div class="mb-5 w-full">
                            <Input
                                type="text"
                                name="otp"
                                label="Otp"
                                placeholder="123456"
                                v-model="form.otp"
                                :error="form.errors.otp"
                            />
                        </div>
                        <Link
                            href="/profile/send"
                            method="post"
                            class="font-bold text-blue-500 p-1 hover:text-blue-700"
                        >
                            Send Code
                        </Link>
                    </div>

                    <button
                        type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    >
                        Verify
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
