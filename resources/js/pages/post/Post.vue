<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage,} from '@inertiajs/vue3';
import { computed } from 'vue';
import ShowComment from '@/components/ShowComments.vue';
import CommentTextBox from '@/components/CommentTextBox.vue';
import SinglePostEditHeader from '@/components/SinglePostEditHeader.vue';
import SinglePostView from '@/components/SinglePostView.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Post',
        href: '/post',
    },
];

const page = usePage();
const auth = computed(() => page.props.auth);
const userDetails = computed(() => page.props.userDetails);
const postDetails = computed(() => page.props.post);
console.log(postDetails);
const comments = computed(() => page.props.comments);
const fallbackImg = 'post_cover_image_1400x600.svg'
</script>

<template >
    <Head title="Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div  class="flex flex-1 flex-col gap-4 rounded-xl p-4">

            <!-- <PlaceholderPattern /> -->
            <SinglePostEditHeader :post-id="postDetails.id" :created-by-id="postDetails.created_by" />

            <SinglePostView :imageurl="postDetails.image ?? fallbackImg" :name="userDetails[0]?.name" :title="postDetails.title" :post_date="postDetails.formatted_date">
                <p>
                    {{ postDetails.content }}
                </p>
            </SinglePostView>
            
            <CommentTextBox :post-id="postDetails.id" :user-id="auth.user?.id"/>
            <span class="flex flex-1 flex-col gap-4 rounded-xl" v-if="comments.length > 0">
                <h2 class="mt-3 text-2xl">Latest Comments:</h2>
                <div class="w-full rounded-md border border-input bg-background px-3 pt-2 text-base ring-offset-background">
                    <ShowComment v-for="comment in comments" :user-name="comment.name" :user-comment="comment.user_comment" :comment-datetime="comment.comment_date"/>
                </div>
            </span>
        </div>
    </AppLayout>
</template>
