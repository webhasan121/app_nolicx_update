import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import Container from "../../components/dashboard/Container";
import OrderStatus from "../../components/dashboard/OrderStatus";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionSection from "../../components/dashboard/section/Section";
import UserDash from "../../components/user/dash/UserDash";
import Table from "../../components/dashboard/table/Table";
import NavLink from "../../components/NavLink";
import PrimaryButton from "../../components/PrimaryButton";
import SecondaryButton from "../../components/SecondaryButton";
import TextInput from "../../components/TextInput";

export default function Orders() {
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
            : "No orders found";

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader title="Your Orders" />
                </SectionSection>
                <SectionSection>
                    <div>
                        <SectionHeader
                            title=""
                            content={
                                <div className="flex flex-wrap items-center justify-end gap-2">
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
                                        className="py-1"
                                        placeholder="Search orders..."
                                    />
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                </div>
                            }
                        />
                        <Table data={rows}>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Status</th>
                                    <th>Product</th>
                                    <th>Total</th>
                                    <th>Shop</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((item) => (
                                    <tr key={item.id}>
                                        <td>
                                            <NavLink
                                                href={route("user.orders.details", {
                                                    id: item.id,
                                                })}
                                            >
                                                View
                                            </NavLink>
                                        </td>

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
                                            <SecondaryButton
                                                onClick={() => cancelOrder(item.id)}
                                            >
                                                cancel
                                            </SecondaryButton>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>

                        {pagination.pages.length ? (
                            <div className="w-full pt-4">
                                <div className="flex w-full items-center justify-between gap-3">
                                    <div className="text-sm text-slate-700">
                                        {resultSummary}
                                    </div>
                                    <div className="flex items-center md:justify-end">
                                        <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                            <button
                                                type="button"
                                                disabled={!pagination.prev?.url}
                                                className="border-r border-slate-200 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pagination.prev?.url)}
                                            >
                                                Previous
                                            </button>
                                            {pagination.pages.map((link, index) => (
                                                <button
                                                    key={`${link.label}-${index}`}
                                                    type="button"
                                                    disabled={!link.url}
                                                    className={`min-w-10 border-r border-slate-200 px-4 py-2 text-sm font-semibold transition ${
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
                                                className="px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pagination.next?.url)}
                                            >
                                                Next
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
