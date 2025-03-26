<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
// import AuthBase from '@/layouts/auth/AuthCardLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import axios from 'axios';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
    stage: 0,
    otp: '',
    remember: false,
});

const stage = ref(0);

const submit = () => {
    console.log(`stage`, stage.value);
    if( stage.value == 0 ){
        form.processing = true;
        axios({
            method: 'post',
            url: 'send_otp',
            data: {
                email: form.email,
            }
        }).then( (data) => {
            if(data.data.status == "200"){
                stage.value = 1;
            }
            form.processing = false;
        } );
    }else if(stage.value == 1){
        form.post(route('login'), {
            onFinish: () => {
                form.reset('email', 'otp')
            }
        });
    }
};

</script>

<template>
    <AuthBase title="Log in to your account" description="Enter your email and password below to log in">
        <Head title="Log in" />
        <TextLink :href="route('home')" :tabindex="5" v-if="stage == 0"> <- Go Back to Home Screen</TextLink>
        <TextLink :href="route('login')" :tabindex="5" v-if="stage == 1"> <- Change Email</TextLink>

        <!-- <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div> -->

        <form @submit.prevent="submit" class="flex flex-col gap-6" v-if="stage == 0">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                    />
                    <Input type="hidden" id="stage" v-model="form.stage" value="0"/>
                    <InputError :message="form.errors.email" />
                </div>
                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Send OTP
                </Button>
            </div>
            </form>
            <form @submit.prevent="submit" class="flex flex-col gap-6" v-if="stage == 1">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="otp">Enter OTP</Label>
                        <!-- <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" :tabindex="5">
                            Forgot password?
                        </TextLink> -->
                    </div>
                    <Input type="hidden" id="email" v-model="form.email"/>
                    <Input
                        id="otp"
                        required
                        :tabindex="2"
                        v-model="form.otp"
                    />
                    <InputError :message="form.errors.otp" />
                </div>

                <div class="flex items-center justify-between" :tabindex="3">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model:checked="form.remember" :tabindex="4" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Don't have an account?
                <TextLink :href="route('register')" :tabindex="5">Sign up</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
