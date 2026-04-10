import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

export default function Index({ filters, comissions }) {
    const [showFilterModal, setShowFilterModal] = useState(false);
    const [where, setWhere] = useState(filters?.where ?? "");
    const [confirm, setConfirm] = useState(filters?.confirm ?? "All");
    const [wid, setWid] = useState(filters?.wid ?? "");
    const [search, setSearch] = useState(filters?.wid ?? "");

    const apply = (next = {}) => {
        router.get(
            route("system.comissions.index"),
            {
                confirm: next.confirm ?? filters?.confirm ?? "",
                where: next.where ?? filters?.where ?? "",
                from: next.from ?? filters?.from ?? "",
                to: next.to ?? filters?.to ?? "",
                wid: next.wid ?? filters?.wid ?? "",
                page: next.page ?? undefined,
            },
            { preserveScroll: true, preserveState: true }
        );
    };

    useEffect(() => {
        setSearch(filters?.wid ?? "");
    }, [filters?.wid]);

    useEffect(() => {
        if ((where ?? "") !== "") {
            return;
        }

        const trimmedSearch = search.trim();
        const currentSearch = (filters?.wid ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            apply({ wid: trimmedSearch, where: "", page: undefined });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search, where]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        apply({
            confirm: nextUrl.searchParams.get("confirm") ?? filters?.confirm ?? "All",
            where: nextUrl.searchParams.get("where") ?? filters?.where ?? "",
            from: nextUrl.searchParams.get("from") ?? filters?.from ?? "",
            to: nextUrl.searchParams.get("to") ?? filters?.to ?? "",
            wid: nextUrl.searchParams.get("wid") ?? filters?.wid ?? "",
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = comissions?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [comissions?.links]);

    const resultSummary =
        comissions?.total > 0
            ? `Showing ${comissions?.from ?? 0}-${comissions?.to ?? 0} of ${comissions?.total ?? 0} comissions`
            : "No comissions found";

    const openPrintable = () => {
        window.open(
            route("system.comissions.takes", {
                confirm: filters?.confirm ?? "",
                where: filters?.where ?? "",
                from: filters?.from ?? "",
                to: filters?.to ?? "",
                wid: filters?.wid ?? "",
            }),
            "_blank"
        );
    };

    return (
        <AppLayout
            title="Comissions"
            header={
                <PageHeader>
                    <div className="flex justify-between">
                        <div>Comissions</div>
                    </div>
                </PageHeader>
            }
        >
            <Head title="Comissions" />

            <Container>
                <div className="flex justify-between items-end mb-4">
                    <div>
                        <PrimaryButton type="button" onClick={() => setShowFilterModal(true)}>
                            <i className="fas fa-filter"></i>
                        </PrimaryButton>
                    </div>
                    <div className="flex justify-start items-end mb-2 space-x-1">
                        <div>
                            <TextInput
                                className=" py-1 w-full "
                                type="date"
                                value={filters?.from ?? ""}
                                onChange={(e) => apply({ from: e.target.value })}
                            />
                        </div>

                        <div>
                            <TextInput
                                className=" py-1 w-full "
                                type="date"
                                value={filters?.to ?? ""}
                                onChange={(e) => apply({ to: e.target.value })}
                            />
                        </div>
                        <div>
                            <TextInput
                                className="py-1 w-full"
                                type="search"
                                placeholder="Search comissions..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                onKeyDown={(e) => {
                                    if (e.key !== "Enter") {
                                        return;
                                    }

                                    e.preventDefault();
                                    apply({ wid: search.trim(), where: "", page: undefined });
                                }}
                            />
                        </div>
                        <PrimaryButton type="button" onClick={openPrintable} className="btn">
                            <i className="fas fa-print"></i>
                        </PrimaryButton>
                    </div>
                </div>

                <Hr className="my-2" />

                <SectionInner>
                    <div>
                        <Table data={[comissions?.summary ?? {}]}>
                            <thead>
                                <tr>
                                    <th>Seller Total Profit</th>
                                    <th>Cut comission</th>
                                    <th>Distribute</th>
                                    <th>Store</th>
                                    <th>Return</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>{comissions?.summary?.profit ?? 0}</td>
                                    <td>{comissions?.summary?.take_comission ?? 0}</td>
                                    <td>{comissions?.summary?.distribute_comission ?? 0}</td>
                                    <td>{comissions?.summary?.store ?? 0}</td>
                                    <td>{comissions?.summary?.return ?? 0}</td>
                                </tr>
                            </tbody>
                        </Table>
                    </div>
                </SectionInner>

                <Section id="pdf-content">
                    <Hr />
                    <Table data={comissions?.data ?? []}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>DT</th>
                                <th>ID</th>
                                <th>Order</th>
                                <th>Product</th>
                                <th>Buy</th>
                                <th>Sell</th>
                                <th>Profit</th>
                                <th>Rate</th>
                                <th>Take</th>
                                <th>Give</th>
                                <th>Store</th>
                                <th>Return</th>
                                <th>Confirmed</th>
                                <th>A/C</th>
                            </tr>
                        </thead>

                        <tbody>
                            {(comissions?.data ?? []).map((item, index) => (
                                <tr key={item.id}>
                                    <td>{(comissions?.from ?? 1) + index}</td>
                                    <td>{item.created_at_formatted}</td>
                                    <td>{item.id ?? "N/A"}</td>
                                    <td>{item.order_id ?? 0}</td>
                                    <td>{item.product_id ?? 0}</td>
                                    <td>{item.buying_price ?? 0}</td>
                                    <td>{item.selling_price ?? 0}</td>
                                    <td>{item.profit ?? 0}</td>
                                    <td>{item.comission_range ?? 0} %</td>
                                    <td>{item.take_comission ?? 0}</td>
                                    <td>{item.distribute_comission ?? 0}</td>
                                    <td>{item.store ?? 0}</td>
                                    <td>{item.return ?? 0}</td>
                                    <td>
                                        {item.confirmed ? (
                                            <>
                                                <span className="p-1 px-2 rounded-xl bg-green-900 text-white">Confirmed</span>
                                                <NavLink href={route("system.comissions.take.refund", { id: item.id })}>
                                                    {" "}Refund
                                                </NavLink>
                                            </>
                                        ) : (
                                            <>
                                                <span className="p-1 px-2 rounded-xl bg-gray-900 text-white">Pending</span>
                                                <form action={route("system.comissions.take.confirm", { id: item.id })} method="post">
                                                    <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content ?? ""} />
                                                    <button type="submit">Confirm</button>
                                                </form>
                                            </>
                                        )}
                                    </td>
                                    <td>
                                        <div className="flex space-x-2">
                                            <NavLink href={route("system.comissions.distributes", { id: item.id })}>
                                                Details
                                            </NavLink>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>

                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>{comissions?.summary?.buying_price}</th>
                                <th>{comissions?.summary?.selling_price}</th>
                                <th>{comissions?.summary?.profit}</th>
                                <td></td>
                                <th>{comissions?.summary?.take_comission}</th>
                                <th>{comissions?.summary?.distribute_comission}</th>
                                <th>{comissions?.summary?.store}</th>
                                <th>{comissions?.summary?.return}</th>
                                <th></th>
                                <th></th>
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
                </Section>
            </Container>

            <Modal show={showFilterModal} onClose={() => setShowFilterModal(false)}>
                <div className="p-3">Filter Comissions</div>
                <Hr className="my-1" />

                <div className="p-3">
                    <div className="flex items-start justify-between my-2 space-x-1">
                        <div>
                            <select
                                value={where}
                                onChange={(e) => {
                                    setWhere(e.target.value);
                                    apply({ where: e.target.value, wid, confirm, page: undefined });
                                }}
                                className="w-full rounded-md py-1"
                            >
                                <option value="">-- Select -- </option>
                                <option value="user_id">User</option>
                                <option value="product_id">Product</option>
                                <option value="order_id">Order</option>
                            </select>
                        </div>
                        <div>
                            <select
                                value={confirm}
                                onChange={(e) => {
                                    setConfirm(e.target.value);
                                    apply({ confirm: e.target.value, where, wid, page: undefined });
                                }}
                                className="py-1 rounded-md"
                            >
                                <option value="All">Both</option>
                                <option value="true">Confirmed</option>
                                <option value="false">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <TextInput
                            className="w-full"
                            placeholder="Search By ID"
                            value={wid}
                            onChange={(e) => {
                                setWid(e.target.value);
                                apply({ wid: e.target.value, where, confirm, page: undefined });
                            }}
                        />
                    </div>
                </div>
                <Hr className="my-1" />
                <div className="p-3">
                    <div className="flex items-center justify-end w-full space-x-1">
                        <SecondaryButton type="button" onClick={() => setShowFilterModal(false)}>
                            Cancel
                        </SecondaryButton>
                    </div>
                </div>
            </Modal>
        </AppLayout>
    );
}
