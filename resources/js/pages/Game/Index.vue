<script setup lang="ts">
import { ref } from 'vue';
import StarField from '@/components/game/StarField.vue';
import HomeMenu from '@/components/game/HomeMenu.vue';
import TitleScreen from '@/components/game/TitleScreen.vue';

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

const showNewGame = ref(false);
</script>

<template>
    <div class="relative min-h-screen bg-[#050510] font-mono text-white overflow-hidden">
        <StarField />

        <div class="relative z-10 flex min-h-screen items-center justify-center p-4">
            <HomeMenu v-if="!showNewGame" :latest-session="latestSession" @new-game="showNewGame = true" />
            <TitleScreen v-else />
        </div>
    </div>
</template>
