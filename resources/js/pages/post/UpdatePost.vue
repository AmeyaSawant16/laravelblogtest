<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import Separator from '@/components/ui/separator/Separator.vue';
import { ref } from 'vue';
import TextLink from '@/components/TextLink.vue';

const page = usePage();

const user = page.props.auth.user as User;
const postData = page.props.postDetails;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My Post',
        href: '/post/my_post',
    },
    {
        title: 'Update Post',
        href: '',
    },
];

const publishTypeOptions = [
    {
        label: 'Now',
        value: 'now',
    },
    {
        label: 'Schedule',
        value: 'schedule',
    }
];

// const 
const imageInput = ref();
const form = useForm({
    title: postData.title,
    excerpt: postData.excerpt,
    content: postData.content,
    image: '',
    keywords: JSON.parse(postData.tags).join(','),
    meta_title: postData.meta_title,
    meta_description: postData.meta_description,
    publish_type: postData.publish_type,
    publish_datetime: postData.publish_datetime,
});

const handleFileUpload = (event: any) => {
    form.image = event.target.files[0]; // Store file object
};

const submit = () => {
    form.post(route('post.updatePost', postData.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Update Post" />
    
            <div class="my-6 mx-4 flex flex-col space-y-6">
                <TextLink :href="route('post.postDetail', postData.id)"> <- Go Back</TextLink>
                <HeadingSmall title="Update Post"/>

                <form  @submit.prevent="submit" method="put" class="space-y-6" enctype="multipart/form-data">
                    <div class="grid gap-2">
                        <Label for="post_title">Post Title</Label>
                        <Input id="post_title" class="mt-1 block w-full" v-model="form.title" required autocomplete="title" placeholder="Post Title" />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="post_excerpt">Post Excerpt</Label>
                        <Textarea
                            rows="5"
                            id="post_excerpt"
                            class="mt-1 block w-full"
                            v-model="form.excerpt"
                            placeholder="Post Excerpt"
                        />
                        <InputError class="mt-2" :message="form.errors.excerpt" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Post Content</Label>
                        <Textarea
                            rows="5"
                            id="post_description"
                            class="mt-1 block w-full"
                            v-model="form.content"
                            placeholder="Post Content"
                        />
                        <InputError class="mt-2" :message="form.errors.content" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="post_image">Post Cover Image</Label>
                        <Input id="post_image" type="file" name="image" class="mt-1 block w-full" placeholder="Post Cover Image" ref="imageInput"
                        @change="handleFileUpload"/>
                        <InputError class="mt-2" :message="form.errors.image" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="post_keywords">Post Tags</Label>
                        <Input id="post_keywords" class="mt-1 block w-full" v-model="form.keywords" placeholder="Post Tags" />
                        <InputError class="mt-2" :message="form.errors.keywords" />
                    </div>

                    <Separator/>
                    <HeadingSmall title="Post Meta Details" description="Add meta details for the post."/>

                    <div class="grid gap-2">
                        <Label for="post_meta_title">Post Meta Title</Label>
                        <Input id="post_meta_title" class="mt-1 block w-full" v-model="form.meta_title" placeholder="Meta Title" />
                        <InputError class="mt-2" :message="form.errors.meta_title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Post Meta Description</Label>
                        <Textarea
                            rows="5"
                            id="meta_description"
                            class="mt-1 block w-full"
                            v-model="form.meta_description"
                            
                            placeholder="Meta Description"
                        />
                        <InputError class="mt-2" :message="form.errors.meta_description" />
                    </div>

                    <Separator/>
                    <HeadingSmall title="Post Scheduling" description="Manage when to publish post."/>

                    <div class="grid gap-2">
                        <Label for="publish_type">When to Publish?</Label>
                        <Select :options="publishTypeOptions" class="mt-1 block w-full" v-model="form.publish_type" />
                        <!-- <Input id="publish_type" type="select" class="mt-1 block w-full" v-model="form.publish_type" placeholder="Select Publish Time" /> -->
                        <InputError class="mt-2" :message="form.errors.publish_type" />
                    </div>

                    <div class="grid gap-2" v-if="form.publish_type == 'schedule'">
                        <Label for="publish_datetime">Schedule Date & Time</Label>
                        <Input id="publish_datetime" type="datetime-local" class="mt-1 block w-full" v-model="form.publish_datetime" placeholder="Select Publish Time" />
                        <InputError class="mt-2" :message="form.errors.publish_datetime" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="form.processing">Update</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Post Updated.</p>
                        </Transition>
                    </div>
                </form>
            </div>
    </AppLayout>
</template>
