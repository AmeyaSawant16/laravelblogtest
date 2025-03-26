<script setup lang="ts">
import { usePage, router} from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import { Inertia } from '@inertiajs/inertia';

interface Props {
    postId: number;
    createdById: number;
}

const props = defineProps<Props>();

const page = usePage();
const userId = page.props?.auth.user?.id;

const showEditPost = (id: any) => {
    Inertia.visit(route('post.editPost', id));
}

const deletePost = (id: any) => {
    router.delete(route('post.deletePost', id), {
      preserveScroll: true
    });
}


</script>

<template>
   <div class="flex flex-row gap-4" v-if="userId && createdById == userId">
        <Button @click="showEditPost(postId)" class="w-[15vh]">Edit</Button>
        <Button variant="destructive" @click="deletePost(postId)" class="w-[15vh]">Delete</Button>
    </div>
</template>
