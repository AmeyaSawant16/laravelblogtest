<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, onMounted, onUnmounted, ref, useTemplateRef, watch } from 'vue'
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import Button from '@/components/ui/button/Button.vue';
import PostGridView from '@/components/PostGridView.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My Post',
        href: '/my_post',
    },
];


const scrollTarget = useTemplateRef<HTMLElement>('posts-grid')

const searchResult = computed(() => usePage().props.searchResult);

const postList:any = usePage().props.postList;
const data = ref(postList[0]);
let pgIndex = 0;

watch( searchResult, (newVal) => {
    if(newVal){
        const test = newVal.filter((item) => item._source.created_by == usePage().props.auth.user.id ).map( (item) => {
                return item._source;
        } );

        data.value = test;
    }else{
        data.value = postList[0];
    }
} )

function loadMorePosts() {
    if( postList.length > pgIndex ){
        pgIndex += 1;
        if(postList[pgIndex]){
            data.value.push(...postList[pgIndex])
        }
    }
}

onMounted(() => {
  window.addEventListener("scroll", handleScroll)
 })

 onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll)
 })

 const handleScroll = (e: any) => {
    let element = scrollTarget.value
  if (element.getBoundingClientRect().bottom < window.innerHeight) {
    loadMorePosts()
  }
 }

 const createPost = () => {
    Inertia.visit(route('post.createPost'));
 }

 const fallbackImg = 'post_cover_image_1400x600.svg'

</script>

<template >
    <Head title="My Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
            <Button @click="createPost()" class="w-[30vh]">
                Create New Post
            </Button>
            <div ref="posts-grid" class="grid auto-rows-min gap-4 md:grid-cols-3" >
                <PostGridView v-for="index in data" :comment-count="index.comment_count" :publish-date="index.formatted_date" :imageurl="index.image ?? fallbackImg" :excerpt="index.excerpt" :post-id="index.id" :title="index.title"/>
            </div>
        </div>
    </AppLayout>
</template>
