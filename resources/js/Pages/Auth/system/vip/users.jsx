import { router, usePage } from "@inertiajs/react";
import { useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Foreach from "../../../../components/dashboard/Foreach";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";

export default function Users() {
    const { vip, filters = {}, printUrl } = usePage().props;
    const [showFilterModal, setShowFilterModal] = useState(false);
    const [search, setSearch] = useState(filters.search ?? "");
    const [nav, setNav] = useState(filters.nav ?? "All");
    const [type, setType] = useState(filters.type ?? "All");
    const [validity, setValidity] = useState(filters.validity ?? "All");
    const [sdate, setSdate] = useState(filters.sdate ?? "");
    const [edate, setEdate] = useState(filters.edate ?? "");

    const applyFilters = (next = {}) => {
        router.get(
            route("system.vip.users"),
            {
                nav,
                search,
                sdate,
                edate,
                type,
                validity,
                ...next,
            },
            { preserveState: true, preserveScroll: true }
        );
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        applyFilters({
            nav: nextUrl.searchParams.get("nav") ?? nav,
            search: nextUrl.searchParams.get("search") ?? search,
            sdate: nextUrl.searchParams.get("sdate") ?? sdate,
            edate: nextUrl.searchParams.get("edate") ?? edate,
            type: nextUrl.searchParams.get("type") ?? type,
            validity: nextUrl.searchParams.get("validity") ?? validity,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = vip?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [vip?.links]);

    const resultSummary =
        vip?.total > 0
            ? `Showing ${vip?.from ?? 0}-${vip?.to ?? 0} of ${vip?.total ?? 0} vip users`
            : "No vip users found";

    return (
        <AppLayout
            title="VIP Users"
            header={
                <PageHeader>
                    <div className="md:flex items-center justify-between">
                        <div className="mb-1">VIP Users</div>
                    </div>
                </PageHeader>
            }
        >
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={
                            <div className="flex flex-wrap justify-between items-start">
                                <div className="flex items-center gap-2">
                                    <SecondaryButton
                                        type="button"
                                        onClick={() => setShowFilterModal(true)}
                                    >
                                        <i className="fa-solid fa-filter"></i>
                                    </SecondaryButton>
                                    <select
                                        className="rounded-md py-1"
                                        value={nav}
                                        onChange={(e) => {
                                            setNav(e.target.value);
                                            applyFilters({ nav: e.target.value });
                                        }}
                                    >
                                        <option value="All">All</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Confirmed">Active</option>
                                        <option value="Trash">Trash</option>
                                    </select>
                                </div>
                                <div className="flex items-center">
                                    <input
                                        type="search"
                                        className="ms-2 rounded-lg border-gray-400 py-1"
                                        placeholder="find name, id"
                                        value={search}
                                        onChange={(e) => {
                                            setSearch(e.target.value);
                                            applyFilters({ search: e.target.value });
                                        }}
                                    />
                                    <PrimaryButton
                                        type="button"
                                        className="ms-2"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        <Foreach data={vip?.data ?? []}>
                            <div>
                                <Table data={vip?.data ?? []}>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                            <th>VIP</th>
                                            <th>Wallet</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Validity</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {(vip?.data ?? []).map((item) => (
                                            <tr key={item.id}>
                                                <td>{item.sl}</td>
                                                <td>
                                                    {item.name ?? "N/A"}
                                                    <br />
                                                    <div className="text-xs ">
                                                        {item.user_email ?? "N/A"}
                                                    </div>
                                                </td>
                                                <td>
                                                    {item.package_name ?? "N/A"}
                                                    <div className="text-xs">
                                                        {" "}
                                                        {item.task_type ?? "N/A"}{" "}
                                                    </div>
                                                </td>
                                                <td>{item.user_coin ?? "0"}</td>
                                                <td>
                                                    {item.status}
                                                    <br />
                                                    {item.deleted_at_formatted ? (
                                                        <span className="text-xs text-red-900 text-bold ">
                                                            {item.deleted_at_formatted}
                                                        </span>
                                                    ) : null}
                                                </td>
                                                <td>
                                                    <div className="text-nowrap">
                                                        {item.created_at_formatted}
                                                    </div>
                                                </td>
                                                <td>
                                                    {item.valid_till_formatted}
                                                    <div className="text-xs">
                                                        {item.valid_till_human}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div className="flex space-x-3">
                                                        <NavLink
                                                            href={route("system.vip.edit", {
                                                                vip: item.id,
                                                            })}
                                                        >
                                                            View
                                                        </NavLink>
                                                        <NavLink href="#">User</NavLink>
                                                    </div>
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
                        </Foreach>
                    </SectionInner>
                </SectionSection>
            </Container>

            <Modal
                show={showFilterModal}
                onClose={() => setShowFilterModal(false)}
                maxWidth="2xl"
            >
                <div className="p-2">User Filter</div>
                <hr className="my-1" />
                <div className="p-3">
                    <div className="md:flex justify-between items-start mb-2 border-b">
                        <p>Taks Type</p>

                        <div className="md:flex items-center gap-2">
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="daily"
                                    value="daily"
                                    checked={type === "daily"}
                                    onChange={(e) => setType(e.target.value)}
                                />
                                <p>Daily Taks</p>
                            </div>
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="monthly"
                                    value="monthly"
                                    checked={type === "monthly"}
                                    onChange={(e) => setType(e.target.value)}
                                />
                                <p>Monthly Taks</p>
                            </div>
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="type_all"
                                    value="All"
                                    checked={type === "All"}
                                    onChange={(e) => setType(e.target.value)}
                                />
                                <p>Both</p>
                            </div>
                        </div>
                    </div>
                    <div className="md:flex items-start justify-between gap-2 mb-2 border-b">
                        <p>Package Validity</p>
                        <div className="md:flex items-center gap-2">
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="valid"
                                    value="valid"
                                    checked={validity === "valid"}
                                    onChange={(e) => setValidity(e.target.value)}
                                />
                                <p>Only Valid</p>
                            </div>
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="invalid"
                                    value="invalid"
                                    checked={validity === "invalid"}
                                    onChange={(e) => setValidity(e.target.value)}
                                />
                                <p>Only Invalid</p>
                            </div>
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="validity_all"
                                    value="All"
                                    checked={validity === "All"}
                                    onChange={(e) => setValidity(e.target.value)}
                                />
                                <p>Both</p>
                            </div>
                        </div>
                    </div>
                    <div className="md:flex items-start justify-between gap-2">
                        <p>Between Date</p>
                        <div className="flex text-xs gap-2">
                            <TextInput
                                type="date"
                                className="py-1"
                                value={sdate}
                                onChange={(e) => setSdate(e.target.value)}
                            />
                            <TextInput
                                type="date"
                                className="py-1"
                                value={edate}
                                onChange={(e) => setEdate(e.target.value)}
                            />
                        </div>
                    </div>
                </div>
                <hr className="my-1" />
                <div className="p-3 flex gap-2">
                    <SecondaryButton
                        type="button"
                        onClick={() => setShowFilterModal(false)}
                    >
                        Close
                    </SecondaryButton>
                    <PrimaryButton
                        type="button"
                        onClick={() => {
                            setShowFilterModal(false);
                            applyFilters();
                        }}
                    >
                        Apply
                    </PrimaryButton>
                </div>
            </Modal>
        </AppLayout>
    );
}
