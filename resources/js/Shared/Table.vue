<script setup lang="ts">
import { PropType } from "vue";
import { Link } from "@inertiajs/vue3";

export type TableRow = {
    body: string;
    classCondition: boolean;
    classTrue: string;
    classFalse: string;
};

defineProps({
    tableHeading: {
        type: String,
        required: true,
    },
    tableSubHeading: {
        type: String,
        required: true,
    },
    tableHead: {
        type: Array as () => string[],
        required: true,
    },
    tableRows: {
        type: Array as PropType<TableRow[]>,
        required: true,
    },
});
</script>

<template>
    <table
        class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
    >
        <caption
            class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white dark:text-white dark:bg-gray-800"
        >
            {{
                tableHeading
            }}
            <p
                class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400"
            >
                {{ tableSubHeading }}
            </p>
        </caption>
        <thead
            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
        >
            <tr>
                <th scope="col" class="px-6 py-3" v-for="head in tableHead">
                    <div v-html="head"></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr
                v-for="row in tableRows"
                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-slate-100 transition-colors duration-300"
            >
                <td
                    v-for="head in tableHead"
                    :key="head"
                    class="px-6 py-4"
                    :class="
                        row[head]?.classCondition
                            ? row[head]?.classTrue
                            : row[head]?.classFalse
                    "
                >
                    <Link
                        v-if="row[head]?.href"
                        :method="row[head]?.method"
                        :href="row[head]?.href"
                    >
                        <span v-html="row[head]?.body"></span>
                    </Link>
                    <button
                        v-else-if="row[head]?.button"
                        @click="$emit('updateForm', row[head]?.button.value)"
                        :id="row[head]?.button.id"
                        :data-modal-target="row[head]?.button.dataModalTarget"
                        :data-modal-toggle="row[head]?.button.dataModalToggle"
                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                        type="button"
                    >
                        {{ row[head]?.button.text }}
                    </button>
                    <span
                        v-else
                        v-html="row[head]?.body"
                        class="line-clamp-1"
                    ></span>
                </td>
            </tr>
        </tbody>
    </table>
</template>
