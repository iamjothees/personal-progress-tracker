import api from "@/core/http/api"
import TimerModel from "../models/timer.model";

export const start = async (): Promise<TimerModel> => {
    const timerJson = (await api.post("/timers/actions/start")).data.timer;
    return TimerModel.fromJson(timerJson);
}

export const pause = async ({timer}: {timer: TimerModel}): Promise<TimerModel> => {
    const reqData = { latest_activity_id: timer.latestActivity?.id };
    const timerJson = (await api.post(`/timers/${timer.id}/actions/pause`, reqData)).data.timer;
    return TimerModel.fromJson(timerJson);
}

export const resume = async ({timer}: {timer: TimerModel}): Promise<TimerModel> => {
    const timerJson = (await api.post(`/timers/${timer.id}/actions/${timer.latestActivity.id}/resume`)).data.timer;
    return TimerModel.fromJson(timerJson);
}

export const stop = async ({timer}: {timer: TimerModel}): Promise<TimerModel> => {
    const timerJson = (await api.post(`/timers/${timer.id}/actions/stop`)).data.timer;
    return TimerModel.fromJson(timerJson);
}

export const reset = async ({timer}: {timer: TimerModel}): Promise<null> => {
    await api.post(`/timers/${timer.id}/actions/reset`);
    return null;
}