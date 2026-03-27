<script setup lang="ts">
import { nextTick, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { Button, Input } from '@codinglabsau/gooey';
import StoreActionController from '@/actions/App/Http/Controllers/Game/StoreActionController';
import AskAiQuestionController from '@/actions/App/Http/Controllers/Game/AskAiQuestionController';
import IndexGameController from '@/actions/App/Http/Controllers/Game/IndexGameController';
import StatsBar from '@/components/game/StatsBar.vue';
import TypeWriter from '@/components/game/TypeWriter.vue';
import { useLoadingDots } from '@/composables/useLoadingDots';

interface Choice {
    key: string;
    text: string;
}

interface GameState {
    narrative: string;
    input_type: 'choice' | 'code';
    hint: string;
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

const MAX_AI_USES = 3;

const state = ref<GameState | null>(null);
const loading = ref(true);
const narrativeComplete = ref(false);
const scrollContainer = ref<HTMLElement>();
const codeAnswer = ref('');
const aiUsesRemaining = ref(MAX_AI_USES);
const aiThinking = ref(false);
const processingText = useLoadingDots(loading);

const sendAction = async (body: Record<string, string>) => {
    if (loading.value || !state.value) return;

    loading.value = true;
    narrativeComplete.value = false;
    codeAnswer.value = '';

    try {
        const { data } = await axios.post(StoreActionController.url(props.gameSessionId), body);
        state.value = data;

        await nextTick();
        scrollToBottom();
    } catch {
        // Could show an error state
    } finally {
        loading.value = false;
    }
};

const makeChoice = (choice: string) => {
    const selected = state.value?.choices.find((c) => c.key === choice);
    sendAction({ choice, choice_text: selected?.text ?? '' });
};

const submitCode = () => {
    if (!codeAnswer.value.trim()) return;
    sendAction({ answer: codeAnswer.value.trim() });
};

const askAi = async () => {
    if (aiUsesRemaining.value <= 0 || aiThinking.value || !state.value) return;

    aiThinking.value = true;
    aiUsesRemaining.value--;

    try {
        const { data } = await axios.post(AskAiQuestionController.url(props.gameSessionId), {
            narrative: state.value!.narrative,
            hint: state.value!.hint,
        });
        codeAnswer.value = data.suggestion;
    } catch {
        codeAnswer.value = '// AI systems offline';
    } finally {
        aiThinking.value = false;
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

                    <!-- Multiple choice input -->
                    <div v-if="narrativeComplete && state.input_type === 'choice' && state.choices.length > 0 && !loading" class="space-y-2 pt-4">
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

                    <!-- Code challenge input -->
                    <div v-if="narrativeComplete && state.input_type === 'code' && !loading && !state.game_over" class="space-y-3 pt-4">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-medium tracking-wider text-orange-400 uppercase">Code Challenge</div>
                            <div v-if="aiUsesRemaining > 0" class="group relative">
                                <Button
                                    variant="outline"
                                    class="border-zinc-700 bg-zinc-900/50 px-3 py-1 font-mono text-xs text-indigo-400 hover:border-indigo-500 hover:bg-indigo-500/10"
                                    :disabled="aiThinking"
                                    @click="askAi"
                                >
                                    {{ aiThinking ? 'Thinking...' : `Ask AI (${aiUsesRemaining} left)` }}
                                </Button>
                                <div class="pointer-events-none absolute right-0 bottom-full mb-2 w-48 rounded bg-zinc-800 px-3 py-2 text-xs text-zinc-400 opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                                    AI can make mistakes
                                </div>
                            </div>
                            <div v-else class="text-xs text-zinc-600">AI assists depleted</div>
                        </div>
                        <div v-if="state.hint" class="rounded border border-zinc-800 bg-zinc-900/50 px-3 py-2 text-xs text-zinc-400">
                            <span class="text-indigo-400">HINT:</span> {{ state.hint }}
                        </div>
                        <div class="space-y-2">
                            <textarea
                                v-model="codeAnswer"
                                placeholder="Type your answer... (Enter to submit, Shift+Enter for new line)"
                                rows="3"
                                class="w-full resize-none rounded-md border border-zinc-700 bg-zinc-900 px-3 py-2 font-mono text-sm text-white placeholder:text-zinc-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none"
                                @keydown.enter.exact.prevent="submitCode"
                            />
                            <Button
                                class="w-full bg-orange-600 font-mono text-sm text-white hover:bg-orange-500"
                                :disabled="!codeAnswer.trim()"
                                @click="submitCode"
                            >
                                EXECUTE
                            </Button>
                        </div>
                    </div>

                    <!-- Loading next encounter -->
                    <div v-if="loading && state" class="pt-4">
                        <div class="text-sm text-zinc-500">{{ processingText }}</div>
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
                        <Button class="mt-4 bg-indigo-600 font-mono text-sm text-white hover:bg-indigo-500" @click="router.visit(IndexGameController.url())">
                            RESTART
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
