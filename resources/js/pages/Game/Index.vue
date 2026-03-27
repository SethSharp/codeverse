<script setup lang="ts">
import { nextTick, ref } from 'vue';
import StarField from '@/components/game/StarField.vue';
import Terminal from '@/components/game/Terminal.vue';
import TitleScreen from '@/components/game/TitleScreen.vue';

interface GameResponse {
    game_session_id: number;
    narrative: string;
    choices: { key: string; text: string }[];
    health: number;
    energy: number;
    inventory: string[];
    encounter: number;
    encounter_title: string;
    game_over: boolean;
    victory: boolean;
}

const gameStarted = ref(false);
const initialData = ref<GameResponse | null>(null);
const terminal = ref<InstanceType<typeof Terminal>>();

const onGameStart = async (data: GameResponse) => {
    initialData.value = data;
    gameStarted.value = true;

    await nextTick();
    terminal.value?.setInitialState(data);
};
</script>

<template>
    <div class="relative min-h-screen bg-[#050510] font-mono text-white overflow-hidden">
        <StarField />

        <div class="relative z-10 flex min-h-screen items-center justify-center p-4">
            <TitleScreen v-if="!gameStarted" @start="onGameStart" />
            <Terminal v-else ref="terminal" :game-session-id="initialData!.game_session_id" />
        </div>
    </div>
</template>
