import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import NavLink from "../../../../components/NavLink";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import OverviewDiv from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import OrderStatus from "../../../../components/dashboard/OrderStatus";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";
import useTranslation from "../../../../hooks/useTranslation";

export default function Index({ filters, stats, orders }) {
    const { t } = useTranslation();
    const [searchValue, setSearchValue] = useState(filters?.search ?? "");

    const apply = (next = {}) => {
        router.get(
            route("system.orders.index"),
            {
                date: next.date ?? filters?.date ?? "today",
                search: next.search ?? filters?.search ?? "",
                sd: next.sd ?? filters?.sd ?? "",
                ed: next.ed ?? filters?.ed ?? "",
                qf: next.qf ?? filters?.qf ?? "id",
                type: next.type ?? filters?.type ?? "",
                status: next.status ?? filters?.status ?? "",
                page: next.page ?? undefined,
            },
            { preserveScroll: true, preserveState: true }
        );
    };

    useEffect(() => {
        setSearchValue(filters?.search ?? "");
    }, [filters?.search]);

    useEffect(() => {
        const timer = window.setTimeout(() => {
            if (searchValue !== (filters?.search ?? "")) {
                apply({ search: searchValue });
            }
        }, 400);

        return () => window.clearTimeout(timer);
    }, [searchValue]);

    const print = () => {
        window.open(
            route("system.orders.sprint", {
                date: filters?.date ?? "today",
                sd: filters?.sd ?? "",
                ed: filters?.ed ?? "",
                search: filters?.search ?? "",
                type: filters?.type ?? "",
                qf: filters?.qf ?? "id",
                status: filters?.status ?? "",
            }),
            "_blank"
        );
    };

    const destroy = (id) => {
        if (!window.confirm("Are your sure want to delete ?")) {
            return;
        }

        router.delete(route("system.orders.destroy", { id }));
    };

    const pagination = useMemo(() => {
        const links = orders?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [orders?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        apply({
            date: nextUrl.searchParams.get("date") ?? filters?.date,
            search: nextUrl.searchParams.get("search") ?? filters?.search,
            sd: nextUrl.searchParams.get("sd") ?? filters?.sd,
            ed: nextUrl.searchParams.get("ed") ?? filters?.ed,
            qf: nextUrl.searchParams.get("qf") ?? filters?.qf,
            type: nextUrl.searchParams.get("type") ?? filters?.type,
            status: nextUrl.searchParams.get("status") ?? filters?.status,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const resultSummary =
        orders?.total > 0
            ? `Showing ${orders?.from ?? 0}-${orders?.to ?? 0} of ${orders?.total ?? 0} orders`
            : "No orders found";

    return (
        <AppLayout
            title={t("Orders")}
            header={
                <PageHeader>
                    <div className="flex items-center justify-between">{t("Orders")}</div>
                </PageHeader>
            }
        >
            <Head title={t("Orders")} />

            <Container>
                <OverviewSection>
                    <OverviewDiv title={t("Orders")} content={stats?.orders ?? 0} />
                    <OverviewDiv title={t("Amount")} content={`${stats?.amount ?? 0} TK`} />
                </OverviewSection>

                <Section>
                    <SectionHeader
                        title={
                            <div className="flex flex-wrap items-center gap-2">
                                <div className="flex items-center gap-2">
                                    <select className="rounded-md border border-gray-300 shadow-sm" value={filters?.qf ?? "id"} onChange={(e) => apply({ qf: e.target.value })}>
                                        <option value="id">{t("Order")}</option>
                                        <option value="user_id">{t("Buyer")}</option>
                                        <option value="belongs_to">{t("Seller")}</option>
                                    </select>
                                    <TextInput type="search" value={searchValue} onChange={(e) => setSearchValue(e.target.value)} placeholder={t("Search")} />
                                </div>

                                <select className="rounded-md border border-gray-300 shadow-sm" value={filters?.type ?? ""} onChange={(e) => apply({ type: e.target.value })}>
                                    <option value="">{t("Both (")}{stats?.orders ?? 0})</option>
                                    <option value="user">{t("U > R (")}{stats?.user_to_reseller ?? 0})</option>
                                    <option value="reseller">{t("R > V (")}{stats?.reseller_to_vendor ?? 0})</option>
                                </select>
                                <select className="rounded-md border border-gray-300 shadow-sm" value={filters?.status ?? ""} onChange={(e) => apply({ status: e.target.value })}>
                                    <option value="">{t("Any")}</option>
                                    <option value="Pending">{t("Pending")}</option>
                                    <option value="Accept">{t("Accept")}</option>
                                    <option value="Picked">{t("Picked")}</option>
                                    <option value="Delivery">{t("Delivery")}</option>
                                    <option value="Delivered">{t("Delivered")}</option>
                                    <option value="Confirm">{t("Finished")}</option>
                                    <option value="Cancel">{t("Cancel")}</option>
                                    <option value="Hold">{t("Hold")}</option>
                                    <option value="Cancelled">{t("Buyer Cancel")}</option>
                                    <option value="None">{t("None")}</option>
                                </select>

                                <select value={filters?.date ?? ""} className="rounded-md border border-gray-300 bg-white shadow-sm" onChange={(e) => apply({ date: e.target.value })}>
                                    <option value="">{t("Null")}</option>
                                    <option value="today">{t("Today")}</option>
                                    <option value="yesterday">{t("Yesterday")}</option>
                                    <option value="between">{t("Custom")}</option>
                                </select>
                                <PrimaryButton type="button" onClick={print}>
                                    <i className="fas fa-print"></i>
                                </PrimaryButton>
                            </div>
                        }
                        content={
                            filters?.date === "between" ? (
                                <div className="flex items-center gap-2 pt-2">
                                    <TextInput
                                        type="date"
                                        className="py-1"
                                        value={filters?.sd ?? ""}
                                        onChange={(e) => apply({ sd: e.target.value })}
                                        id="sd"
                                    />
                                    <TextInput
                                        type="date"
                                        className="py-1"
                                        value={filters?.ed ?? ""}
                                        onChange={(e) => apply({ ed: e.target.value })}
                                        id="ed"
                                    />
                                </div>
                            ) : null
                        }
                    />

                    <SectionInner>

                        <Table data={orders?.data ?? []}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{t("ID")}</th>
                                    <th>{t("Buyer")}</th>
                                    <th>{t("Flow")}</th>
                                    <th>{t("Seller")}</th>
                                    <th>{t("Status")}</th>
                                    <th>{t("Amount")}</th>
                                    <th>{t("Comission")}</th>
                                    <th>{t("Date")}</th>
                                    <th>{t("A/C")}</th>
                                </tr>
                            </thead>

                            <tbody>
                                {(orders?.data ?? []).map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>{item.id ?? "N/A"}</td>
                                        <td>
                                            {item.user.id ? (
                                                <NavLinkBtn href={route("system.users.edit", { id: item.user.id })}>
                                                    {item.user.name}
                                                </NavLinkBtn>
                                            ) : null}
                                            {item.user.phone}| {item.user.email}
                                        </td>
                                        <td>
                                            <div className="flex items-center">
                                                <div>
                                                    <span className="text-xs"></span>
                                                    {item.user_type}
                                                </div>
                                                <i className="px-2 fas fa-caret-right"></i>
                                                {item.belongs_to_type}
                                            </div>
                                        </td>
                                        <td>
                                            {item.seller.id ? (
                                                <NavLinkBtn href={route("system.users.edit", { id: item.seller.id })}>
                                                    {item.seller.name}
                                                </NavLinkBtn>
                                            ) : null}
                                            {item.seller.phone} | {item.seller.email}
                                        </td>
                                        <td>
                                            <OrderStatus status={item.status} />
                                        </td>
                                        <td>{item.total ?? 0}{t("TK")}</td>
                                        <td>{item.comission ?? 0}{t("TK")}</td>
                                        <td>{item.created_at_formatted}</td>
                                        <td>
                                            <div className="flex">
                                                <NavLink href={route("system.orders.details", { id: item.id })}>{t("Details")}</NavLink>
                                                <DangerButton type="button" onClick={() => destroy(item.id)}>
                                                    <i className="fas fa-trash"></i>
                                                </DangerButton>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colSpan="6">{orders?.count ?? 0}{t("Item")}</td>
                                    <td>{orders?.sum_total ?? 0}</td>
                                    <td>{orders?.sum_comission ?? 0}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
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
                                            >{t("Previous")}</button>
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
                                            >{t("Next")}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ) : null}

                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
