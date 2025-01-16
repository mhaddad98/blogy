<script setup lang="ts">
import SubTitle from "../../Shared/SubTitle.vue";
import PostCard from "./../Post/PostCard.vue";
import Pagination from "./../Post/Pagination.vue";

defineProps({
    author: String,
    posts: Object,
});
</script>

<template>
    <div class="flex-col max-w-7xl mx-auto p-4">
        <div>
            <SubTitle class="text-center mb-4">{{ author }} Posts</SubTitle>
        </div>
        <div v-if="posts?.data.length > 0">
            <SubTitle>Posts</SubTitle>
        </div>
        <div v-if="posts?.data.length > 0">
            <div class="flex justify-center">
                <div class="flex gap-4 flex-wrap justify-center mt-4">
                    <PostCard v-for="post in posts?.data" :post="post" />
                </div>
            </div>
            <div v-if="posts?.links.length > 3" class="flex justify-end">
                <Pagination :links="posts?.links" />
            </div>
        </div>

        <div v-if="posts?.data.length <= 0">
            <SubTitle>No Post Yet for {{ author }}</SubTitle>
        </div>
    </div>
</template>
