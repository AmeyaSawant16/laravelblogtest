<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Link, usePage } from '@inertiajs/vue3';
import HeadingSmall from './HeadingSmall.vue';
import { Label } from './ui/label';

interface Props {
    postId: number;
    imageurl: string;
    title: string;
    excerpt: string;
    commentCount: number;
    publishDate: string;

}

const props = defineProps<Props>();
const appUrl = usePage().props.ziggy.url;
</script>

<template>
   <Card class="rounded-xl overflow-hidden">
        <img class="relative border border-sidebar-border/70 dark:border-sidebar-border" :src="appUrl+'/post/image/'+imageurl" />
        <CardHeader class="px-4 pb-0 pt-2 text-center">
            <CardTitle class="text-xl text-left">{{ title }}</CardTitle>
            <HeadingSmall :title="'Published Date: '+publishDate" class="text-left text-xs" />
            <CardDescription class="text-left">
                {{ excerpt }}
            </CardDescription>
        </CardHeader>
        <CardContent class="flex flex-row gap-2 mt-2 px-4 py-4">
            <Label>Total Comments: {{ commentCount }}</Label>
            <Link class="text-right ml-auto" :href="route('post.postDetail', postId)">View Post</Link>
        </CardContent>
    </Card>
</template>
