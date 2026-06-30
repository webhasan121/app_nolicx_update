import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import Table from "../../../../components/dashboard/table/Table";
import useTranslation from "../../../../hooks/useTranslation";
import { todayInputDate } from "../../../../utils/dateInput";
import { ActionIconButton } from "../../../../components/ActionIcon";
import { formatCurrency } from "../../../../utils/formatAmount";

function buildParams(status, find, sdate, edate, page) {
    const params = { status, find, sdate, edate };

    if (page) {
        params.page = page;
    }

    return params;
}

export default function Index({
    status = "*",
    find = "",
    sdate = "",
    edate = "",
    history,
}) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(find ?? "");
    const today = todayInputDate();

    const visit = (nextStatus, nextFind, nextSdate, nextEdate, page = null) => {
        router.get(
            route("system.deposit.index"),
            buildParams(nextStatus, nextFind, nextSdate, nextEdate, page),
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                only: ["status", "find", "sdate", "edate", "history"],
            },
        );
    };

    const print = () => {
        window.open(
            route("system.deposit.print-summery", {
                status,
                find: search.trim(),
                sdate,
                edate,
            }),
            "_blank",
        );
    };

    const confirmDeposit = (id) => {
        router.post(route("system.deposit.confirm", { deposit: id }));
    };

    const denyDeposit = (id) => {
        if (!window.confirm("Are you sure you want to delete this deposit?")) {
            return;
        }

        router.delete(route("system.deposit.destroy", { deposit: id }));
    };

    useEffect(() => {
        setSearch(find ?? "");
    }, [find]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (find ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            visit(status, trimmedSearch, sdate, edate);
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        visit(
            nextUrl.searchParams.get("status") ?? status,
            nextUrl.searchParams.get("find") ?? search,
            nextUrl.searchParams.get("sdate") ?? sdate,
            nextUrl.searchParams.get("edate") ?? edate,
            nextUrl.searchParams.get("page") ?? undefined,
        );
    };

    const pagination = useMemo(() => {
        const links = history?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [history?.links]);

    const resultSummary =
        history?.total > 0
            ? `Showing ${history?.from ?? 0}-${history?.to ?? 0} of ${history?.total ?? 0} deposits`
            : "No deposits found";
    const hasActiveFilters = Boolean(search.trim() || sdate || edate || status !== "*");

    return (
        <AppLayout title={t("Deposit")} header={<PageHeader>{t("Deposit")}</PageHeader>}>
            <Head title={t("Deposit")} />

            <Container>
                <Section>
                    <SectionHeader
                        title=""
                        content={
                            <div className="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                                <div className="flex w-full flex-col gap-2 py-1 sm:flex-row sm:flex-wrap sm:items-center xl:w-auto">
                                    <select
                                        value={status}
                                        onChange={(e) =>
                                            visit(e.target.value, search.trim(), sdate, edate)
                                        }
                                        className="mb-1 h-10 w-full rounded-md border border-gray-300 py-1 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:w-auto"
                                    >
                                        <option value="*">{t("All")}</option>
                                        <option value="0">{t("Pending")}</option>
                                        <option value="1">{t("Confirmed")}</option>
                                    </select>
                                    <TextInput
                                        type="date"
                                        id="sdate"
                                        value={sdate || today}
                                        onChange={(e) =>
                                            visit(status, search.trim(), e.target.value, edate)
                                        }
                                        className="h-10 w-full py-1 sm:w-auto"
                                    />
                                    <TextInput
                                        type="date"
                                        id="edate"
                                        value={edate}
                                        onChange={(e) =>
                                            visit(status, search.trim(), sdate, e.target.value)
                                        }
                                        className="h-10 w-full py-1 sm:w-auto"
                                    />
                                    <TextInput
                                        type="search"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key !== "Enter") {
                                                return;
                                            }

                                            e.preventDefault();
                                            visit(status, search.trim(), sdate, edate);
                                        }}
                                        className="h-10 w-full py-1 sm:w-56"
                                        placeholder={t("Search deposits...")}
                                    />
                                    {hasActiveFilters ? (
                                        <button
                                            type="button"
                                            className="inline-flex h-10 w-auto items-center justify-center self-start rounded-md border border-gray-300 bg-white px-3 py-1 text-sm font-semibold text-slate-700 shadow-sm hover:bg-gray-50"
                                            onClick={() => {
                                                setSearch("");
                                                visit("*", "", "", "");
                                            }}
                                        >
                                            {t("Reset")}
                                        </button>
                                    ) : null}
                                </div>

                                <div className="flex py-1 xl:justify-end">
                                    <PrimaryButton
                                        type="button"
                                        onClick={print}
                                        className="inline-flex w-auto justify-center self-start"
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                    />

                    <br />

                    <div id="pdf-content">
                        <hr clas="my-1" />
                        <div className="overflow-x-auto">
                            <Table data={history?.data ?? []}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{t("User")}</th>
                                        <th>{t("Amount")}</th>
                                        <th>{t("Payment")}</th>
                                        <th>{t("Trx ID")}</th>
                                        <th>{t("Status")}</th>
                                        <th>{t("Date")}</th>
                                        <th>{t("A/C")}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {(history?.data ?? []).map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{(history?.from ?? 1) + index}</td>
                                            <td>
                                                <NavLinkBtn
                                                    href={route(
                                                        "system.users.edit",
                                                        { id: item.user.id },
                                                    )}
                                                >
                                                    {item.user.name}
                                                </NavLinkBtn>
                                            </td>
                                            <td>{formatCurrency(item.amount)}</td>
                                            <td>
                                                <div className="flex items-center">
                                                    {item.senderAccountNumber}{" "}
                                                    <i className="px-2 fas fa-caret-right"></i>
                                                    {item.paymentMethod}{" "}
                                                    <i className="px-2 fas fa-caret-right"></i>
                                                    {item.receiverAccountNumber}
                                                </div>
                                            </td>
                                            <td>{item.transactionId ?? "N/A"}</td>
                                            <td>
                                                {item.confirmed
                                                    ? "Confirmed"
                                                    : "Pending"}
                                            </td>
                                            <td>{item.created_at_diff}</td>
                                            <td>
                                                <div className="flex items-center gap-2 px-2 py-1">
                                                    {item.confirmed ? (
                                                        <ActionIconButton
                                                            action="confirm"
                                                            className="cursor-default opacity-70"
                                                            disabled
                                                            title={t("Confirmed")}
                                                        />
                                                    ) : (
                                                        <ActionIconButton
                                                            action="confirm"
                                                            title={t("Confirm")}
                                                            onClick={() =>
                                                                confirmDeposit(item.id)
                                                            }
                                                        />
                                                    )}
                                                    <ActionIconButton
                                                        action="reject"
                                                        className={`${
                                                            item.confirmed
                                                                ? "opacity-50 cursor-not-allowed"
                                                                : ""
                                                        }`}
                                                        disabled={item.confirmed}
                                                        title={
                                                            item.confirmed
                                                                ? "Confirmed deposits cannot be deleted"
                                                                : "Delete"
                                                        }
                                                        onClick={() =>
                                                            denyDeposit(item.id)
                                                        }
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td
                                            colSpan="2"
                                            className="font-bold text-right"
                                        >{t("Total")}</td>
                                        <td className="font-bold">
                                            {formatCurrency(history?.sum)}
                                        </td>
                                        <td colSpan="5"></td>
                                    </tr>
                                </tfoot>
                            </Table>
                        </div>
                        <div>
                            {pagination.pages.length ? (
                                <div className="w-full pt-4">
                                    <div className="flex w-full flex-col gap-3 md:flex-row md:items-center md:justify-between">
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
                        </div>
                    </div>
                </Section>
            </Container>
        </AppLayout>
    );
}
