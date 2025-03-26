<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, onMounted, onUnmounted, ref, useTemplateRef, watch } from 'vue'
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PostGridView from '@/components/PostGridView.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/home',
    },
];

const fallbackImg = 'post_cover_image_1400x600.svg'

const postList:any = usePage().props.postList;

console.log(`Cache Data: `, usePage().props.cacheData);
console.log(`postList: `, postList);

const searchResult = computed(() => usePage().props.searchResult);

const scrollTarget = useTemplateRef<HTMLElement>('posts-grid')
const data = ref(postList[0]);
const pgIndex = 0;

watch( searchResult, (newVal) => {
    if(newVal){
        const test = newVal.map( (item) => {
            return item._source;
        } );

        data.value = test;
    }else{
        data.value = postList;
    }
} )

function loadMorePosts() {
    if( postList.length() > pgIndex ){
        pgIndex += 1;
        data.value.push(...postList[pgIndex])
    }
    // console.log(`Im triggered`)
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

</script>

<template >
    <Head title="Home" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div  class="flex flex-1 flex-col gap-4 rounded-xl p-4">
            <div ref="posts-grid" class="grid auto-rows-min gap-4 md:grid-cols-3" >
                <PostGridView v-for="index in data" :comment-count="index.comment_count" :publish-date="index.formatted_date" :imageurl="index.image ?? fallbackImg" :excerpt="index.excerpt" :post-id="index.id" :title="index.title"/>
            </div>
        </div>
    </AppLayout>
</template>
