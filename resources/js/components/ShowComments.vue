<script setup lang="ts">
import { useDateFormat  } from '@vueuse/core';
import Separator from './ui/separator/Separator.vue';

interface Props {
    userName: string;
    userComment: string;
    commentDatetime: string;
}

defineProps<Props>();
</script>

<template>
    <div class="mb-5">
        <p class="my-2 text-xl">{{ userName }} <span class="text-xs ml-2">({{ useDateFormat(commentDatetime, 'MMM DD, YYYY - hh:mm a') }})</span></p>
        <p class="text-gray-300">{{ userComment }}</p>
    </div>
    <Separator/>
</template>
