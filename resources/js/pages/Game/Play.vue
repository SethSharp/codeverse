<script setup lang="ts">
import { nextTick, onMounted, ref } from 'vue';
import axios from 'axios';
import StarField from '@/components/game/StarField.vue';
import Terminal from '@/components/game/Terminal.vue';
import ResumeGameController from '@/actions/App/Http/Controllers/Game/ResumeGameController';
import { useLoadingDots } from '@/composables/useLoadingDots';

interface GameSessionData {
    id: number;
    player_name: string;
    health: number;
    energy: number;
    inventory: unknown[];
    current_encounter: number;
    game_over: boolean;
    victory: boolean;
}

const props = defineProps<{
    gameSession: GameSessionData;
    initialState?: Record<string, unknown>;
}>();

const terminal = ref<InstanceType<typeof Terminal>>();
const loading = ref(true);
const loadingText = useLoadingDots(loading, 'Connecting to station');

onMounted(async () => {
    if (props.initialState) {
        loading.value = false;

        await nextTick();
        terminal.value?.setInitialState(props.initialState);
        return;
    }

    try {
        const { data } = await axios.post(ResumeGameController.url(props.gameSession.id));
        loading.value = false;

        await nextTick();
        terminal.value?.setInitialState(data);
    } catch {
        loading.value = false;
    }
});
</script>

<template>
    <div class="relative min-h-screen bg-[#050510] font-mono text-white overflow-hidden">
        <StarField />

        <div class="relative z-10 flex min-h-screen items-center justify-center p-4">
            <div v-if="loading" class="text-sm text-indigo-400">{{ loadingText }}</div>
            <Terminal v-else ref="terminal" :game-session-id="gameSession.id" />
        </div>
    </div>
</template>
