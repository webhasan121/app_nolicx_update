import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../Layouts/App";
import Modal from "../../../components/Modal";
import NavLink from "../../../components/NavLink";
import NavLinkBtn from "../../../components/NavLinkBtn";
import PrimaryButton from "../../../components/PrimaryButton";
import TextInput from "../../../components/TextInput";
import Container from "../../../components/dashboard/Container";
import Foreach from "../../../components/dashboard/Foreach";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";

function buildQuery(filters, updates = {}) {
    return Object.fromEntries(
        Object.entries({ ...filters, ...updates }).filter(
            ([, value]) => value !== "" && value !== null && value !== undefined
        )
    );
}

export default function Index({ filters = {}, products = { data: [], links: [] }, isReseller = false, printUrl }) {
    const [filterOpen, setFilterOpen] = useState(false);
    const [selectedModel, setSelectedModel] = useState([]);
    const [searchTerm, setSearchTerm] = useState(filters.search ?? "");
    const [modalCreated, setModalCreated] = useState(filters.created ?? "");
    const [modalStatus, setModalStatus] = useState(
        filters.take === "trash" ? "trash" : (filters.nav ?? "Active")
    );

    const rows = products?.data ?? [];
    const isTrash = filters.take === "trash";

    const updateFilters = (updates, resetPage = true) => {
        router.get(route("vendor.products.view"), buildQuery(filters, { ...updates, ...(resetPage ? { page: 1 } : {}) }), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const toggleSelected = (id) => {
        setSelectedModel((prev) =>
            prev.includes(id) ? prev.filter((item) => item !== id) : [...prev, id]
        );
    };

    const postBulkAction = (routeName) => {
        if (selectedModel.length < 1) return;
        router.post(
            route(routeName),
            { selectedModel },
            { preserveScroll: true, onSuccess: () => setSelectedModel([]) }
        );
    };

    useEffect(() => {
        setSearchTerm(filters.search ?? "");
    }, [filters.search]);

    useEffect(() => {
        setModalCreated(filters.created ?? "");
        setModalStatus(filters.take === "trash" ? "trash" : (filters.nav ?? "Active"));
    }, [filters.created, filters.nav, filters.take]);

    useEffect(() => {
        const timeoutId = window.setTimeout(() => {
            if ((searchTerm ?? "") === (filters.search ?? "")) {
                return;
            }
            updateFilters({ search: searchTerm ?? "" });
        }, 400);

        return () => window.clearTimeout(timeoutId);
    }, [searchTerm, filters.search]);

    const pagination = useMemo(() => {
        const links = products?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [products?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        updateFilters({
            nav: nextUrl.searchParams.get("nav") ?? filters.nav ?? "Active",
            take: nextUrl.searchParams.get("take") ?? filters.take ?? "",
            search: nextUrl.searchParams.get("search") ?? filters.search ?? "",
            created: nextUrl.searchParams.get("created") ?? filters.created ?? "",
            page: nextUrl.searchParams.get("page") ?? undefined,
        }, false);
    };

    const resultSummary =
        products?.total > 0
            ? `Showing ${products?.from ?? 0}-${products?.to ?? 0} of ${products?.total ?? 0} products`
            : "No products found";

    const applyModalFilters = () => {
        if (modalStatus === "trash") {
            updateFilters({ take: "trash", nav: "", created: modalCreated });
        } else {
            updateFilters({ take: "", nav: modalStatus, created: modalCreated });
        }

        setFilterOpen(false);
    };

    const resetModalFilters = () => {
        setModalCreated("");
        setModalStatus("Active");
        updateFilters({ take: "", nav: "Active", created: "" });
        setFilterOpen(false);
    };

    return (
        <AppLayout
            title="Products"
            header={
                <PageHeader>
                    Products
                    <br />
                    <div>
                        <NavLink href={route("vendor.products.view")} active={route().current("vendor.products.*")}>
                            Your Product
                        </NavLink>
                        {isReseller ? (
                            <NavLink
                                href={route("reseller.resel-product.index")}
                                active={route().current("reseller.resel-product.*")}
                            >
                                Reseller Product
                            </NavLink>
                        ) : null}
                    </div>
                </PageHeader>
            }
        >
            <Head title="Products" />

            <Container>
                <Section>
                    <SectionHeader title="Your Products" content="Your have product to resel" />
                    <SectionInner>
                        <NavLinkBtn href={route("vendor.products.create")}>
                            Add Product
                        </NavLinkBtn>
                    </SectionInner>
                </Section>
            </Container>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-center">
                                <div>
                                    {selectedModel.length < 1 ? (
                                        <div>
                                            <NavLink href={route("vendor.products.view", buildQuery(filters, { nav: "Active", take: "" }))} active={(filters.nav ?? "Active") === "Active" && !filters.take}>
                                                Active
                                            </NavLink>
                                            <NavLink href={route("vendor.products.view", buildQuery(filters, { take: "trash", nav: "" }))} active={isTrash}>
                                                Trash
                                            </NavLink>
                                        </div>
                                    ) : isTrash ? (
                                        <PrimaryButton type="button" onClick={() => postBulkAction("vendor.products.bulk-restore")}>
                                            Restore
                                        </PrimaryButton>
                                    ) : (
                                        <PrimaryButton type="button" onClick={() => postBulkAction("vendor.products.bulk-trash")}>
                                            Move to Trash
                                        </PrimaryButton>
                                    )}
                                </div>

                                <div className="flex items-center">
                                    <TextInput
                                        type="search"
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                        placeholder="Search by name"
                                        className="mx-2 hidden lg:block py-1"
                                    />
                                    <PrimaryButton type="button" className="mr-2" onClick={() => window.open(printUrl, "_blank")}>
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                    <PrimaryButton type="button" onClick={() => setFilterOpen(true)}>
                                        Filter
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                        content=""
                    />
                    <SectionInner>
                        <Foreach data={rows}>
                            <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Stock</th>
                                        <th>Build Cost</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Status</th>
                                        <th>Insert At</th>
                                        <th>A/C</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.map((product, index) => (
                                        <tr key={product.id}>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    checked={selectedModel.includes(product.id)}
                                                    onChange={() => toggleSelected(product.id)}
                                                    style={{ width: 20, height: 20 }}
                                                />
                                            </td>
                                            <td>{index + 1}</td>
                                            <td>
                                                <div className="flex items-center">
                                                    {product.thumbnail_url ? (
                                                        <img className="w-8 h-8 mr-2 rounded-md" src={product.thumbnail_url} alt="" />
                                                    ) : null}
                                                    {product.name ?? "N/A"}
                                                </div>
                                            </td>
                                            <td>{product.unit}</td>
                                            <td>{product.buying_price}</td>
                                            <td>{product.price}</td>
                                            <td>{product.discount ?? 0}</td>
                                            <td>{product.status}</td>
                                            <td>{product.created_at_human}</td>
                                            <td>
                                                <NavLinkBtn href={route("vendor.products.edit", { product: product.encrypted_id })}>
                                                    view
                                                </NavLinkBtn>
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
                        </Foreach>
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={filterOpen} onClose={() => setFilterOpen(false)} maxWidth="xl">
                <div className="p-3">
                    <SectionHeader title="Filter Your Own" content="" />
                    <SectionInner>
                        <div className="flex justify-between items-start gap-6">
                            <div>
                                <h3>Filter by Create date</h3>
                                <ul className="ms-4 mt-2">
                                    <li>
                                        <div className="flex items-center mb-2">
                                            <input
                                                className="p-0 m-0 mr-3"
                                                type="radio"
                                                checked={modalCreated === "today"}
                                                onChange={() => setModalCreated("today")}
                                            />
                                            <label className="p-0 m-0">Today</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h3>Filter by Status</h3>
                                <ul className="ms-4 mt-2">
                                    <li>
                                        <div className="flex items-center mb-2">
                                            <input
                                                className="p-0 m-0 mr-3"
                                                type="radio"
                                                checked={modalStatus === "Active"}
                                                onChange={() => setModalStatus("Active")}
                                            />
                                            <label className="p-0 m-0">Active</label>
                                        </div>
                                        <div className="flex items-center mb-2">
                                            <input
                                                className="p-0 m-0 mr-3"
                                                type="radio"
                                                checked={modalStatus === "In Active"}
                                                onChange={() => setModalStatus("In Active")}
                                            />
                                            <label className="p-0 m-0">Disable</label>
                                        </div>
                                        <div className="flex items-center mb-2">
                                            <input
                                                className="p-0 m-0 mr-3"
                                                type="radio"
                                                checked={modalStatus === "trash"}
                                                onChange={() => setModalStatus("trash")}
                                            />
                                            <label className="p-0 m-0">Trash</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div className="flex items-end gap-2 pt-7">
                                <PrimaryButton type="button" onClick={resetModalFilters}>
                                    Reset
                                </PrimaryButton>
                                <PrimaryButton type="button" onClick={applyModalFilters}>
                                    Apply
                                </PrimaryButton>
                            </div>
                        </div>
                    </SectionInner>
                </div>
            </Modal>
        </AppLayout>
    );
}
