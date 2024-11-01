<template>
    <div class="mt-4">
        <InputLabel for="password" value="Password" />

        <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="new-password"
        />

        <InputError class="mt-2" :message="form.errors.password" />
    </div>

    <div class="mt-4">
        <InputLabel for="password_confirmation" value="Confirm Password" />

        <TextInput
            id="password_confirmation"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password_confirmation"
            required
            autocomplete="new-password"
        />

        <InputError class="mt-2" :message="form.errors.password_confirmation" />
    </div>

</template>

<script setup>
import { ref, reactive } from 'vue';

const form = reactive({
    password:null,
    password_confirmation:null,
    email:null,
})
</script>