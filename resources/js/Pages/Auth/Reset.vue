<script setup lang="ts">
import { useForm, usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import Input from "../../Shared/Forms/Input.vue";
import SuccessAlert from "../../Shared/SuccessAlert.vue";
const page = usePage();
let form = useForm({
    otp: "",
    email: "",
});

let passwordForm = useForm({
    password: "",
    password_confirmation: "",
    passToken: "",
    email: "",
});

function submit() {
    form.post("/reset");
}

function submitPassword() {
    passwordForm.passToken = page.props.flash.passToken;
    passwordForm.email = form.email;
    passwordForm.post("/reset/new");
}

const props = defineProps();
</script>

<script lang="ts">
export default {
    layout: null,
};
</script>

<template>
    <div class="flex flex-col items-center justify-center">
        <SuccessAlert
            class="my-4"
            v-if="$page.props.flash.message"
            :errorMessage="$page.props.flash.message"
        />
        <div
            class="p-5 border min-w-2xl max-w-2xl w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg mt-5"
        >
            <h2 class="text-4xl font-extrabold dark:text-white">
                Reset password
            </h2>
            <p
                v-if="$page.props.errors['main']"
                class="mt-2 text-sm text-red-600 dark:text-red-500"
            >
                {{ $page.props.errors["main"] }}
            </p>

            <div class="p-4">
                <form class="mx-auto" @submit.prevent="submit">
                    <div class="flex gap-3 items-start">
                        <div class="mb-5 w-full">
                            <Input
                                type="email"
                                name="email"
                                label="Email"
                                placeholder="your@email.com"
                                v-model="form.email"
                                :error="
                                    form.errors.email ||
                                    $page.props.errors['email']
                                "
                            />
                        </div>
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
                            :href="`/reset/send/${form.email}`"
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

            <div class="p-4" v-if="$page.props.flash.passToken">
                <form
                    class="mx-auto"
                    @submit.prevent="submitPassword"
                    id="passwordForm"
                >
                    <div class="flex gap-3 items-start">
                        <input
                            type="hidden"
                            v-model="passwordForm.password"
                            name="passToken"
                        />
                        <input
                            type="hidden"
                            v-model="passwordForm.email"
                            name="email"
                        />
                        <div class="mb-5 w-full">
                            <Input
                                type="password"
                                name="password"
                                label="Password"
                                placeholder="********"
                                v-model="passwordForm.password"
                                :error="passwordForm.errors.password"
                            />
                        </div>
                        <div class="mb-5 w-full">
                            <Input
                                type="password"
                                name="password_confirmation"
                                label="Retype Password"
                                placeholder="********"
                                v-model="passwordForm.password_confirmation"
                                :error="
                                    passwordForm.errors.password_confirmation
                                "
                            />
                        </div>
                    </div>

                    <button
                        form="passwordForm"
                        type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    >
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
