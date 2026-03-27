<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Button } from '@codinglabsau/gooey';
import ShowGameController from '@/actions/App/Http/Controllers/Game/ShowGameController';
import HistoryController from '@/actions/App/Http/Controllers/Game/HistoryController';

interface LatestSession {
    id: number;
    player_name: string;
    health: number;
    energy: number;
    current_encounter: number;
    game_over: boolean;
}

defineProps<{
    latestSession: LatestSession | null;
}>();

defineEmits<{
    newGame: [];
}>();

const resumeGame = (id: number) => {
    router.visit(ShowGameController.url(id));
};
</script>

<template>
    <div class="flex w-full max-w-2xl flex-col items-center gap-8 text-center">
        <div class="space-y-4">
            <img src="/svg/codinglabs-logo.svg" alt="Coding Labs" class="mx-auto h-8 opacity-60" />
            <div class="text-sm font-medium tracking-[0.3em] text-indigo-400 uppercase">presents</div>
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">
                Lost in the
                <span class="bg-gradient-to-r from-indigo-400 to-orange-400 bg-clip-text text-transparent">Codeverse</span>
            </h1>
        </div>

        <div class="w-full max-w-xs space-y-3">
            <!-- Continue -->
            <Button
                v-if="latestSession && !latestSession.game_over"
                class="w-full bg-indigo-600 py-3 font-mono text-sm font-medium text-white hover:bg-indigo-500"
                @click="resumeGame(latestSession.id)"
            >
                CONTINUE
                <span class="ml-2 text-xs text-indigo-300">
                    ({{ latestSession.player_name }} — Room {{ latestSession.current_encounter }}/8)
                </span>
            </Button>

            <!-- New Game -->
            <Button
                variant="outline"
                class="w-full border-zinc-700 bg-zinc-900/50 py-3 font-mono text-sm text-zinc-300 hover:border-indigo-500 hover:bg-indigo-500/10 hover:text-white"
                @click="$emit('newGame')"
            >
                NEW GAME
            </Button>

            <!-- History -->
            <Button
                variant="outline"
                class="w-full border-zinc-800 bg-zinc-950/50 py-3 font-mono text-xs text-zinc-500 hover:border-zinc-700 hover:text-zinc-400"
                @click="router.visit(HistoryController.url())"
            >
                HISTORY
            </Button>
        </div>
    </div>
</template>
