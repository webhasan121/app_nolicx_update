export const MAX_PRODUCT_VIDEO_SECONDS = 15;

export const PRODUCT_VIDEO_DURATION_ERROR = `Product video must be ${MAX_PRODUCT_VIDEO_SECONDS} seconds or less.`;

export function validateProductVideoDuration(file) {
    return new Promise((resolve, reject) => {
        if (!(file instanceof File)) {
            resolve();
            return;
        }

        const video = document.createElement("video");
        const url = URL.createObjectURL(file);

        const cleanup = () => {
            URL.revokeObjectURL(url);
            video.removeAttribute("src");
            video.load();
        };

        video.preload = "metadata";
        video.onloadedmetadata = () => {
            const duration = Number(video.duration);
            cleanup();

            if (!Number.isFinite(duration)) {
                reject(new Error("Unable to read the video duration."));
                return;
            }

            if (duration > MAX_PRODUCT_VIDEO_SECONDS) {
                reject(new Error(PRODUCT_VIDEO_DURATION_ERROR));
                return;
            }

            resolve();
        };
        video.onerror = () => {
            cleanup();
            reject(new Error("Unable to read the video duration."));
        };
        video.src = url;
    });
}
