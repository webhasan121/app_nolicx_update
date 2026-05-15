import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import Table from "../../../../components/dashboard/table/Table";
import useTranslation from "../../../../hooks/useTranslation";

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

    const visit = (nextStatus, nextFind, nextSdate, nextEdate, page = null) => {
        router.get(
            route("system.deposit.index"),
            buildParams(nextStatus, nextFind, nextSdate, nextEdate, page),
            {
                preserveScroll: true,
                preserveState: true,
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

    return (
        <AppLayout title={t("Deposit")} header={<PageHeader>{t("Deposit")}</PageHeader>}>
            <Head title={t("Deposit")} />

            <Container>
                <Section>
                    <SectionHeader
                        title=""
                        content={
                            <div className="flex flex-wrap items-center justify-between gap-3">
                                <div className="flex flex-wrap items-center gap-2 py-1">
                                    <select
                                        value={status}
                                        onChange={(e) =>
                                            visit(e.target.value, search.trim(), sdate, edate)
                                        }
                                        className="py-1 mb-1 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500 focus:ring-1"
                                    >
                                        <option value="*">{t("All")}</option>
                                        <option value="0">{t("Pending")}</option>
                                        <option value="1">{t("Confirmed")}</option>
                                    </select>
                                    <TextInput
                                        type="date"
                                        id="sdate"
                                        value={sdate}
                                        onChange={(e) =>
                                            visit(status, search.trim(), e.target.value, edate)
                                        }
                                        className="py-1"
                                    />
                                    <TextInput
                                        type="date"
                                        id="edate"
                                        value={edate}
                                        onChange={(e) =>
                                            visit(status, search.trim(), sdate, e.target.value)
                                        }
                                        className="py-1"
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
                                        className="py-1"
                                        placeholder={t("Search deposits...")}
                                    />
                                </div>

                                <div className="flex items-center justify-end py-1">
                                    <PrimaryButton type="button" onClick={print}>
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                    />

                    <br />

                    <div id="pdf-content">
                        <hr clas="my-1" />
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
                                        <td>{item.amount ?? 0}</td>
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
                                                    <button
                                                        type="button"
                                                        className="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-white bg-green-600 border border-transparent rounded-md cursor-default"
                                                        disabled
                                                        title={t("Confirmed")}
                                                    >
                                                        <i className="fas fa-check-circle"></i>
                                                    </button>
                                                ) : (
                                                    <PrimaryButton
                                                        type="button"
                                                        className="justify-center w-8 h-8 px-0 py-0"
                                                        onClick={() =>
                                                            confirmDeposit(item.id)
                                                        }
                                                    >
                                                        <i className="fas fa-check"></i>
                                                    </PrimaryButton>
                                                )}
                                                <DangerButton
                                                    type="button"
                                                    className={`justify-center w-8 h-8 px-0 py-0 ${
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
                                                >
                                                    <i className="fas fa-times"></i>
                                                </DangerButton>
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
                                        {history?.sum}
                                    </td>
                                    <td colSpan="5"></td>
                                </tr>
                            </tfoot>
                        </Table>
                        <div>
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
                        </div>
                    </div>
                </Section>
            </Container>
        </AppLayout>
    );
}
