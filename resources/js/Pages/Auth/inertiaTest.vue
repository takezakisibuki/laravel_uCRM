<script  setup>
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const newTitle = ref('');
const newContent = ref('');
</script>

<template>
    <p>inertiaのテストです。</p>
    <br>
    <a href="/">aタグ経由です</a><br>
    <Link href="/">link経由です</Link><br>
    <Link :href="route('inertia.index')">名前付きルートの確認です</Link><br>
    <Link :href="route('inertia.show',{id:1})">ルートパラメータのテストです。</Link><br>
    <div class="mb-8"></div>
    <input type="text" name="newTitle" v-model="newTitle" placeholder="メッセージを入力してください">{{}}<br>
    <input type="text" name="newTitle" v-model="newContent" placeholder="メッセージを入力してください"><br>
    <Link as="button" :href="route('inertia.store')" method="post" 
    :data="{
        title:newTitle,
        content:newContent
        }">DB保存テスト</Link>

</template>