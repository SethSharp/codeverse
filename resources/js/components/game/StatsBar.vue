<script setup lang="ts">
interface InventoryItem {
    name: string;
    description: string;
}

defineProps<{
    health: number;
    energy: number;
    inventory: InventoryItem[];
    encounter: number;
    encounterTitle: string;
}>();
</script>

<template>
    <div class="flex flex-wrap items-center gap-4 rounded-lg border border-zinc-800 bg-zinc-950/80 px-4 py-3 text-xs backdrop-blur">
        <!-- Encounter -->
        <div class="flex items-center gap-2">
            <span class="text-zinc-500">ROOM</span>
            <span class="font-bold text-white">{{ encounter }}/8</span>
            <span class="text-zinc-600">&middot;</span>
            <span class="text-indigo-400">{{ encounterTitle }}</span>
        </div>

        <div class="hidden h-4 w-px bg-zinc-800 sm:block"></div>

        <!-- Health -->
        <div class="flex items-center gap-2">
            <span class="text-zinc-500">HP</span>
            <div class="h-2 w-20 overflow-hidden rounded-full bg-zinc-800">
                <div
                    class="h-full rounded-full transition-all duration-500"
                    :class="health > 50 ? 'bg-green-500' : health > 25 ? 'bg-yellow-500' : 'bg-red-500'"
                    :style="{ width: `${health}%` }"
                ></div>
            </div>
            <span class="font-mono text-white">{{ health }}</span>
        </div>

        <!-- Energy -->
        <div class="flex items-center gap-2">
            <span class="text-zinc-500">EN</span>
            <div class="h-2 w-20 overflow-hidden rounded-full bg-zinc-800">
                <div
                    class="h-full rounded-full bg-indigo-500 transition-all duration-500"
                    :style="{ width: `${energy}%` }"
                ></div>
            </div>
            <span class="font-mono text-white">{{ energy }}</span>
        </div>

        <!-- Inventory -->
        <div v-if="inventory.length > 0" class="flex items-center gap-2">
            <span class="text-zinc-500">INV</span>
            <span
                v-for="(item, i) in inventory"
                :key="i"
                :title="item.description"
                class="cursor-default rounded bg-zinc-800 px-1.5 py-0.5 text-orange-400"
            >
                {{ item.name }}
            </span>
        </div>
    </div>
</template>
