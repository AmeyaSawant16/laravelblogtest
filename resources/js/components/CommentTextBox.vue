<script setup lang="ts">
import { Link, useForm} from '@inertiajs/vue3';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import Button from '@/components/ui/button/Button.vue';
import InputError from '@/components/InputError.vue';

interface Props {
    userId?: number;
    postId: number;
}

const props = defineProps<Props>();

const form = useForm({
    user_comment: '',
});

const addComment = () => {
    form
    .transform((data) => ({
        ...data,
        user_id: props.userId,
        post_id: props.postId,
    }))
    .post(route('comment.addComment'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};


</script>

<template>
   <form class="flex flex-col gap-4" v-if="userId" @submit.prevent="addComment" method="post">
        <h2 class="text-2xl">Post a Comment</h2>
        <Textarea rows="5" v-model="form.user_comment"/>
        <InputError class="mt-2" :message="form.errors.user_comment" />
        <Button type="submit" class="w-[30vh]">Post Comment</Button>
    </form>
    <div v-else>
        <Link
            :href="route('login')"
            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-md leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
        >
            Click here to Log in, To Post Comment
        </Link>
    </div>
</template>
