import { Link, router } from "@inertiajs/react";
import axios from "axios";
import { useEffect, useState } from "react";
import useTranslation from "../hooks/useTranslation";

export default function NoticeBell({ className = "" }) {
    const { t } = useTranslation();
    const [open, setOpen] = useState(false);
    const [notices, setNotices] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [todayCount, setTodayCount] = useState(0);

    const loadFeed = async () => {
        const response = await axios.get(route("dashboard.notices.feed"));

        const data = response.data ?? {};

        setNotices(data.notices ?? []);
        setUnreadCount(Number(data.unread_count || 0));
        setTodayCount(Number(data.today_count || 0));
    };

    useEffect(() => {
        loadFeed().catch(() => {});

        const interval = window.setInterval(() => {
            loadFeed().catch(() => {});
        }, 5000);

        return () => window.clearInterval(interval);
    }, []);

    const markRead = async (notice) => {
        if (!notice.is_read) {
            setUnreadCount((count) => Math.max(count - 1, 0));
            setNotices((items) =>
                items.map((item) =>
                    item.id === notice.id ? { ...item, is_read: true } : item
                )
            );
            await axios.post(route("dashboard.notices.read", { notice: notice.id }));
        }

        router.visit(notice.link_url || route("dashboard.notices.index"));
    };

    const markAllRead = async () => {
        setUnreadCount(0);
        setNotices((items) => items.map((item) => ({ ...item, is_read: true })));
        await axios.post(route("dashboard.notices.read-all"));
    };


    return (
        <div className={`relative ${className}`}>
            <button
                type="button"
                onClick={() => setOpen((value) => !value)}
                className="relative inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50"
                title={t("Notice")}
            >
                <i className="fas fa-bell"></i>
                {unreadCount > 0 ? (
                    <span className="absolute -right-1 -top-1 min-w-5 rounded-full bg-red-600 px-1 text-center text-xs font-bold text-white">
                        {unreadCount > 99 ? "99+" : unreadCount}
                    </span>
                ) : null}
            </button>

            {open ? (
                <div className="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-md border border-slate-200 bg-white shadow-lg">
                    <div className="flex items-center justify-between border-b px-4 py-2">
                        <div>
                            <div className="font-semibold text-slate-800">{t("Notice")}</div>
                            <div className="text-[11px] font-medium text-slate-500">
                                {t("Today")}: {todayCount}
                                {unreadCount > 0 ? ` | ${t("New")}: ${unreadCount}` : ""}
                            </div>
                        </div>
                        <div className="flex items-center gap-3">
                            {unreadCount > 0 ? (
                                <button
                                    type="button"
                                    onClick={markAllRead}
                                    className="text-xs font-semibold text-slate-600 hover:text-slate-900"
                                >
                                    {t("Mark all read")}
                                </button>
                            ) : null}
                            <Link
                                href={route("dashboard.notices.index")}
                                className="text-xs font-semibold text-orange-600"
                            >
                                {t("View All")}
                            </Link>
                        </div>
                    </div>

                    <div className="max-h-80 overflow-y-auto">
                        {notices.length ? (
                            notices.map((notice) => (
                                <button
                                    type="button"
                                    key={notice.id}
                                    onClick={() => markRead(notice)}
                                    className={`block w-full border-b px-4 py-3 text-left hover:bg-slate-50 ${
                                        notice.is_read ? "bg-white" : "bg-orange-50/60"
                                    }`}
                                >
                                    <div className="flex items-start gap-2">
                                        {!notice.is_read ? (
                                            <span className="mt-1 h-2 w-2 shrink-0 rounded-full bg-red-600"></span>
                                        ) : null}
                                        <div className="min-w-0">
                                            <div className="text-sm font-semibold text-slate-900">
                                                {notice.title}
                                            </div>
                                            <div className="mt-1 line-clamp-2 text-xs text-slate-600">
                                                {notice.body}
                                            </div>
                                            <div className="mt-1 text-[11px] text-slate-400">
                                                {notice.created_at_formatted}
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            ))
                        ) : (
                            <div className="px-4 py-6 text-center text-sm text-slate-500">
                                {t("No notice found.")}
                            </div>
                        )}
                    </div>
                </div>
            ) : null}
        </div>
    );
}
