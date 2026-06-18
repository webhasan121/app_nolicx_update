import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../Layouts/App";
import NavLink from "../../../components/NavLink";
import NavLinkBtn from "../../../components/NavLinkBtn";
import PrimaryButton from "../../../components/PrimaryButton";
import TextInput from "../../../components/TextInput";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";
import ProductName from "../../../components/ProductName";
import useTranslation from "../../../hooks/useTranslation";
import { ActionIconLink } from "../../../components/ActionIcon";
import { formatCurrency } from "../../../utils/formatAmount";

function buildQuery(filters, updates = {}) {
    return Object.fromEntries(
        Object.entries({ ...filters, ...updates }).filter(
            ([, value]) => value !== "" && value !== null && value !== undefined
        )
    );
}

export default function Index({ filters = {}, products = { data: [], links: [] }, isReseller = false, printUrl }) {
    const { t } = useTranslation();
    const [selectedModel, setSelectedModel] = useState([]);
    const [searchTerm, setSearchTerm] = useState(filters.search ?? "");

    const rows = products?.data ?? [];
    const isTrash = filters.take === "trash";

    const updateFilters = (updates, resetPage = true) => {
        router.get(route("vendor.products.view"), buildQuery(filters, { ...updates, ...(resetPage ? { page: 1 } : {}) }), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ["filters", "products", "printUrl", "selectedCount"],
        });
    };

    const toggleSelected = (id) => {
        setSelectedModel((prev) =>
            prev.includes(id) ? prev.filter((item) => item !== id) : [...prev, id]
        );
    };

    const postBulkAction = (routeName) => {
        if (selectedModel.length < 1) return;
        if (
            routeName === "vendor.products.bulk-trash" &&
            !window.confirm("Are you sure you want to move selected products to trash?")
        ) {
            return;
        }

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
            start_date: nextUrl.searchParams.get("start_date") ?? filters.start_date ?? "",
            end_date: nextUrl.searchParams.get("end_date") ?? filters.end_date ?? "",
            page: nextUrl.searchParams.get("page") ?? undefined,
        }, false);
    };

    const resultSummary =
        products?.total > 0
            ? `Showing ${products?.from ?? 0}-${products?.to ?? 0} of ${products?.total ?? 0} products`
            : "No products found";

    const updateDateFilter = (key, value) => {
        updateFilters({ [key]: value, created: "" });
    };

    const statusFilterValue = isTrash ? "trash" : (filters.nav ?? "Active");
    const updateStatusFilter = (value) => {
        if (value === "trash") {
            updateFilters({ take: "trash", nav: "" });
            return;
        }

        updateFilters({ take: "", nav: value });
    };

    const hasActiveToolbarFilter =
        isTrash ||
        (filters.nav ?? "Active") !== "Active" ||
        (filters.search ?? "") !== "" ||
        (filters.created ?? "") !== "" ||
        (filters.start_date ?? "") !== "" ||
        (filters.end_date ?? "") !== "";

    const resetToolbarFilters = () => {
        setSearchTerm("");
        updateFilters({
            take: "",
            nav: "Active",
            search: "",
            created: "",
            start_date: "",
            end_date: "",
        });
    };

    return (
        <AppLayout
            title={t("Products")}
            header={
                <PageHeader>{t("Products")}<br />
                    <div>
                        <NavLink href={route("vendor.products.view")} active={route().current("vendor.products.*")}>{t("Your Product")}</NavLink>
                        {isReseller ? (
                            <NavLink
                                href={route("reseller.resel-product.index")}
                                active={route().current("reseller.resel-product.*")}
                            >{t("Reseller Product")}</NavLink>
                        ) : null}
                    </div>
                </PageHeader>
            }
        >
            <Head title={t("Products")} />

            <Container>
                <Section>
                    <SectionHeader title={t("Your Products")} content={t("Your have product to resel")} />
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
                                            <NavLink href={route("vendor.products.view", buildQuery(filters, { nav: "Active", take: "" }))} active={(filters.nav ?? "Active") === "Active" && !filters.take}>{t("Active")}</NavLink>
                                            <NavLink href={route("vendor.products.view", buildQuery(filters, { take: "trash", nav: "" }))} active={isTrash}>{t("Trash")}</NavLink>
                                        </div>
                                    ) : isTrash ? (
                                        <PrimaryButton type="button" onClick={() => postBulkAction("vendor.products.bulk-restore")}>{t("Restore")}</PrimaryButton>
                                    ) : (
                                        <PrimaryButton type="button" onClick={() => postBulkAction("vendor.products.bulk-trash")}>{t("Move to Trash")}</PrimaryButton>
                                    )}
                                </div>

                                <div className="flex flex-wrap items-center justify-end gap-2">
                                    <select
                                        value={statusFilterValue}
                                        onChange={(e) => updateStatusFilter(e.target.value)}
                                        className="w-36 rounded-md border-gray-300 py-1 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        aria-label="Status"
                                    >
                                        <option value="Active">{t("Active")}</option>
                                        <option value="In Active">{t("Disable")}</option>
                                        <option value="trash">{t("Trash")}</option>
                                    </select>
                                    <input
                                        type="date"
                                        value={filters.start_date ?? ""}
                                        onChange={(e) => updateDateFilter("start_date", e.target.value)}
                                        className="w-36 rounded-md border-gray-300 py-1 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        aria-label="Start date"
                                    />
                                    <input
                                        type="date"
                                        value={filters.end_date ?? ""}
                                        onChange={(e) => updateDateFilter("end_date", e.target.value)}
                                        className="w-36 rounded-md border-gray-300 py-1 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        aria-label="End date"
                                    />
                                    <TextInput
                                        type="search"
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                        placeholder={t("Search by name")}
                                        className="hidden lg:block py-1"
                                    />
                                    {hasActiveToolbarFilter ? (
                                        <PrimaryButton type="button" onClick={resetToolbarFilters}>
                                            {t("Reset")}
                                        </PrimaryButton>
                                    ) : null}
                                    <PrimaryButton type="button" className="mr-2" onClick={() => window.open(printUrl, "_blank")}>
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                        content=""
                    />
                    <SectionInner>
                        <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>#</th>
                                        <th>{t("Product")}</th>
                                        <th>{t("Stock")}</th>
                                        <th>{t("Build Cost")}</th>
                                        <th>{t("Price")}</th>
                                        <th>{t("Discount")}</th>
                                        <th>{t("Order")}</th>
                                        <th>{t("Status")}</th>
                                        <th>{t("Insert At")}</th>
                                        <th>{t("A/C")}</th>
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
                                                    <div>
                                                        <div><ProductName value={product.name} /></div>
                                                        {product.has_pending ? (
                                                            <span
                                                                title={`Pending Order #${product.first_order_id ?? ""}`}
                                                                className="inline-flex rounded bg-red-900 px-1 text-xs text-white"
                                                            >
                                                                {product.first_order_id ?? "N/A"}
                                                            </span>
                                                        ) : null}
                                                        {product.has_accept ? (
                                                            <span
                                                                title={`Accept Order #${product.first_order_id ?? ""}`}
                                                                className="ml-1 inline-flex rounded bg-green-900 px-1 text-xs text-white"
                                                            >
                                                                {product.first_order_id ?? "N/A"}
                                                            </span>
                                                        ) : null}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{product.unit}</td>
                                            <td>{formatCurrency(product.buying_price)}</td>
                                            <td>{formatCurrency(product.price)}</td>
                                            <td>{formatCurrency(product.discount)}</td>
                                            <td>{product.orders_count ?? 0}</td>
                                            <td>{product.status}</td>
                                            <td>{product.created_at_human}</td>
                                            <td>
                                                <ActionIconLink href={route("vendor.products.edit", { product: product.encrypted_id })} action="view" title="View" />
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
                    </SectionInner>
                </Section>
            </Container>

        </AppLayout>
    );
}
