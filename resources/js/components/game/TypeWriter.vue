<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        text: string;
        speed?: number;
    }>(),
    { speed: 20 },
);

const emit = defineEmits<{
    complete: [];
}>();

const displayed = ref('');
let timeout: ReturnType<typeof setTimeout>;

const typeText = (text: string) => {
    displayed.value = '';
    let i = 0;

    const next = () => {
        if (i < text.length) {
            displayed.value += text[i];
            i++;
            timeout = setTimeout(next, props.speed);
        } else {
            emit('complete');
        }
    };

    next();
};

const skip = () => {
    clearTimeout(timeout);
    displayed.value = props.text;
    emit('complete');
};

onMounted(() => typeText(props.text));

watch(
    () => props.text,
    (newText) => {
        clearTimeout(timeout);
        typeText(newText);
    },
);
</script>

<template>
    <div class="cursor-pointer whitespace-pre-wrap text-sm leading-relaxed text-zinc-300" @click="skip">
        {{ displayed }}<span class="animate-pulse text-indigo-400">█</span>
    </div>
</template>
