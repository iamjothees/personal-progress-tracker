interface Config {
    showSeconds: boolean
}

class Ticker {
    days: number;
    hours: number;
    minutes: number;
    seconds: number;
    config: Config;
    showSeconds: boolean;
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
        return `${this.days} Days`;
    }
    constructor(days: number = 0, hours: number = 0, minutes: number = 0, seconds: number = 0, config: Config = { showSeconds: false }) {
        this.days = days;
        this.hours = hours;
        this.minutes = minutes;
        this.seconds = seconds;
        this.config = config;
    }
    tick() {
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
    clone(): Ticker {
        const newTicker = new Ticker(this.days, this.hours, this.minutes, this.seconds, this.config);
        return newTicker;
    }
}
export default Ticker