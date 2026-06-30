import { Head, router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../Layouts/App";
import Hr from "../../../components/Hr";
import NavLink from "../../../components/NavLink";
import PrimaryButton from "../../../components/PrimaryButton";
import SecondaryButton from "../../../components/SecondaryButton";
import ApplicationName from "../../../components/ApplicationName";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import ReselProductCart from "../../../components/dashboard/reseller/ReselProductCart";

export default function Shops({
    filters = {},
    shops = { data: [], links: [] },
    selectedShop = null,
    products = null,
    printUrl,
}) {

    const { auth } = usePage().props;
    const [q, setQ] = useState(filters.q ?? "");
    const [location, setLocation] = useState(filters.location ?? "");
    const [get, setGet] = useState(filters.get ?? "");
    const userLocation =
        auth?.user?.city || auth?.user?.state || auth?.user?.country || "";

    useEffect(() => {
        setQ(filters.q ?? "");
        setLocation(filters.location ?? "");
        setGet(filters.get ?? "");
    }, [filters.q, filters.location, filters.get]);

    useEffect(() => {
        const timeout = setTimeout(() => {
            const locationChanged = location !== (filters.location ?? "");
            const nextState = locationChanged ? "" : (filters.state || undefined);
            const nextParams = {
                q: q || undefined,
                location: location || undefined,
                state: nextState,
                get: get || undefined,
            };

            const sameAsCurrent =
                (filters.q ?? "") === (q ?? "") &&
                (filters.location ?? "") === (location ?? "") &&
                !locationChanged &&
                (filters.get ?? "") === (get ?? "");

            if (sameAsCurrent) {
                return;
            }

            router.get(
                route("shops"),
                nextParams,
                {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                }
            );
        }, 1000);

        return () => clearTimeout(timeout);
    }, [q, location, get, filters.q, filters.location, filters.state, filters.get]);

    const getShopByMyLocation = () => {
        router.get(
            route("shops"),
            { location: userLocation, state: "me", q: "", get: undefined },
            { preserveState: true, preserveScroll: true }
        );
    };

    const getAllShops = () => {
        router.get(
            route("shops"),
            { location: "Bangladesh", state: "all", q: "", get: undefined },
            { preserveState: true, preserveScroll: true }
        );
    };

    const applySearch = () => {
        const locationChanged = location !== (filters.location ?? "");

        router.get(
            route("shops"),
            {
                q: q || undefined,
                location: location || undefined,
                state: locationChanged ? undefined : (filters.state || undefined),
                get: get || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    };

    const cleanLabel = (label) =>
        String(label)
            .replace(/&laquo;/g, "")
            .replace(/&raquo;/g, "")
            .trim();

    const pagination = useMemo(() => {
        const links = shops?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [shops?.links]);

    const productPagination = useMemo(() => {
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

        router.visit(url, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    return (
        <AppLayout title="Vendor Shops" header={<PageHeader>Vendor Shops</PageHeader>}>
            <Head title="Vendor Shops" />

            <Container>
                <div className="space-y-2 md:flex md:items-center md:justify-between">
                    <div className="flex flex-wrap items-center justify-start py-3">
                        <NavLink href="/">
                            <i className="fas fa-home pe-2"></i>
                        </NavLink>
                        <NavLink href={route("shops")}>
                            <ApplicationName />
                            <div className="px-2">Shops</div>
                        </NavLink>
                    </div>

                    <div className="ms-auto flex w-full flex-col gap-2 rounded-md bg-white/70 p-2 shadow-sm md:max-w-2xl md:flex-row md:items-center md:justify-end md:bg-transparent md:p-0 md:shadow-none">
                        <div className="flex flex-col gap-2 shrink-0 sm:flex-row sm:flex-wrap sm:items-center">
                            {auth?.user ? (
                                <PrimaryButton
                                    type="button"
                                    onClick={getShopByMyLocation}
                                    className="inline-flex h-10 w-auto items-center justify-center whitespace-nowrap bg-orange-500 px-3 py-1 text-xs uppercase tracking-wide text-white hover:bg-orange-600"
                                >
                                    My Location ({userLocation || "ANY"}){" "}
                                    <i className="ps-1 fas fa-location"></i>
                                </PrimaryButton>
                            ) : null}
                            <SecondaryButton
                                type="button"
                                onClick={getAllShops}
                                className="inline-flex h-10 w-auto items-center justify-center whitespace-nowrap px-3 py-1 text-xs uppercase tracking-wide"
                            >
                                All Shops
                            </SecondaryButton>
                        </div>
                        <input
                            type="search"
                            value={q}
                            onChange={(e) => setQ(e.target.value)}
                            onKeyDown={(e) => {
                                if (e.key !== "Enter") {
                                    return;
                                }

                                e.preventDefault();
                                applySearch();
                            }}
                            className="h-10 w-full rounded border border-gray-300 px-3 py-1 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200"
                            placeholder={selectedShop ? "Search products..." : "Search shops, state, city or town..."}
                            style={{ minWidth: 0, fontSize: '16px' }}
                        />
                        <PrimaryButton
                            type="button"
                            className="inline-flex w-auto justify-center self-start md:ms-1"
                            onClick={() => window.open(printUrl, "_blank")}
                        >
                            <i className="fas fa-print"></i>
                        </PrimaryButton>
                    </div>
                </div>

                {selectedShop ? (
                    <div>
                        <div className="overflow-hidden bg-white">
                            <div className="relative">
                                {selectedShop.banner_url ? (
                                    <img
                                        className="w-full h-48 bg-indigo-900"
                                        src={selectedShop.banner_url}
                                        alt=""
                                    />
                                ) : null}
                                {selectedShop.logo_url ? (
                                    <img
                                        className="absolute top-0 right-0 m-2 bg-white rounded-full"
                                        style={{ height: "80px", width: "80px" }}
                                        src={selectedShop.logo_url}
                                        alt=""
                                    />
                                ) : null}
                            </div>
                            <Container>
                                <div>
                                    <div className="grid grid-cols-1 gap-3 py-3 sm:grid-cols-2">
                                        <div className="w-full rounded-lg border p-2">
                                            <p>Shop</p>
                                            <div>{selectedShop.shop_name_en}</div>
                                            <p className="text-xs">
                                                {selectedShop.village},{" "}
                                                {selectedShop.upozila},{" "}
                                                {selectedShop.district}
                                            </p>
                                            <div className="py-3">
                                                <div className="flex items-center">
                                                    <i className="text-indigo-900 fas fa-star"></i>
                                                    <i className="text-indigo-900 fas fa-star"></i>
                                                    <i className="text-indigo-900 fas fa-star"></i>
                                                    <i className="text-indigo-900 fas fa-star"></i>
                                                    <i className="fas fa-star"></i>
                                                </div>
                                            </div>
                                            <div className="flex items-center justify-between mt-2 space-x-2 space-y-2">
                                                <div className="inline-block px-2 text-xs text-white rounded-lg bg-sky-900">
                                                    Vendor
                                                </div>
                                            </div>
                                        </div>

                                        <div className="w-full rounded-lg border p-2">
                                            <p>Owner</p>
                                            <div className="text-md">
                                                {selectedShop.user?.name ?? "N/A"}
                                            </div>
                                            <p className="text-xs">
                                                <i className="pr-3 fas fa-caret-right"></i>{" "}
                                                {selectedShop.email}
                                            </p>
                                            <p className="text-xs">
                                                <i className="pr-3 fas fa-caret-right"></i>{" "}
                                                {selectedShop.phone}
                                            </p>
                                            <p className="text-xs">
                                                {selectedShop.user?.village},{" "}
                                                {selectedShop.user?.upozila},{" "}
                                                {selectedShop.user?.district}
                                            </p>
                                        </div>
                                    </div>

                                    <Hr />
                                    <div className="flex flex-wrap items-center justify-center gap-3">
                                        <div>
                                            <i className="fas fa-heart"></i>
                                        </div>
                                        <NavLink
                                            href={route("shops", {
                                                get: selectedShop.id,
                                                slug: selectedShop.shop_name_en,
                                            })}
                                        >
                                            Visit Shop <i className="px-2 fas fa-angle"></i>
                                        </NavLink>
                                    </div>
                                </div>
                            </Container>
                        </div>

                        <div className="my-[100]">
                            <div className="w-full product_section ">
                                <div className="py-2 text-sm">Products</div>
                                {products ? (
                                    <>
                                        <div
                                            className="grid grid-cols-2 gap-2 sm:gap-3 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-7"
                                        >
                                            {(products.data ?? []).map((pd) => (
                                                <ReselProductCart
                                                    key={pd.id}
                                                    product={pd}
                                                />
                                            ))}
                                        </div>
                                        {productPagination.pages.length ? (
                                            <div className="w-full pt-4">
                                                <div className="flex w-full flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                                    <div className="text-sm text-slate-700">
                                                        {products?.total > 0
                                                            ? `Showing ${products?.from ?? 0}-${products?.to ?? 0} of ${products?.total ?? 0} products`
                                                            : "No products found"}
                                                    </div>
                                                    <div className="flex w-full justify-start md:w-auto md:justify-end">
                                                        <div className="inline-flex flex-nowrap overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                                            <button
                                                                type="button"
                                                                disabled={!productPagination.prev?.url}
                                                                className="whitespace-nowrap border-r border-slate-200 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                                onClick={() => goToPage(productPagination.prev?.url)}
                                                            >
                                                                Previous
                                                            </button>
                                                            {productPagination.pages.map((link, idx) => (
                                                                <button
                                                                    key={`${link.label}-${idx}`}
                                                                type="button"
                                                                disabled={!link.url}
                                                                className={`min-w-10 whitespace-nowrap border-r border-slate-200 px-4 py-2 text-sm font-semibold transition ${
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
                                                                disabled={!productPagination.next?.url}
                                                                className="whitespace-nowrap px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                                onClick={() => goToPage(productPagination.next?.url)}
                                                            >
                                                                Next
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ) : null}
                                    </>
                                ) : null}
                            </div>
                        </div>
                    </div>
                ) : (
                    <>
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                            {(shops.data ?? []).length > 0 ? (
                                shops.data.map((shop) => (
                                    <div key={shop.id}>
                                        <div className="overflow-hidden bg-white rounded-lg shadow">
                                            <div className="relative p-3">
                                                {shop.logo_url ? (
                                                    <img
                                                        className="absolute object-cover bg-white rounded-full top-2 right-2"
                                                        style={{ height: "50px", width: "50px" }}
                                                        src={shop.logo_url}
                                                        alt={shop.shop_name_en}
                                                    />
                                                ) : null}
                                                <div className="pr-16">{shop.shop_name_en}</div>
                                                <p className="pr-16 text-xs">
                                                    {shop.village}, {shop.upozila},{" "}
                                                    {shop.district}
                                                </p>
                                                <div className="py-3">
                                                    <div className="flex items-center">
                                                        <i className="text-indigo-900 fas fa-star"></i>
                                                        <i className="text-indigo-900 fas fa-star"></i>
                                                        <i className="text-indigo-900 fas fa-star"></i>
                                                        <i className="text-indigo-900 fas fa-star"></i>
                                                        <i className="fas fa-star"></i>
                                                    </div>
                                                </div>
                                                <div className="flex items-center justify-between mt-2 space-x-2 space-y-2">
                                                    <div className="inline-block px-2 text-xs text-white rounded-lg bg-sky-900">
                                                        vendor
                                                    </div>
                                                </div>
                                                <Hr />
                                                <div className="flex justify-between">
                                                    <div>
                                                        <i className="fas fa-heart"></i>
                                                    </div>
                                                    <NavLink
                                                        href={route("shops", {
                                                            get: shop.id,
                                                            slug: shop.shop_name_en,
                                                        })}
                                                    >
                                                        Visit Shop <i className="px-2 fas fa-angle"></i>
                                                    </NavLink>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <p>No Shops Found !</p>
                            )}
                        </div>
                        {pagination.pages.length ? (
                            <div className="w-full pt-4">
                                <div className="flex w-full flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div className="text-sm text-slate-700">
                                        {shops?.total > 0
                                            ? `Showing ${shops?.from ?? 0}-${shops?.to ?? 0} of ${shops?.total ?? 0} shops`
                                            : "No shops found"}
                                    </div>
                                    <div className="flex w-full justify-start md:w-auto md:justify-end">
                                        <div className="inline-flex flex-nowrap overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                            <button
                                                type="button"
                                                disabled={!pagination.prev?.url}
                                                className="whitespace-nowrap border-r border-slate-200 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pagination.prev?.url)}
                                            >
                                                Previous
                                            </button>
                                            {pagination.pages.map((link, idx) => (
                                                <button
                                                    key={`${link.label}-${idx}`}
                                                type="button"
                                                disabled={!link.url}
                                                className={`min-w-10 whitespace-nowrap border-r border-slate-200 px-4 py-2 text-sm font-semibold transition ${
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
                                                className="whitespace-nowrap px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pagination.next?.url)}
                                            >
                                                Next
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ) : null}
                    </>
                )}

            </Container>
        </AppLayout>
    );
}
