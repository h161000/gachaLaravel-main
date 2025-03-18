<script setup>
defineProps(['message']);
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-[#ff0000]">
            {{ message }}
        </p>
    </div>
</template>
