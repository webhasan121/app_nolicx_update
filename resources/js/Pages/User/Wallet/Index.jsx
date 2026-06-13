import { Link, router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import PrimaryButton from "../../../components/PrimaryButton";
import TextInput from "../../../components/TextInput";
import Table from "../../../components/dashboard/table/Table";
import UserDash from "../../../components/user/dash/UserDash";
import useTranslation from "../../../hooks/useTranslation";
import { formatAmount } from "../../../utils/formatAmount";



function EarningCard({ title, amount, href, t }) {
    return (
        <div className="w-48 space-y-3">
            <div className="p-3 rounded-lg shadow-md">
                <div>
                    <div className="">{title}</div>
                </div>
                <div className="pt-2 text-lg font-bold text-indigo-900">
                    {formatAmount(amount)}{t("TK")}</div>
                <div className="text-xs">
                    <Link href={href} className="text-gray-600">
                        View All
                    </Link>
                </div>
            </div>
        </div>
    );
}

export default function WalletIndex() {
    const { t } = useTranslation();
    const {
        wallet_balance,
        available_balance,
        task,
        comission,
        cut,
        reffer,
        withdraw,
        filters = {},
        printUrl,
    } = usePage().props;
    const [search, setSearch] = useState(filters.find ?? "");
    const rows = withdraw?.data ?? [];

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
                route("user.wallet.index"),
                { find: trimmedSearch },
                {
                    preserveScroll: true,
                    preserveState: true,
                    replace: true,
                    only: ["filters", "withdraw", "printUrl"],
                }
            );
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        router.get(
            route("user.wallet.index"),
            {
                find: nextUrl.searchParams.get("find") ?? search,
                page: nextUrl.searchParams.get("page") ?? undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                only: ["filters", "withdraw", "printUrl"],
            }
        );
    };

    const pagination = useMemo(() => {
        const links = withdraw?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [withdraw?.links]);

    const resultSummary =
        withdraw?.total > 0
            ? `Showing ${withdraw?.from ?? 0}-${withdraw?.to ?? 0} of ${withdraw?.total ?? 0} withdraw requests`
            : "No withdraw info found";

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <div className="items-center justify-between md:flex">
                        <SectionHeader
                            title={t("Your Wallet")}
                            content={
                                <div>
                                    <div className="text-2xl font-bold text-indigo-900">
                                        {" "}{t("Wallet Balance")}-{formatAmount(wallet_balance)}{t("TK")}{" "}
                                    </div>
                                    <div className="text-sm text-gray-600">
                                        {t("Withdrawable Balance")}: {formatAmount(available_balance)}{t("TK")}
                                    </div>
                                </div>
                            }
                        />
                        <Link
                            href={route("user.wallet.withdraw")}
                            className="px-2 py-2 text-sm font-bold uppercase border-0 rounded-lg ring-1"
                        >
                            Withdraw
                        </Link>
                    </div>
                </SectionSection>

                <SectionSection>
                    <SectionHeader title={t("Todays Earning")} />
                    <SectionInner>
                        <div className="flex flex-wrap items-start justify-start space-x-3 spacy-y-3">
                            <EarningCard
                                t={t}
                                title={t("Task")}
                                amount={task?.coin ?? 0}
                                href={route("user.wallet.tasks")}
                            />
                            <EarningCard
                                t={t}
                                title={t("Earn Comission")}
                                amount={comission}
                                href={route("user.wallet.earn-comissions")}
                            />
                            <EarningCard
                                t={t}
                                title={t("Cut Comission")}
                                amount={cut}
                                href={route("user.wallet.earn-comissions", {
                                    nav: "system",
                                })}
                            />
                            <EarningCard
                                t={t}
                                title={t("VIP Reffer")}
                                amount={reffer}
                                href={route("user.wallet.reffer")}
                            />
                        </div>
                    </SectionInner>
                </SectionSection>

                <SectionSection>
                    <SectionHeader
                        title={t("Withdraws Requests")}
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
                                            route("user.wallet.index"),
                                            { find: search.trim() },
                                            {
                                                preserveScroll: true,
                                                preserveState: true,
                                                replace: true,
                                            }
                                        );
                                    }}
                                    className="py-1"
                                    placeholder={t("Search requests...")}
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
                    <SectionInner>
                        {rows.length ? (
                            <div>
                                <Table data={rows}>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{t("Amount")}</th>
                                            <th>{t("Status")}</th>
                                            <th>{t("Date")}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {rows.map((item, index) => (
                                            <tr key={item.id}>
                                                <td>#{(withdraw?.from ?? 1) + index}</td>
                                                <td>{formatAmount(item.amount)}{t("TK")}</td>
                                                <td>{item.status}</td>
                                                <td className="text-xs text-gray-500">
                                                    {item.created_at} - {item.created_at_human}
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
                        ) : (
                            <div>{t("No Withdraw Info Found !")}</div>
                        )}
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
