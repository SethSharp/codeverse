import { computed, onUnmounted, ref, watch, type Ref } from 'vue';

const VERBS = [
    'Processing',
    'Thinking',
    'Cooking',
    'Sizzling',
    'Hallucinating',
    'Compiling',
    'Defragmenting',
    'Rebooting neurons',
    'Consulting the void',
    'Asking the space wizard',
    'Reversing the polarity',
    'Warming up the flux capacitor',
    'Grepping the universe',
    'Stack overflowing',
    'Debugging reality',
];

const randomVerb = () => VERBS[Math.floor(Math.random() * VERBS.length)];

export const useLoadingDots = (loading: Ref<boolean>, base?: string, interval = 400) => {
    const dotCount = ref(1);
    const currentVerb = ref(base ?? randomVerb());
    let timer: ReturnType<typeof setInterval>;

    const text = computed(() => (loading.value ? currentVerb.value + '.'.repeat(dotCount.value) : ''));

    const start = () => {
        currentVerb.value = base ?? randomVerb();
        dotCount.value = 1;
        timer = setInterval(() => {
            dotCount.value = (dotCount.value % 3) + 1;
        }, interval);
    };

    if (loading.value) {
        start();
    }

    watch(loading, (isLoading) => {
        if (isLoading) {
            start();
        } else {
            clearInterval(timer);
        }
    });

    onUnmounted(() => clearInterval(timer));

    return text;
};
