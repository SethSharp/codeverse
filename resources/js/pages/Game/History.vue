<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { Button } from '@codinglabsau/gooey';
import StarField from '@/components/game/StarField.vue';
import DeleteGameController from '@/actions/App/Http/Controllers/Game/DeleteGameController';

interface Session {
    id: number;
    player_name: string;
    health: number;
    energy: number;
    current_encounter: number;
    game_over: boolean;
    victory: boolean;
    created_at: string;
}

defineProps<{
    sessions: Session[];
}>();

const deleteGame = async (id: number) => {
    await axios.delete(DeleteGameController.url(id));
    router.reload();
};
</script>

<template>
    <div class="relative min-h-screen bg-[#050510] font-mono text-white overflow-hidden">
        <StarField />

        <div class="relative z-10 mx-auto flex min-h-screen max-w-2xl flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-white">Game History</h1>
                <Button
                    variant="outline"
                    class="border-zinc-700 bg-zinc-900/50 font-mono text-xs text-zinc-400 hover:border-indigo-500 hover:text-white"
                    @click="router.visit('/')"
                >
                    BACK
                </Button>
            </div>

            <div v-if="sessions.length === 0" class="py-12 text-center text-sm text-zinc-600">
                No games played yet. Start your first mission.
            </div>

            <div v-else class="space-y-2">
                <div
                    v-for="session in sessions"
                    :key="session.id"
                    class="group flex items-center justify-between rounded-lg border border-zinc-800 bg-zinc-950/80 px-4 py-3 backdrop-blur"
                >
                    <div class="flex items-center gap-4">
                        <div class="flex size-10 items-center justify-center rounded bg-indigo-500/10 text-sm font-bold text-indigo-400">
                            {{ session.current_encounter }}/8
                        </div>
                        <div>
                            <div class="text-sm font-medium text-white">{{ session.player_name }}</div>
                            <div class="flex gap-3 text-xs text-zinc-500">
                                <span>HP {{ session.health }}</span>
                                <span>EN {{ session.energy }}</span>
                                <span>{{ session.created_at }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div v-if="session.game_over" class="text-xs" :class="session.victory ? 'text-green-400' : 'text-red-400'">
                            {{ session.victory ? 'COMPLETED' : 'CRASHED' }}
                        </div>
                        <div v-else class="text-xs text-indigo-400">IN PROGRESS</div>
                        <button
                            class="text-xs text-zinc-600 opacity-0 transition-opacity hover:text-red-400 group-hover:opacity-100"
                            @click="deleteGame(session.id)"
                        >
                            DELETE
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
