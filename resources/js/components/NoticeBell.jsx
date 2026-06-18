import { Link, router, usePage } from "@inertiajs/react";
import axios from "axios";
import { useEffect, useRef, useState } from "react";
import useTranslation from "../hooks/useTranslation";
import { subscribeToNoticeChannel } from "../realtime/notices";

export default function NoticeBell({ className = "", role = null }) {
    const { t } = useTranslation();
    const { auth } = usePage().props;
    const [open, setOpen] = useState(false);
    const [notices, setNotices] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [todayCount, setTodayCount] = useState(0);
    const wrapperRef = useRef(null);
    const user = auth?.user;
    const roleNames = user?.roles?.map((item) => item.name) ?? [];
    const noticeRole = role
        || (["system", "vendor", "reseller", "rider"].includes(user?.active_nav) ? user.active_nav : null)
        || (roleNames.includes("system") || roleNames.includes("admin") ? "system" : null)
        || (roleNames.find((item) => ["vendor", "reseller", "rider", "user"].includes(item)) ?? "user");
    const noticeParams = noticeRole
        ? { role: noticeRole, ...(noticeRole === "user" ? { personal: 1 } : {}) }
        : {};

    const loadFeed = async () => {
        const response = await axios.get(route("dashboard.notices.feed"), {
            params: noticeParams,
        });

        const data = response.data ?? {};

        setNotices(data.notices ?? []);
        setUnreadCount(Number(data.unread_count || 0));
        setTodayCount(Number(data.today_count || 0));
    };

    useEffect(() => {
        loadFeed().catch(() => {});

        const interval = window.setInterval(() => {
            loadFeed().catch(() => {});
        }, 3000);

        const handleFocus = () => {
            loadFeed().catch(() => {});
        };

        const handleVisibilityChange = () => {
            if (!document.hidden) {
                loadFeed().catch(() => {});
            }
        };

        window.addEventListener("focus", handleFocus);
        document.addEventListener("visibilitychange", handleVisibilityChange);

        return () => {
            window.clearInterval(interval);
            window.removeEventListener("focus", handleFocus);
            document.removeEventListener("visibilitychange", handleVisibilityChange);
        };
    }, [noticeRole, user?.id]);

    useEffect(() => {
        return subscribeToNoticeChannel(noticeRole, user?.id, (payload) => {
            const notice = payload?.notice;

            if (!notice?.id) {
                return;
            }

            const noticeRoles = Array.isArray(notice.target_roles) ? notice.target_roles : [];
            const isRoleWideNotice = !notice.order_id && (!noticeRoles.length || noticeRoles.includes(noticeRole));

            if (noticeRole === "user" && notice.target_user_id && Number(notice.target_user_id) !== Number(user?.id)) {
                return;
            }

            if (
                noticeRole === "rider" &&
                Array.isArray(notice.rider_ids) &&
                notice.rider_ids.length > 0 &&
                !notice.rider_ids.map(Number).includes(Number(user?.id))
            ) {
                return;
            }

            if (noticeRole === "vendor") {
                const isDirectVendorOrder = Number(notice.seller_id) === Number(user?.id) && notice.seller_type === "vendor";
                const isResoldVendorProduct = Array.isArray(notice.vendor_ids) && notice.vendor_ids.map(Number).includes(Number(user?.id));

                if (!isRoleWideNotice && !isDirectVendorOrder && !isResoldVendorProduct) {
                    return;
                }
            }

            if (
                noticeRole === "reseller" &&
                !isRoleWideNotice &&
                (Number(notice.seller_id) !== Number(user?.id) || notice.seller_type !== "reseller")
            ) {
                return;
            }

            if (payload.action === "deleted") {
                setNotices((items) => items.filter((item) => item.id !== notice.id));
                return;
            }

            const linkUrl = notice.link_urls?.[noticeRole] ?? null;
            const nextNotice = {
                ...notice,
                link_url: linkUrl,
                is_read: false,
            };

            setNotices((items) => {
                const remaining = items.filter((item) => item.id !== nextNotice.id);
                return [nextNotice, ...remaining].slice(0, 5);
            });

            if (payload.action === "created") {
                setUnreadCount((count) => count + 1);
                setTodayCount((count) => count + 1);
            }
        });
    }, [noticeRole, user?.id]);

    useEffect(() => {
        if (!open) {
            return undefined;
        }

        const handlePointerDown = (event) => {
            if (!wrapperRef.current?.contains(event.target)) {
                setOpen(false);
            }
        };

        const handleKeyDown = (event) => {
            if (event.key === "Escape") {
                setOpen(false);
            }
        };

        document.addEventListener("mousedown", handlePointerDown);
        document.addEventListener("touchstart", handlePointerDown);
        document.addEventListener("keydown", handleKeyDown);

        return () => {
            document.removeEventListener("mousedown", handlePointerDown);
            document.removeEventListener("touchstart", handlePointerDown);
            document.removeEventListener("keydown", handleKeyDown);
        };
    }, [open]);

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

        router.visit(notice.link_url || route("dashboard.notices.index", noticeParams));
    };

    const markAllRead = async () => {
        setUnreadCount(0);
        setNotices((items) => items.map((item) => ({ ...item, is_read: true })));
        await axios.post(route("dashboard.notices.read-all"), null, {
            params: noticeParams,
        });
    };


    return (
        <div ref={wrapperRef} className={`relative ${className}`}>
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
                                href={route("dashboard.notices.index", noticeParams)}
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
