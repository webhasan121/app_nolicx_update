import Pusher from "pusher-js";

export function subscribeToNoticeChannel(role, userId, onNotice) {
    const key = import.meta.env.VITE_PUSHER_APP_KEY;
    const cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

    if (!key || !cluster || !role || typeof window === "undefined") {
        return () => {};
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const pusher = new Pusher(key, {
        cluster,
        forceTLS: true,
        channelAuthorization: {
            endpoint: "/broadcasting/auth",
            transport: "ajax",
            headers: csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {},
        },
    });
    const channelNames = role === "user" && userId
        ? ["private-notices.user", `private-notices.user.${userId}`]
        : [`private-notices.${role}`];

    channelNames.forEach((channelName) => {
        pusher.subscribe(channelName).bind("notice.updated", onNotice);
    });

    return () => {
        channelNames.forEach((channelName) => pusher.unsubscribe(channelName));
        pusher.disconnect();
    };
}
