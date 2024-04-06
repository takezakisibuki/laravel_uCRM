<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Label from "@/Components/InputLabel.vue";
import Input from "@/Components/TextInput.vue";

const emittest = (e) => {
    console.log(e);
}
</script>

<template>
    <GuestLayout>
        <Label value="件名"></Label>
        <Input modelValue="初期値が入ります。" @update:modelValue="emittest"></Input>
    </GuestLayout>
</template>