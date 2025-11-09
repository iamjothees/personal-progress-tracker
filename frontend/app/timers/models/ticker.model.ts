interface Config {
    showSeconds: boolean
}

interface Props {
    days?: number;
    hours?: number;
    minutes?: number;
    seconds?: number;
    completed?: boolean;
    config?: Config;
}

class Ticker {
    days: number;
    hours: number;
    minutes: number;
    seconds: number;
    config: Config;
    showSeconds: boolean;
    completed?: boolean = null;
    get formattedTime(): string {
        let time = `${`${this.hours}`.padStart(2, "0")}:${`${this.minutes}`.padStart(2, "0")}`;
        if (this.config.showSeconds) {
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
    tick(): Ticker {
        ++this.seconds;
        if (this.seconds === 60) {
            ++this.minutes;
            this.seconds = 0;
        }
        if (this.minutes === 60) {
            ++this.hours;
            this.minutes = 0;
        }
        if (this.hours === 24) {
            ++this.days;
            this.hours = 0;
        }
        return this;
    }
    complete(): Ticker {
        this.completed = true;
        return this;
    }
    clone(): Ticker {
        const newTicker = new Ticker({
            days: this.days, hours: this.hours, minutes: this.minutes, seconds: this.seconds, 
            completed: this.completed,
            config: {...this.config}
        });
        return newTicker;
    }
}
export default Ticker