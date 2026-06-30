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
import { formatCurrency } from "../../../utils/formatAmount";



function EarningCard({ title, amount, href, t }) {
    return (
        <div className="w-full">
            <div className="space-y-3 rounded-lg p-3 shadow-md">
                <div>
                    <div>{title}</div>
                </div>
                <div className="pt-2 text-lg font-bold text-indigo-900">
                    {formatCurrency(amount)}
                </div>
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
                    <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <SectionHeader
                            title={t("Your Wallet")}
                            content={
                                <div>
                                    <div className="text-2xl font-bold text-indigo-900">
                                        {" "}{t("Wallet Balance")}-{formatCurrency(wallet_balance)}{" "}
                                    </div>
                                    <div className="text-sm text-gray-600">
                                        {t("Withdrawable Balance")}: {formatCurrency(available_balance)}
                                    </div>
                                </div>
                            }
                        />
                        <Link
                            href={route("user.wallet.withdraw")}
                            className="w-full rounded-lg px-3 py-2 text-center text-sm font-bold uppercase ring-1 md:w-auto"
                        >
                            Withdraw
                        </Link>
                    </div>
                </SectionSection>

                <SectionSection>
                    <SectionHeader title={t("Todays Earning")} />
                    <SectionInner>
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
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
                            <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
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
                                    className="w-full py-1 sm:w-auto"
                                    placeholder={t("Search requests...")}
                                />
                                <PrimaryButton
                                    type="button"
                                    className="w-full sm:w-auto"
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
                                                <td>{formatCurrency(item.amount)}</td>
                                                <td>{t(item.status_label ?? item.status)}</td>
                                                <td className="text-xs text-gray-500">
                                                    {item.created_at} - {item.created_at_human}
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
                                                <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                                    <button
                                                        type="button"
                                                        disabled={!pagination.prev?.url}
                                                        className="border-r border-slate-200 px-3 py-2 text-xs text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400 sm:px-4 sm:text-sm"
                                                        onClick={() => goToPage(pagination.prev?.url)}
                                                    >{t("Previous")}</button>
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
                                                        className="px-3 py-2 text-xs text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400 sm:px-4 sm:text-sm"
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
