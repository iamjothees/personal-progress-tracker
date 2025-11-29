import TickerModel from "@/timers/models/ticker.model";

interface EngineConfig {
    onTick?: (model: TickerModel) => void;
}

export default class TickerEngine {
    private model: TickerModel;
    private timerId: any = null;
    private startTime: number = 0;
    private segmentStartTime: number = 0; // Track start of current active segment
    private accumulatedSeconds: number = 0;
    private pauseStartTime: number = 0; // Track when pause started
    private onTick?: (model: TickerModel) => void;

    constructor(model: TickerModel, config: EngineConfig = {}) {
        this.model = model;
        this.onTick = config.onTick;
    }

    /**
     * Updates the internal model reference (used when API refreshes data)
     * Only syncs accumulated seconds when not actively running to avoid conflicts
     */
    updateModel(newModel: TickerModel) {
        this.model = newModel;
        // Only sync accumulated seconds if we're not currently ticking
        // This prevents UI updates from overwriting our precise time tracking
        if (!this.timerId) {
            this.accumulatedSeconds = this.model.secondsElapsed;
        }
    }

    start() {
        if (this.model.completed) throw new Error('Cannot start a completed ticker');
        if (this.timerId) return;

        const now = Date.now();
        this.startTime = now;
        this.segmentStartTime = now; // Start tracking this segment
        this.accumulatedSeconds = this.model.secondsElapsed; // Resume from current state

        this.model.running = true;

        // Immediate tick to update UI state
        if (this.onTick) this.onTick(this.model);

        this.timerId = setInterval(() => {
            const now = Date.now();
            const currentSegment = Math.floor((now - this.startTime) / 1000);
            const totalSeconds = this.accumulatedSeconds + currentSegment;

            this.model.setFromTotalSeconds(totalSeconds);

            if (this.onTick) this.onTick(this.model); // Notify UI
        }, 100);
    }

    pause() {
        if (this.model.completed && !this.timerId) return; // Allow pausing if just completing
        if (!this.timerId) return;

        this.model.running = false;
        clearInterval(this.timerId);
        this.timerId = null;

        const now = Date.now();
        this.accumulatedSeconds += Math.floor((now - this.startTime) / 1000);
        this.pauseStartTime = now; // Track when pause started

        if (this.onTick) this.onTick(this.model);
    }

    reset() {
        this.pause();
        this.accumulatedSeconds = 0;
        this.segmentStartTime = 0;
        this.pauseStartTime = 0;
        this.model.setFromTotalSeconds(0);
        this.model.running = false;
        this.model.completed = false;
        if (this.onTick) this.onTick(this.model);
    }

    adjustTime(seconds: number) {
        this.accumulatedSeconds += seconds;
        this.model.setFromTotalSeconds(this.accumulatedSeconds);
        if (this.onTick) this.onTick(this.model);
    }

    /**
     * Get seconds since the last action (start or resume)
     * Used for PAUSE action - tells how long this active segment has been
     */
    getSecondsSinceLastAction(): number {
        if (this.timerId) {
            // If running, calculate time since segment started
            const now = Date.now();
            return Math.floor((now - this.segmentStartTime) / 1000);
        }
        return 0; // Not running
    }

    /**
     * Get the pause duration (time since pause started)
     * Used for RESUME action - tells how long the pause has been
     */
    getPauseDuration(): number {
        if (!this.timerId && this.pauseStartTime > 0) {
            // If paused, calculate time since pause started
            const now = Date.now();
            return Math.floor((now - this.pauseStartTime) / 1000);
        }
        return 0; // Not paused
    }

    /**
     * Get the total accumulated seconds (total active time from start)
     * Used for STOP action - tells total active time excluding breaks
     */
    getTotalAccumulatedSeconds(): number {
        if (this.timerId) {
            // If running, calculate current total
            const now = Date.now();
            const currentSegment = Math.floor((now - this.startTime) / 1000);
            return this.accumulatedSeconds + currentSegment;
        }
        return this.accumulatedSeconds;
    }
}