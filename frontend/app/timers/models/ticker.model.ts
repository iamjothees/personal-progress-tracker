import TimerModel from "./timer.model";

interface Config {
    showSeconds: boolean
}

interface Props {
    days?: number;
    hours?: number;
    minutes?: number;
    seconds?: number;
    completed?: boolean | null;
    config?: Config;
}

class TickerModel {
    days: number;
    hours: number;
    minutes: number;
    seconds: number;
    config: Config;
    completed?: boolean | null = null;
    running: boolean = false;

    get secondsElapsed(): number{
        return (this.days * 86400) + (this.hours * 3600) + (this.minutes * 60) + this.seconds;
    };

    get formattedTime(): string {
        let time = `${`${this.hours}`.padStart(2, "0")}:${`${this.minutes}`.padStart(2, "0")}`;
        
        if (this.config.showSeconds === true) { 
            time = `${time}:${this.formattedSeconds}`;
        }
        return time;
    }

    get formattedSeconds(): string {
        return `${this.seconds}`.padStart(2, "0");
    }

    get formattedDays(): string {
        return `${this.days}`;
    }

    get started(): boolean {
        return this.days > 0 || this.hours > 0 || this.minutes > 0 || this.seconds > 0;
    }

    constructor(
        {days = 0, hours = 0, minutes = 0, seconds = 0, completed = null, config = { showSeconds: false }}: Props = {}
    ) {
        this.days = days;
        this.hours = hours;
        this.minutes = minutes;
        this.seconds = seconds;
        this.completed = completed;
        this.config = config;
    }

    setFromTotalSeconds(total: number): void {
        this.days = Math.floor(total / 86400);
        total %= 86400;

        this.hours = Math.floor(total / 3600);
        total %= 3600;

        this.minutes = Math.floor(total / 60);
        this.seconds = total % 60;
    }

    static fromTimer(timer: TimerModel): TickerModel {
        const { days, hours, minutes, seconds } = timer.duration;
        const isCompleted = !!timer.stoppedAt;
        
        const newTicker = new TickerModel({
            days,
            hours,
            minutes,
            seconds,
            completed: isCompleted,
            config: { showSeconds: false }
        });
        newTicker.running = timer.running;
        return newTicker;
    }

    complete(): TickerModel {
        this.completed = true;
        this.running = false;
        return this;
    }

    clone(): TickerModel {
        const newTicker = new TickerModel({
            days: this.days, 
            hours: this.hours, 
            minutes: this.minutes, 
            seconds: this.seconds, 
            completed: this.completed,
            config: {...this.config}
        });
        newTicker.running = this.running;
        return newTicker;
    }
}
export default TickerModel;