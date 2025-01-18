<script setup lang="ts">
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import Tabs, { Tab } from "../../Shared/Tabs.vue";
import DangerAlert from "../../Shared/DangerAlert.vue";
import Table from "../../Shared/Table.vue";
import Input from "../../Shared/Forms/Input.vue";
import SuccessAlert from "../../Shared/SuccessAlert.vue";
import Index from "./Index.vue";
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";

const page = usePage();
const url = page.url;

const deleted = url.includes("deleted");
const active = !deleted;

const props = defineProps({
    users: Object,
});

let form = useForm({
    user: "",
    name: "",
    email: "",
    is_admin: false,
    userIndex: 0,
});

function fillForm(user) {
    form.user = user.id;
    form.name = user.name;
    form.email = user.email;
    form.is_admin = Boolean(user.admin);
    form.userIndex = props?.users?.findIndex((u) => u.id === user.id);
}

function submit() {
    const userIndex = form.userIndex;

    const original = {
        name: props?.users?.[userIndex]?.name,
        email: props?.users?.[userIndex]?.email,
        is_admin: props?.users?.[userIndex]?.admin,
    };

    const changes = {};

    for (const key in original) {
        if (form[key] != original[key]) {
            changes[key] = form[key];
        }
    }

    router.patch(`/admin/user/${form.user}/edit`, {
        ...changes,
    });
}

const tabs: Tab[] = [
    {
        id: 0,
        href: "?active=true",
        label: "Active Users",
        active: active,
    },
    {
        id: 1,
        href: "?deleted=true",
        label: "Deleted Users",
        active: deleted,
    },
];
onMounted(() => {
    initFlowbite();
});

const tableHead = ["Name", "Email", "Verified", "Admin", "Edit", "Delete"];
</script>

<template>
    <Index>
        <div class="max-w-7xl mx-auto mt-4 px-4">
            <ul
                class="mt-4 flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400"
            >
                <Tabs :tabs />
            </ul>

            <DangerAlert
                v-if="$page.props.errors['main']"
                :errorMessage="$page.props.errors['main']"
            />
            <SuccessAlert
                v-if="$page.props.flash.message"
                :errorMessage="$page.props.flash.message"
            />

            <div v-if="users?.length > 0" class="max-w-7xl mx-auto my-5">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <Table
                        @update-form="(user) => fillForm(user)"
                        tableHeading="Active Users"
                        tableSubHeading="The UsersList"
                        :tableHead
                        :tableRows="
                            users?.map((user) => {
                                return {
                                    Name: {
                                        body: user.name,
                                        classCondition: true,
                                        classTrue: 'font-bold',
                                        classFalse: '',
                                    },
                                    Email: {
                                        body: user.email,
                                        classCondition: null,
                                        classTrue: '',
                                        classFalse: '',
                                    },
                                    Verified: {
                                        body: user.verified ? 'Yes' : 'No',
                                        classCondition: user.verified,
                                        classTrue: 'text-blue-500 font-bold',
                                        classFalse: '',
                                    },

                                    Admin: {
                                        body: user.admin ? 'Yes' : 'No',
                                        classCondition: user.admin,
                                        classTrue: 'text-blue-500 font-bold',
                                        classFalse: '',
                                    },
                                    Edit: {
                                        button: !user.deleted
                                            ? {
                                                  id: 'defaultModalButton',
                                                  dataModalTarget:
                                                      'defaultModal',
                                                  dataModalToggle:
                                                      'defaultModal',
                                                  text: 'Edit',
                                                  value: user,
                                              }
                                            : null,
                                        body: !user.deleted
                                            ? null
                                            : `Restore To Edit`,
                                    },
                                    Delete: {
                                        href: `user/${user.id}`,
                                        method: !user.deleted
                                            ? 'delete'
                                            : 'patch',
                                        body: !user.deleted
                                            ? `Delete
                                        `
                                            : `Restore
                                        `,
                                        classCondition: !user.deleted,
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

            <div
                id="defaultModal"
                tabindex="-1"
                aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full"
            >
                <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
                    <div
                        class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5"
                    >
                        <div
                            class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600"
                        >
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Edit User
                            </h3>
                            <button
                                type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-toggle="defaultModal"
                            >
                                <svg
                                    aria-hidden="true"
                                    class="w-5 h-5"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <DangerAlert
                            v-if="$page.props.errors['userUpdate']"
                            :errorMessage="$page.props.errors['userUpdate']"
                        />
                        <SuccessAlert
                            v-if="$page.props.flash.message"
                            :errorMessage="$page.props.flash.message"
                        />

                        <!-- Modal body -->
                        <form @submit.prevent="submit">
                            <div class="grid gap-4 mb-4 sm:grid-cols-2">
                                <div>
                                    <Input
                                        type="text"
                                        name="name"
                                        label="Name"
                                        placeholder="example name"
                                        v-model="form.name"
                                        :error="
                                            form.errors.name ||
                                            $page.props.errors.name
                                        "
                                    />
                                </div>
                                <div>
                                    <Input
                                        type="email"
                                        name="email"
                                        label="Email"
                                        placeholder="example@email.com"
                                        v-model="form.email"
                                        :error="
                                            form.errors.email ||
                                            $page.props.errors.email
                                        "
                                    />
                                </div>
                                <div>
                                    <label
                                        class="inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            name="admin"
                                            type="checkbox"
                                            class="sr-only peer"
                                            v-model="form.is_admin"
                                        />
                                        <div
                                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"
                                        ></div>
                                        <span
                                            class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300"
                                            >Admin</span
                                        >
                                    </label>
                                </div>
                            </div>
                            <button
                                type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex items-center"
                            >
                                <svg
                                    class="mr-1 -ml-1 w-6 h-6"
                                    fill="none"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.2"
                                        d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"
                                    />
                                </svg>
                                Edit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </Index>
</template>
