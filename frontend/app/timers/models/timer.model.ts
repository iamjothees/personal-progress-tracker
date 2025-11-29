import { UserModel } from '@/users/user.model';
import TickerModel from './ticker.model';
import dayjs, { Dayjs } from 'dayjs';
import { Duration as DayjsDuration } from 'dayjs/plugin/duration';

interface ConstructorParams {
    id: string;
    startedAt: Dayjs;
    stoppedAt: Dayjs|null;
    elapsedSeconds: number;
    running: boolean;
    activities: TimerActivityModel[];
    latestActivity: TimerActivityModel | null;
    owner: UserModel;
}

interface TimerActivityConstructorParams {
    id: string;
    timer?: TimerModel;
    pausedAt: Dayjs;
    resumedAt: Dayjs | null;
}

interface Duration {
    days: number;
    hours: number;
    minutes: number;
    seconds: number;
}

class TimerActivityModel {
    id: string;
    timer?: TimerModel;
    pausedAt: Dayjs;
    resumedAt: Dayjs | null;

    constructor({id, timer, pausedAt, resumedAt}: TimerActivityConstructorParams) {
        this.id = id;
        this.timer = timer;
        this.pausedAt = pausedAt;
        this.resumedAt = resumedAt;
    }

    static fromJson(json: any): TimerActivityModel {
        if (!json.id) {
            throw new Error("Timer Activity ID is required");
        }

        if (!json.paused_at) {
            throw new Error("Paused At is required");
        }

        return new TimerActivityModel({
            id: json.id,
            pausedAt: dayjs(json.paused_at),
            resumedAt: json.resumed_at ? dayjs(json.resumed_at) : null,
        });
    }
}

class TimerModel {
    id: string;
    startedAt: Dayjs;
    stoppedAt: Dayjs|null = null;
    elapsedSeconds: number;
    running: boolean = false;
    activities: TimerActivityModel[];
    latestActivity: TimerActivityModel | null;
    owner: UserModel;
    ticker: TickerModel;

    _isRunning: boolean;
    _duration: DayjsDuration;

    constructor(
        {id, startedAt, stoppedAt, elapsedSeconds, running, activities, latestActivity, owner}: ConstructorParams
    ) {

        if (dayjs(startedAt).isValid() === false) {
            throw new Error("Invalid Started At");
        }

        if (stoppedAt){
            if (dayjs(stoppedAt).isValid() === false) {
                throw new Error("Invalid Stopped At");
            }
            if (dayjs(startedAt).isAfter(dayjs(stoppedAt))) {
                throw new Error("Started At is after Stopped At");
            }
        }

        if (elapsedSeconds < 0) {
            throw new Error("Elapsed Seconds cannot be negative");
        }

        this.id = id;
        this.startedAt = startedAt;
        this.stoppedAt = stoppedAt;
        this.elapsedSeconds = elapsedSeconds;
        this.running = running;
        this.activities = activities;
        this.latestActivity = latestActivity;
        this.owner = owner;

        if (this.latestActivity){
            this.latestActivity.timer = this;
        }

        this._isRunning = this.findIsRunning();
        this._duration = dayjs.duration(this.elapsedSeconds, "seconds");

        this.ticker = new TickerModel({ ...this.duration, completed: Boolean(this.stoppedAt) });
    }

    get isRunning(): boolean {
        return this._isRunning;
    }

    get formattedElapsedTime(): string {
        return this._duration.format('D [days], H [hours], m [minutes], s [seconds]');
    }

    get duration(): Duration {
        return {
            days: this._duration.days(),
            hours: this._duration.hours(),
            minutes: this._duration.minutes(),
            seconds: this._duration.seconds(),
        }
    }

    findIsRunning(): boolean {
        if (this.stoppedAt) {
            return false;
        }

        if (this.latestActivity && (this.latestActivity.resumedAt === null)) {
            return false;
        }

        return true;
    }

    static fromJson(json: any): TimerModel {
        if (!json.id) {
            throw new Error("Timer ID is required");
        }

        if (!json.started_at) {
            throw new Error("Started At is required");
        }

        if (Number.isInteger(json.elapsed_seconds) === false) {
            throw new Error("Elapsed Seconds is invalid");
        }

        return new TimerModel({
            id: json.id,
            startedAt: dayjs(json.started_at),
            stoppedAt: json.stopped_at ? dayjs(json.stopped_at) : null,
            elapsedSeconds: json.elapsed_seconds,
            running: Boolean(json.running),
            activities: json.activities.map((activity: any) => TimerActivityModel.fromJson(activity)),
            latestActivity: json.latest_activity ? TimerActivityModel.fromJson(json.latest_activity) : null,
            owner: UserModel.fromJson(json.owner),
        });
    }
}

export default TimerModel