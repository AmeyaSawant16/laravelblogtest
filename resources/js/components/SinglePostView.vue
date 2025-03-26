<script setup lang="ts">
import Separator from '@/components/ui/separator/Separator.vue';
import Label from '@/components/ui/label/Label.vue';
import { usePage } from '@inertiajs/vue3';

interface Props {
    imageurl: string;
    title: string;
    name: string;
    post_date: string;
}

const props = defineProps<Props>();

const appUrl = usePage().props.ziggy.url;

</script>

<template>
   <img class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border" :src="appUrl+'/post/image/'+imageurl" />
    <Separator />
    <h1 class="text-3xl">{{ title }}</h1>
    <Label class="italic">Created By: {{ name }}</Label>
    <Label class="italic">Published Date: {{ post_date }}</Label>
    <slot />
    <Separator />
</template>
