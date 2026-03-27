<script setup lang="ts">
import { nextTick, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@codinglabsau/gooey';
import StatsBar from '@/components/game/StatsBar.vue';
import TypeWriter from '@/components/game/TypeWriter.vue';

interface Choice {
    key: string;
    text: string;
}

interface GameState {
    narrative: string;
    choices: Choice[];
    health: number;
    energy: number;
    inventory: string[];
    encounter: number;
    encounter_title: string;
    game_over: boolean;
    victory: boolean;
}

const props = defineProps<{
    gameSessionId: number;
}>();

const state = ref<GameState | null>(null);
const loading = ref(true);
const narrativeComplete = ref(false);
const scrollContainer = ref<HTMLElement>();

const csrfToken = () => document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

const makeChoice = async (choice: string) => {
    if (loading.value || !state.value) return;

    loading.value = true;
    narrativeComplete.value = false;

    try {
        const response = await fetch(`/game/${props.gameSessionId}/action`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({ choice }),
        });

        if (!response.ok) throw new Error('Failed');

        const data = await response.json();
        state.value = data;

        await nextTick();
        scrollToBottom();
    } catch {
        // Could show an error state
    } finally {
        loading.value = false;
    }
};

const scrollToBottom = () => {
    scrollContainer.value?.scrollTo({
        top: scrollContainer.value.scrollHeight,
        behavior: 'smooth',
    });
};

const onNarrativeComplete = () => {
    narrativeComplete.value = true;
};

const setInitialState = (data: GameState) => {
    state.value = data;
    loading.value = false;
};

defineExpose({ setInitialState });
</script>

<template>
    <div class="flex w-full max-w-4xl flex-col gap-4">
        <StatsBar
            v-if="state"
            :health="state.health"
            :energy="state.energy"
            :inventory="state.inventory"
            :encounter="state.encounter"
            :encounter-title="state.encounter_title"
        />

        <!-- Terminal window -->
        <div class="overflow-hidden rounded-lg border border-zinc-800 bg-zinc-950/90 shadow-2xl shadow-indigo-500/5 backdrop-blur">
            <!-- Title bar -->
            <div class="flex items-center gap-2 border-b border-zinc-800 px-4 py-2">
                <div class="size-3 rounded-full bg-red-500/80"></div>
                <div class="size-3 rounded-full bg-yellow-500/80"></div>
                <div class="size-3 rounded-full bg-green-500/80"></div>
                <span class="ml-2 text-xs text-zinc-500">codeverse://station-terminal</span>
            </div>

            <!-- Content -->
            <div ref="scrollContainer" class="h-[28rem] overflow-y-auto p-6 sm:h-[32rem]">
                <!-- Loading state -->
                <div v-if="loading && !state" class="flex h-full items-center justify-center">
                    <div class="space-y-2 text-center">
                        <div class="text-sm text-indigo-400">Connecting to station systems...</div>
                        <div class="mx-auto h-1 w-32 overflow-hidden rounded-full bg-zinc-800">
                            <div class="h-full w-1/2 animate-pulse rounded-full bg-indigo-500"></div>
                        </div>
                    </div>
                </div>

                <!-- Narrative -->
                <div v-else-if="state" class="space-y-6">
                    <TypeWriter :text="state.narrative" :speed="20" @complete="onNarrativeComplete" />

                    <!-- Choices -->
                    <div v-if="narrativeComplete && state.choices.length > 0 && !loading" class="space-y-2 pt-4">
                        <div class="text-xs font-medium tracking-wider text-zinc-500 uppercase">Choose your action:</div>
                        <div class="space-y-2">
                            <Button
                                v-for="choice in state.choices"
                                :key="choice.key"
                                variant="outline"
                                class="w-full justify-start border-zinc-700 bg-zinc-900/50 px-4 py-3 text-left font-mono text-sm text-zinc-300 transition-all hover:border-indigo-500 hover:bg-indigo-500/10 hover:text-white"
                                @click="makeChoice(choice.key)"
                            >
                                <span class="mr-3 font-bold text-indigo-400">[{{ choice.key }}]</span>
                                {{ choice.text }}
                            </Button>
                        </div>
                    </div>

                    <!-- Loading next encounter -->
                    <div v-if="loading && state" class="pt-4">
                        <div class="animate-pulse text-sm text-zinc-500">Processing command...</div>
                    </div>

                    <!-- Game over -->
                    <div v-if="state.game_over" class="pt-6 text-center">
                        <div v-if="state.victory" class="space-y-2">
                            <div class="text-2xl font-bold text-green-400">MISSION COMPLETE</div>
                            <div class="text-sm text-zinc-400">The station has been rebooted. You saved the Codeverse.</div>
                        </div>
                        <div v-else class="space-y-2">
                            <div class="text-2xl font-bold text-red-400">SYSTEM CRASH</div>
                            <div class="text-sm text-zinc-400">The station has gone dark. The Codeverse is lost.</div>
                        </div>
                        <Button class="mt-4 bg-indigo-600 font-mono text-sm text-white hover:bg-indigo-500" @click="router.visit('/game')">
                            RESTART
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
