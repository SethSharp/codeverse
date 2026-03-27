<script setup lang="ts">
import { ref } from 'vue';
import { Button, Input } from '@codinglabsau/gooey';

const emit = defineEmits<{
    start: [data: Record<string, unknown>];
}>();

const playerName = ref('');
const loading = ref(false);
const error = ref('');

const startGame = async () => {
    if (!playerName.value.trim()) {
        error.value = 'Enter your name, recruit.';
        return;
    }

    loading.value = true;
    error.value = '';

    try {
        const response = await fetch('/game/start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({ player_name: playerName.value.trim() }),
        });

        if (!response.ok) {
            throw new Error('Failed to start game');
        }

        const data = await response.json();
        emit('start', data);
    } catch {
        error.value = 'System malfunction. Try again.';
        loading.value = false;
    }
};
</script>

<template>
    <div class="flex w-full max-w-2xl flex-col items-center gap-8 text-center">
        <!-- Logo / Title -->
        <div class="space-y-4">
            <img src="/svg/codinglabs-logo.svg" alt="Coding Labs" class="mx-auto h-8 opacity-60" />
            <div class="text-sm font-medium tracking-[0.3em] text-indigo-400 uppercase">presents</div>
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">
                Lost in the
                <span class="bg-gradient-to-r from-indigo-400 to-orange-400 bg-clip-text text-transparent">Codeverse</span>
            </h1>
            <p class="mx-auto mt-4 max-w-md text-sm text-zinc-400">
                The station is failing. Systems are crashing. You're the only developer who can reach the Core Reactor and reboot
                before it all implodes.
            </p>
        </div>

        <!-- Terminal-style input -->
        <div class="w-full max-w-sm space-y-4">
            <div class="rounded-lg border border-zinc-800 bg-zinc-950/80 p-6 backdrop-blur">
                <div class="mb-4 flex items-center gap-2 text-xs text-zinc-500">
                    <div class="size-2 rounded-full bg-green-500"></div>
                    <span>STATION TERMINAL v3.7.1</span>
                </div>

                <label class="mb-2 block text-left text-xs text-zinc-400">
                    <span class="text-indigo-400">$</span> IDENTIFY YOURSELF, DEVELOPER:
                </label>
                <Input
                    v-model="playerName"
                    placeholder="Enter your name..."
                    class="border-zinc-700 bg-zinc-900 font-mono text-white placeholder:text-zinc-600 focus:border-indigo-500 focus:ring-indigo-500"
                    :maxlength="50"
                    @keyup.enter="startGame"
                />

                <p v-if="error" class="mt-2 text-xs text-red-400">{{ error }}</p>

                <Button
                    class="mt-4 w-full bg-indigo-600 font-mono text-sm font-medium text-white hover:bg-indigo-500"
                    :disabled="loading"
                    @click="startGame"
                >
                    {{ loading ? 'INITIALISING...' : 'LAUNCH MISSION' }}
                </Button>
            </div>

            <p class="text-xs text-zinc-600">8 encounters &middot; ~15 min &middot; AI-powered narrative</p>
        </div>
    </div>
</template>
