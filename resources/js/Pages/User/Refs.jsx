import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import Container from "../../components/dashboard/Container";
import SectionSection from "../../components/dashboard/section/Section";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionInner from "../../components/dashboard/section/Inner";
import Table from "../../components/dashboard/table/Table";
import UserDash from "../../components/user/dash/UserDash";
import PrimaryButton from "../../components/PrimaryButton";
import TextInput from "../../components/TextInput";

export default function Refs() {
    const {
        refUsers = {},
        refOwnerName = "User Not Found",
        totalRefUsers = 0,
        filters = {},
        printUrl,
    } = usePage().props;
    const rows = refUsers.data ?? [];
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
                route("user.ref.view"),
                { find: trimmedSearch },
                {
                    preserveScroll: true,
                    preserveState: true,
                    replace: true,
                    only: ["filters", "refUsers", "totalRefUsers", "printUrl"],
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
            route("user.ref.view"),
            {
                find: nextUrl.searchParams.get("find") ?? search,
                page: nextUrl.searchParams.get("page") ?? undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                only: ["filters", "refUsers", "totalRefUsers", "printUrl"],
            }
        );
    };

    const pagination = useMemo(() => {
        const links = refUsers?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [refUsers?.links]);

    const resultSummary =
        refUsers?.total > 0
            ? `Showing ${refUsers?.from ?? 0}-${refUsers?.to ?? 0} of ${refUsers?.total ?? 0} referred users`
            : "No referred users found";

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title="Referred User"
                        content={
                            <div className="flex flex-wrap items-center justify-between gap-3">
                                <p>
                                    You accept referrer by <strong>{refOwnerName}</strong>. And You have total{" "}
                                    {totalRefUsers} referrer user.
                                </p>
                                <div className="flex flex-wrap items-center justify-end gap-2 ml-auto">
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
                                                route("user.ref.view"),
                                                { find: search.trim() },
                                                {
                                                    preserveScroll: true,
                                                    preserveState: true,
                                                    replace: true,
                                                }
                                            );
                                        }}
                                        className="py-1"
                                        placeholder="Search ref users..."
                                    />
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                    />

                    <SectionInner>
                        <Table data={rows}>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Comission</th>
                                    <th>Join</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((user) => (
                                    <tr key={user.id}>
                                        <td>{user.id}</td>
                                        <td>{user.name}</td>
                                        <td>{user.email ?? "N/A"}</td>
                                        <td>{user.phone ?? "N/A"}</td>
                                        <td>{user.comission}</td>
                                        <td>{user.join}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>

                        {pagination.pages.length ? (
                            <div className="w-full pt-4">
                                <div className="flex items-center justify-between w-full gap-3">
                                    <div className="text-sm text-slate-700">
                                        {resultSummary}
                                    </div>
                                    <div className="flex items-center md:justify-end">
                                        <div className="overflow-hidden bg-white border shadow-sm rounded-xl border-slate-200">
                                            <button
                                                type="button"
                                                disabled={!pagination.prev?.url}
                                                className="px-4 py-2 text-sm transition border-r border-slate-200 text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
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
                                                className="px-4 py-2 text-sm transition text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pagination.next?.url)}
                                            >
                                                Next
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ) : null}
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
