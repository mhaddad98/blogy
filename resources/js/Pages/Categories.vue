<script setup lang="ts">
import PageTitle from "../Shared/PageTitle.vue";
import CategoryCard from "./Categories/CategoryCard.vue";
defineProps({
    categories: Object,
});
</script>

<template>
    <div class="max-w-7xl mx-auto p-4">
        <PageTitle>Categories</PageTitle>
        <div class="flex gap-4 flex-wrap justify-center mt-10">
            <CategoryCard
                v-for="category in categories"
                :key="category.id"
                :category="category"
            />
        </div>
    </div>
</template>
