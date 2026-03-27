<script setup lang="ts">
import { onMounted, ref } from 'vue';

const canvas = ref<HTMLCanvasElement>();

onMounted(() => {
    const el = canvas.value!;
    const ctx = el.getContext('2d')!;

    const resize = () => {
        el.width = window.innerWidth;
        el.height = window.innerHeight;
    };

    resize();
    window.addEventListener('resize', resize);

    const stars = Array.from({ length: 200 }, () => ({
        x: Math.random() * el.width,
        y: Math.random() * el.height,
        size: Math.random() * 2,
        speed: Math.random() * 0.5 + 0.1,
        opacity: Math.random(),
    }));

    const draw = () => {
        ctx.fillStyle = '#050510';
        ctx.fillRect(0, 0, el.width, el.height);

        for (const star of stars) {
            star.y -= star.speed;
            star.opacity += (Math.random() - 0.5) * 0.02;
            star.opacity = Math.max(0.1, Math.min(1, star.opacity));

            if (star.y < 0) {
                star.y = el.height;
                star.x = Math.random() * el.width;
            }

            ctx.beginPath();
            ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255, 255, 255, ${star.opacity})`;
            ctx.fill();
        }

        requestAnimationFrame(draw);
    };

    draw();
});
</script>

<template>
    <canvas ref="canvas" class="fixed inset-0 z-0" />
</template>
