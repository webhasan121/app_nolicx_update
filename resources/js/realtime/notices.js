const getCookie = (name) => {
    const match = document.cookie
        .split("; ")
        .find((row) => row.startsWith(`${name}=`));

    return match ? decodeURIComponent(match.split("=").slice(1).join("=")) : "";
};

const buildSocketUrl = () => {
    const key = import.meta.env.VITE_REVERB_APP_KEY;

    if (!key) {
        return null;
    }

    const host = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
    const port = import.meta.env.VITE_REVERB_PORT || "8080";
    const scheme = import.meta.env.VITE_REVERB_SCHEME === "https" ? "wss" : "ws";

    return `${scheme}://${host}:${port}/app/${key}?protocol=7&client=nolix&version=1.0&flash=false`;
};

const authorizeChannel = async (socketId, channelName) => {
    const response = await window.axios.post("/broadcasting/auth", {
        socket_id: socketId,
        channel_name: channelName,
    }, {
        headers: {
            "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
        },
    });

    return response.data;
};

export function subscribeToNoticeChannel(role, userId, onNotice) {
    const url = buildSocketUrl();

    if (!url || !role || typeof window === "undefined" || !window.WebSocket) {
        return () => {};
    }

    let socket = null;
    let reconnectTimer = null;
    let closedByClient = false;
    const channelNames = role === "user" && userId
        ? [`private-notices.user`, `private-notices.user.${userId}`]
        : [`private-notices.${role}`];

    const connect = () => {
        socket = new WebSocket(url);

        socket.addEventListener("message", async (event) => {
            let message = {};

            try {
                message = JSON.parse(event.data);
            } catch {
                return;
            }

            if (message.event === "pusher:connection_established") {
                const payload = JSON.parse(message.data || "{}");
                await Promise.all(channelNames.map(async (channelName) => {
                    const auth = await authorizeChannel(payload.socket_id, channelName);

                    socket?.send(JSON.stringify({
                        event: "pusher:subscribe",
                        data: {
                            channel: channelName,
                            auth: auth.auth,
                        },
                    }));
                }));

                return;
            }

            if (message.event === "pusher:ping") {
                socket?.send(JSON.stringify({ event: "pusher:pong", data: {} }));
                return;
            }

            if (message.event === "notice.updated" || message.event === ".notice.updated") {
                const payload = typeof message.data === "string"
                    ? JSON.parse(message.data || "{}")
                    : message.data;

                onNotice(payload);
            }
        });

        socket.addEventListener("close", () => {
            if (!closedByClient) {
                reconnectTimer = window.setTimeout(connect, 3000);
            }
        });
    };

    connect();

    return () => {
        closedByClient = true;
        window.clearTimeout(reconnectTimer);
        socket?.close();
    };
}
