import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../Layouts/App";
import NavLink from "../../../components/NavLink";
import NavLinkBtn from "../../../components/NavLinkBtn";
import PrimaryButton from "../../../components/PrimaryButton";
import TextInput from "../../../components/TextInput";
import PageHeader from "../../../components/dashboard/PageHeader";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";
import ProductName from "../../../components/ProductName";
import useTranslation from "../../../hooks/useTranslation";
import { ActionIconLink } from "../../../components/ActionIcon";
import { formatCurrency } from "../../../utils/formatAmount";

export default function Index({ products, filters, printUrl }) {
    const { t } = useTranslation();
    const [nav, setNav] = useState(filters?.nav ?? "own");
    const [pd, setPd] = useState(filters?.pd ?? "Active");
    const [search, setSearch] = useState(filters?.search ?? "");

    const requestProducts = (overrides = {}, options = {}) => {
        const nextNav = overrides.nav ?? nav;
        const nextPd = overrides.pd ?? pd;
        const nextSearch = overrides.search ?? search.trim();

        router.get(
            route("reseller.products.list"),
            {
                nav: nextNav,
                pd: nextPd,
                search: nextSearch || undefined,
                page: overrides.page ?? undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                only: ["filters", "products", "printUrl"],
                ...options,
            }
        );
    };

    useEffect(() => {
        setNav(filters?.nav ?? "own");
        setPd(filters?.pd ?? "Active");
        setSearch(filters?.search ?? "");
    }, [filters?.nav, filters?.pd, filters?.search]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters?.search ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            requestProducts({ search: trimmedSearch });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search, filters?.search]);

    const cleanLabel = (label) =>
        String(label)
            .replace(/&laquo;/g, "")
            .replace(/&raquo;/g, "")
            .trim();

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

        const nextUrl = new URL(url, window.location.origin);

        requestProducts({
            nav: nextUrl.searchParams.get("nav") ?? nav,
            pd: nextUrl.searchParams.get("pd") ?? pd,
            search: nextUrl.searchParams.get("search") ?? search.trim(),
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const resultSummary =
        products?.total > 0
            ? `Showing ${products?.from ?? 0}-${products?.to ?? 0} of ${products?.total ?? 0} products`
            : "No products found";

    return (
        <AppLayout
            title={t("Products")}
            header={
                <PageHeader>
                    <div className="flex items-start justify-between">{t("Products")}<div className="flex space-x-1">
                            <NavLinkBtn href={route("vendor.products.create")}>
                                <i className="pr-2 fas fa-plus"></i> New
                            </NavLinkBtn>
                            <NavLinkBtn href={route("reseller.resel-product.index")}>
                                Recel from vendor
                            </NavLinkBtn>
                        </div>
                    </div>
                    <br />

                    <NavLink
                        href={route("reseller.products.list", { nav: "own" })}
                        active={nav === "own"}
                    >{t("Your Product")}</NavLink>
                    <NavLink
                        href={route("reseller.products.list", { nav: "resel" })}
                        active={nav === "resel"}
                    >{t("Resel Product")}</NavLink>
                </PageHeader>
            }
        >
            <Head title={t("Products")} />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-end gap-2">
                                <TextInput
                                    type="search"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={(e) => {
                                        if (e.key !== "Enter") {
                                            return;
                                        }

                                        e.preventDefault();
                                        requestProducts({ search: search.trim() });
                                    }}
                                    placeholder={t("Search products...")}
                                    className="py-1"
                                    />
                                <PrimaryButton
                                    type="button"
                                    onClick={() => window.open(printUrl, "_blank")}
                                >
                                    <i className="fas fa-print"></i>
                                </PrimaryButton>
                            </div>
                        }
                        content={
                            <div className="flex items-center justify-between">
                                <div>
                                    <NavLink
                                        href={route("reseller.products.list", {
                                            nav,
                                            pd: "Active",
                                            search: search || undefined,
                                        })}
                                        active={pd === "Active"}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            setPd("Active");
                                            requestProducts({
                                                nav,
                                                pd: "Active",
                                                page: undefined,
                                            });
                                        }}
                                    >{t("Active")}</NavLink>
                                    <NavLink
                                        href={route("reseller.products.list", {
                                            nav,
                                            pd: "Trash",
                                            search: search || undefined,
                                        })}
                                        active={pd === "Trash"}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            setPd("Trash");
                                            requestProducts({
                                                nav,
                                                pd: "Trash",
                                                page: undefined,
                                            });
                                        }}
                                    >{t("Trash")}</NavLink>
                                </div>
                            </div>
                        }
                    />
                    <SectionInner>
                        <Table data={products?.data ?? []}>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>{t("Product")}</th>
                                    <th>{t("In Stock")}</th>
                                    <th>{t("Name")}</th>
                                    <th>{t("Status")}</th>
                                    <th>{t("Order")}</th>
                                    <th>{t("Cost")}</th>
                                    <th>{t("Price")}</th>
                                    <th>{t("Sel Price")}</th>
                                    <th>{t("Insert At")}</th>
                                    <th>{t("A/C")}</th>
                                </tr>
                            </thead>

                            <tbody>
                                {(products?.data ?? []).map((product, index) => (
                                    <tr key={product.id}>
                                        <td>
                                            <input
                                                type="checkbox"
                                                className="rounded"
                                                value={product.id}
                                                style={{ width: 20, height: 20 }}
                                                onChange={() => {}}
                                            />
                                        </td>
                                        <td>{index + 1}</td>
                                        <td>
                                            <div className="flex items-start">
                                                <img
                                                    className="w-8 h-8 rounded-md shadow"
                                                    src={product.thumbnail ? `/storage/${product.thumbnail}` : ""}
                                                    alt=""
                                                />
                                            </div>
                                        </td>
                                        <td>{product.unit}</td>
                                        <td>
                                            <p><ProductName value={product.name} /></p>
                                            {product.has_pending && (
                                                <a
                                                    title={`Pending Order #${product.first_order_id ?? ""}`}
                                                    className="inline-flex block px-1 mr-1 text-xs text-white bg-red-900 rounded"
                                                >
                                                    {product.first_order_id ?? "N\\A"}
                                                </a>
                                            )}
                                            {product.has_accept && (
                                                <a
                                                    title={`Accept Order #${product.first_order_id ?? ""}`}
                                                    className="inline-flex block px-1 mr-1 text-xs text-white bg-green-900 rounded"
                                                >
                                                    {product.first_order_id ?? "N\\A"}
                                                </a>
                                            )}
                                        </td>
                                        <td>{product.status_label}</td>
                                        <td>{product.orders_count}</td>
                                        <td>{formatCurrency(product.buying_price)}</td>
                                        <td>{formatCurrency(product.price)}</td>
                                        <td>{formatCurrency(product.offer_type ? product.discount : product.price)}</td>
                                        <td>{product.created_at_human}</td>
                                        <td>
                                            <ActionIconLink
                                                href={route("reseller.products.edit", {
                                                    id: product.encrypted_id,
                                                })}
                                                action="edit"
                                                title={t("edit")}
                                            />
                                        </td>
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
                                                    {cleanLabel(link.label)}
                                                </button>
                                            ))}
                                            <button
                                                type="button"
                                                disabled={!pagination.next?.url}
                                                className="px-4 py-2 text-sm transition text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
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
