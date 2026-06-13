import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Container from "../../../components/dashboard/Container";
import { todayInputDate } from "../../../utils/dateInput";
import useTranslation from "../../../hooks/useTranslation";

function buildQuery(filters, updates = {}) {
    return Object.fromEntries(
        Object.entries({ ...filters, ...updates }).filter(([, value]) => value !== "" && value !== null && value !== undefined)
    );
}

function updateFilter(filters, updates) {
    router.get(route("dashboard"), buildQuery(filters, { ...updates }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function SummaryCard({ title, children }) {
    return (
        <div
            className="relative overflow-hidden rounded p-3 shadow"
            style={{ backgroundColor: "orange", color: "white", zIndex: 1 }}
        >
            <div className="mb-3 text-md">{title}</div>
            <div className="text-end text-2xl">{children}</div>
            <div className="rider-summary-card-accent"></div>
        </div>
    );
}

export default function Index({ riderConsignmentIndex }) {
    const { t } = useTranslation();
    const filters = riderConsignmentIndex?.filters ?? {};
    const consignments = riderConsignmentIndex?.consignments ?? [];
    const pagination = riderConsignmentIndex?.pagination ?? {};
    const totals = riderConsignmentIndex?.totals ?? {};
    const [search, setSearch] = useState(filters.find ?? "");
    const today = todayInputDate();

    const changeStatus = (id, status) => {
        router.post(
            route("rider.consignment.status", { consignment: id }),
            { status },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    useEffect(() => {
        const nextSearch = search.trim();
        const currentSearch = (filters.find ?? "").trim();

        if (nextSearch === currentSearch) {
            return undefined;
        }

        const timeout = setTimeout(() => {
            updateFilter(filters, { find: nextSearch, page: undefined });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    useEffect(() => {
        setSearch(filters.find ?? "");
    }, [filters.find]);


    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        updateFilter(filters, {
            find: nextUrl.searchParams.get("find") ?? filters.find,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const dateFilters = [
        ["Today", t("Today")],
        ["Yesterday", t("Yesterday")],
        ["Weak", t("This Week")],
        ["Month", t("This Month")],
        ["between", t("Date Between")],
        ["any", t("Any Time")],
    ];

    return (
        <div>
            <Container>
                <style
                    dangerouslySetInnerHTML={{
                        __html: `
                            .rider-summary-card-accent {
                                position: absolute;
                                bottom: -100px;
                                right: -100px;
                                width: 200px;
                                height: 200px;
                                border-radius: 50%;
                                background: radial-gradient(rgb(12, 165, 94), transparent);
                                z-index: -1;
                            }

                            .rider-summary-card-accent::after {
                                content: "";
                                position: absolute;
                                width: 80px;
                                height: 80px;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                border-radius: 50%;
                                background: radial-gradient(green, transparent);
                            }
                        `,
                    }}
                />

                <div className="mb-3 grid gap-5" style={{ gridTemplateColumns: "repeat(auto-fill, minmax(150px, 1fr))", maxWidth: 350 }}>
                    <SummaryCard title={t("Delivery")}>{totals.delivery ?? 0} TK</SummaryCard>
                    <SummaryCard title={t("Earn")}>{totals.earn ?? 0} TK</SummaryCard>
                </div>

                <div className="flex flex-wrap items-center justify-between gap-3">
                    <div className="flex flex-wrap items-center gap-2">
                        <select
                            value={filters.status ?? "All"}
                            onChange={(e) => updateFilter(filters, { status: e.target.value, page: undefined })}
                            className="py-1 mt-1 rounded"
                            id="select_status"
                        >
                            <option value="All"> -- {t("All")} -- </option>
                            <option value="Pending">{t("Pending")}</option>
                            <option value="Received">{t("Received")}</option>
                            <option value="Completed">{t("Delivered")}</option>
                            <option value="Returned">{t("Returned")}</option>
                        </select>

                        <input
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder={t("Search consignments...")}
                            className="w-64 rounded border-gray-300 py-1 text-sm"
                        />
                    </div>

                    <div className="flex flex-wrap items-center justify-end gap-2">
                        <select
                            value={filters.created_at ?? "Today"}
                            onChange={(e) => updateFilter(filters, { created_at: e.target.value, page: undefined })}
                            className="rounded border-gray-300 py-1 text-sm"
                        >
                            {dateFilters.map(([value, label]) => (
                                <option key={value} value={value}>
                                    {label}
                                </option>
                            ))}
                        </select>

                        {filters.created_at === "between" ? (
                            <div className="flex flex-wrap items-center gap-2">
                                <label className="flex items-center gap-1 text-xs text-gray-600">
                                    {t("From")}
                                    <input
                                        type="date"
                                        name="start_time"
                                        value={filters.start_time || today}
                                        onChange={(e) => updateFilter(filters, { start_time: e.target.value, page: undefined })}
                                        className="rounded border-gray-300 py-1 text-sm"
                                    />
                                </label>
                                <label className="flex items-center gap-1 text-xs text-gray-600">
                                    {t("To")}
                                    <input
                                        type="date"
                                        name="end_time"
                                        value={filters.end_time ?? ""}
                                        onChange={(e) => updateFilter(filters, { end_time: e.target.value, page: undefined })}
                                        className="rounded border-gray-300 py-1 text-sm"
                                    />
                                </label>
                            </div>
                        ) : null}
                    </div>
                </div>

                {consignments.length > 0 ? (
                    <>
                        <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                            {consignments.map((cod) => (
                                <div key={cod.id} className="relative flex flex-col justify-between text-center bg-white rounded shadow">
                                    <div className="py-2 bg-gray-200">
                                        <h3 className="text-xs text-gray-500">
                                            {t("Order ID")}
                                            <a
                                                href={route("rider.consignment.view", { id: cod.id })}
                                                className="inline-block px-2 text-xs text-white bg-indigo-900 shadow cursor-pointer rounded-xl"
                                            >
                                                {t("View")}
                                            </a>
                                        </h3>
                                        <div className="font-bold">{cod.order_id}</div>
                                    </div>

                                    <div className="p-2">
                                        <div className="flex items-center justify-center -space-x-2 overflow-hidden">
                                            {cod.images.map((image, index) => (
                                                <img
                                                    key={`${cod.id}-${index}`}
                                                    src={`/storage/${image}`}
                                                    className="inline-block rounded-full size-10 ring-2 ring-white outline -outline-offset-1 outline-black/5"
                                                    alt=""
                                                />
                                            ))}
                                        </div>
                                    </div>

                                    <div className="px-3 py-2">
                                        <div className="flex justify-center text-2xl font-bold ">
                                            {cod.display_total} Tk
                                        </div>
                                        <div className="text-sm text-center text-gray-500">
                                            <div>
                                                <span className="pl-1 font-bold">{cod.total_for_not_resel ?? "N/A"}</span>
                                                <span className="px-1" style={{ lineHeight: "8px" }}>+</span>
                                                <span>{cod.system_comission ?? "N/A"}</span>
                                            </div>
                                            <div className="text-xs text-red-500">
                                                {t("Commission")} {cod.system_comission ?? "N/A"}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="px-3 py-2">
                                        <p className="text-xswwww">{cod.created_at_formatted}</p>
                                        <div className="text-xs text-gray-500">
                                            <i className="pr-1 fas fa-map-marker-alt"></i>
                                            {cod.location ?? "N/A"}
                                        </div>
                                    </div>

                                    {cod.status === "Pending" ? (
                                        <>
                                            <div className="pb-2">
                                                <button
                                                    className="px-2 py-1 text-sm text-white bg-indigo-900 border rounded shadow"
                                                    onClick={() => changeStatus(cod.id, "Received")}
                                                >
                                                    {t("Mark as Received")}
                                                </button>
                                            </div>
                                            <div className="absolute p-1" style={{ top: 43, left: "50%", transform: "translatex(-50%)" }}>
                                                <div className="px-2 text-xs bg-white shadow rounded-xl"> {t("Pending")} </div>
                                            </div>
                                        </>
                                    ) : null}

                                    {cod.status === "Received" ? (
                                        <>
                                            <div className="pb-2">
                                                <button
                                                    className="px-2 py-1 text-sm text-white bg-indigo-900 border rounded shadow"
                                                    onClick={() => changeStatus(cod.id, "Completed")}
                                                >
                                                    {t("Mark as Delivered")}
                                                </button>
                                            </div>
                                            <div className="absolute p-1" style={{ top: 43, left: "50%", transform: "translatex(-50%)" }}>
                                                <div className="px-2 text-xs bg-indigo-200 shadow rounded-xl"> {t("Received")} </div>
                                            </div>
                                        </>
                                    ) : null}

                                    {cod.status === "Completed" ? (
                                        <>
                                            <p className="p-2 font-bold text-green-900 bg-green-200">
                                                <i className="fas fa-check-circle ps-2"></i> {t("Earn")} ({cod.shipping}TK)
                                            </p>
                                            <div className="absolute p-1" style={{ top: 43, left: "50%", transform: "translatex(-50%)" }}>
                                                <div className="px-2 text-xs text-white bg-green-900 shadow rounded-xl"> {t("Done")} </div>
                                            </div>
                                        </>
                                    ) : null}
                                </div>
                            ))}
                        </div>
                        {pagination?.links?.length ? (
                            <div className="mt-4 flex flex-wrap items-center justify-between gap-3">
                                <div className="text-sm text-gray-600">
                                    {t("Showing :from-:to of :total", {
                                        from: pagination.from ?? 0,
                                        to: pagination.to ?? 0,
                                        total: pagination.total ?? 0,
                                    })}
                                </div>
                                <div className="flex flex-wrap items-center gap-1">
                                    {pagination.links.map((link, index) => (
                                        <button
                                            key={`${link.label}-${index}`}
                                            type="button"
                                            disabled={!link.url || link.active}
                                            onClick={() => goToPage(link.url)}
                                            className={`min-w-9 rounded border px-3 py-1 text-sm ${
                                                link.active
                                                    ? "border-orange-500 bg-orange-500 text-white"
                                                    : "border-gray-300 bg-white text-gray-700"
                                            } disabled:cursor-not-allowed disabled:opacity-50`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ))}
                                </div>
                            </div>
                        ) : null}
                    </>
                ) : (
                    <p className="p-1 bg-gray-50">{t("No Consignment Found !")}</p>
                )}
            </Container>
        </div>
    );
}
