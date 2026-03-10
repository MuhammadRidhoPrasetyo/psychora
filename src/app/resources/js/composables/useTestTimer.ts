import { ref, computed, onUnmounted } from 'vue';

export function useTestTimer(expiredAt: string | null, onExpire?: () => void) {
    const remainingSeconds = ref(0);
    let intervalId: ReturnType<typeof setInterval> | null = null;

    const calculateRemaining = (): number => {
        if (!expiredAt) return 0;
        const diff = Math.floor((new Date(expiredAt).getTime() - Date.now()) / 1000);
        return Math.max(0, diff);
    };

    const hours = computed(() => Math.floor(remainingSeconds.value / 3600));
    const minutes = computed(() => Math.floor((remainingSeconds.value % 3600) / 60));
    const seconds = computed(() => remainingSeconds.value % 60);

    const formatted = computed(() => {
        const h = String(hours.value).padStart(2, '0');
        const m = String(minutes.value).padStart(2, '0');
        const s = String(seconds.value).padStart(2, '0');
        return hours.value > 0 ? `${h}:${m}:${s}` : `${m}:${s}`;
    });

    const isExpired = computed(() => remainingSeconds.value <= 0 && expiredAt !== null);
    const isWarning = computed(() => remainingSeconds.value > 0 && remainingSeconds.value <= 300);
    const isDanger = computed(() => remainingSeconds.value > 0 && remainingSeconds.value <= 60);
    const percentage = computed(() => {
        if (!expiredAt) return 100;
        return Math.max(0, (remainingSeconds.value / calculateRemaining()) * 100);
    });

    const start = () => {
        remainingSeconds.value = calculateRemaining();
        if (remainingSeconds.value <= 0) {
            onExpire?.();
            return;
        }

        intervalId = setInterval(() => {
            remainingSeconds.value = calculateRemaining();
            if (remainingSeconds.value <= 0) {
                stop();
                onExpire?.();
            }
        }, 1000);
    };

    const stop = () => {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
    };

    onUnmounted(stop);

    return {
        remainingSeconds,
        hours,
        minutes,
        seconds,
        formatted,
        isExpired,
        isWarning,
        isDanger,
        percentage,
        start,
        stop,
    };
}
