import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import Container from "../../components/dashboard/Container";
import OrderStatus from "../../components/dashboard/OrderStatus";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionSection from "../../components/dashboard/section/Section";
import UserDash from "../../components/user/dash/UserDash";
import Table from "../../components/dashboard/table/Table";
import PrimaryButton from "../../components/PrimaryButton";
import TextInput from "../../components/TextInput";
import { ActionIconButton, ActionIconLink } from "../../components/ActionIcon";
import useTranslation from "../../hooks/useTranslation";

export default function Orders() {
    const { t } = useTranslation();
    const { orders = {}, nav, filters = {}, printUrl } = usePage().props;
    const rows = orders.data ?? [];
    const [search, setSearch] = useState(filters.find ?? "");

    useEffect(() => {
        setSearch(filters.find ?? "");
    }, [filters.find]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.find ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            router.get(
                route("user.orders.view"),
                { find: trimmedSearch },
                {
                    preserveScroll: true,
                    preserveState: true,
                    replace: true,
                    only: ["filters", "orders", "nav", "printUrl"],
                }
            );
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const remove = (id) => {
        if (confirm("Are you sure?")) {
            router.delete(route("user.orders.delete", id));
        }
    };

    const cancelOrder = (id) => {
        router.patch(route("user.orders.cancel", id));
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        router.get(
            route("user.orders.view"),
            {
                find: nextUrl.searchParams.get("find") ?? search,
                page: nextUrl.searchParams.get("page") ?? undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                only: ["filters", "orders", "nav", "printUrl"],
            }
        );
    };

    const pagination = useMemo(() => {
        const links = orders?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [orders?.links]);

    const resultSummary =
        orders?.total > 0
            ? `Showing ${orders?.from ?? 0}-${orders?.to ?? 0} of ${orders?.total ?? 0} orders`
            : t("No orders found");

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader title={t("Your Orders")} />
                </SectionSection>
                <SectionSection>
                    <div>
                        <SectionHeader
                            title=""
                            content={
                                <div className="mb-2 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
                                    <TextInput
                                        type="search"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key !== "Enter") {
                                                return;
                                            }

                                            e.preventDefault();
                                            router.get(
                                                route("user.orders.view"),
                                                { find: search.trim() },
                                                {
                                                    preserveScroll: true,
                                                    preserveState: true,
                                                    replace: true,
                                                }
                                            );
                                        }}
                                        className="w-full py-1 sm:w-auto"
                                        placeholder={t("Search orders...")}
                                    />
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                        className="w-full sm:w-auto"
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                </div>
                            }
                        />
                        <Table data={rows} emptyMessage={t("Data Not Found")}>
                            <thead>
                                <tr>
                                    <th>{t("ID")}</th>
                                    <th>{t("Status")}</th>
                                    <th>{t("Product")}</th>
                                    <th>{t("Total")}</th>
                                    <th>{t("Shop")}</th>
                                    <th>{t("A/C")}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.id}</td>

                                        <td>
                                            <OrderStatus status={item.status} />
                                        </td>

                                        <td>
                                            {item.cart_orders_count ?? "N/A"} |{" "}
                                            {item.quantity ?? "N/A"}
                                        </td>

                                        <td>{item.total ?? "N/A"} TK</td>

                                        <td>
                                            {item?.shop?.shop_name_en}
                                            <i className="px-1 fas fa-caret-right"></i>
                                            {item.shop?.shop_name_bn}

                                            <br />

                                            <div className="text-xs">
                                                {item.shop?.village ?? "n/a"},{" "}
                                                {item.shop?.upozila ?? "n/a"},{" "}
                                                {item.shop?.district ?? "n/a"}
                                            </div>
                                        </td>

                                        <td>
                                            <div className="flex items-center gap-2">
                                                <ActionIconLink
                                                    href={route("user.orders.details", {
                                                        id: item.id,
                                                    })}
                                                    action="details"
                                                    title="Details"
                                                />
                                                <ActionIconButton
                                                    action="cancel"
                                                    title="Cancel"
                                                    onClick={() => cancelOrder(item.id)}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>

                        {pagination.pages.length ? (
                            <div className="w-full pt-4">
                                <div className="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div className="text-sm leading-6 text-slate-700">
                                        {resultSummary}
                                    </div>
                                    <div className="flex w-full justify-center sm:w-auto sm:justify-end">
                                        <div className="overflow-hidden bg-white border shadow-sm rounded-xl border-slate-200">
                                            <button
                                                type="button"
                                                disabled={!pagination.prev?.url}
                                                className="px-3 py-2 text-xs transition border-r border-slate-200 text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400 sm:px-4 sm:text-sm"
                                                onClick={() => goToPage(pagination.prev?.url)}
                                            >
                                                {t("Previous")}
                                            </button>
                                            {pagination.pages.map((link, index) => (
                                                <button
                                                    key={`${link.label}-${index}`}
                                                    type="button"
                                                    disabled={!link.url}
                                                    className={`min-w-8 border-r border-slate-200 px-3 py-2 text-xs font-semibold transition sm:min-w-10 sm:px-4 sm:text-sm ${
                                                        link.active
                                                            ? "bg-slate-100 text-blue-600"
                                                            : "bg-white text-slate-700 hover:bg-slate-50"
                                                    } disabled:cursor-not-allowed disabled:opacity-50`}
                                                    onClick={() => goToPage(link.url)}
                                                >
                                                    {link.label}
                                                </button>
                                            ))}
                                            <button
                                                type="button"
                                                disabled={!pagination.next?.url}
                                                className="px-3 py-2 text-xs transition text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400 sm:px-4 sm:text-sm"
                                                onClick={() => goToPage(pagination.next?.url)}
                                            >
                                                {t("Next")}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ) : null}
                    </div>
                </SectionSection>
            </Container>
        </UserDash>
    );
}

{
    /* <OrderStatus status={order.status} /> */
}
