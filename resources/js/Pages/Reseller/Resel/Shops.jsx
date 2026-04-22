import { Head, router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../Layouts/App";
import Modal from "../../../components/Modal";
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
    const [showModal, setShowModal] = useState(false);
    const [get, setGet] = useState(filters.get ?? "");

    useEffect(() => {
        setQ(filters.q ?? "");
        setLocation(filters.location ?? "");
        setGet(filters.get ?? "");
    }, [filters.q, filters.location, filters.get]);

    useEffect(() => {
        const timeout = setTimeout(() => {
            const nextParams = {
                q: q || undefined,
                location: location || undefined,
                state: filters.state || undefined,
                get: get || undefined,
            };

            const sameAsCurrent =
                (filters.q ?? "") === (q ?? "") &&
                (filters.location ?? "") === (location ?? "") &&
                (filters.state ?? "") === (nextParams.state ?? "") &&
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
        }, 400);

        return () => clearTimeout(timeout);
    }, [q, location, get, filters.q, filters.location, filters.state, filters.get]);

    const getShopByMyLocation = () => {
        const city = auth?.user?.city ?? "";
        router.get(
            route("shops"),
            { location: city, state: "me", q: "", get: undefined },
            { preserveState: true, preserveScroll: true }
        );
        setShowModal(false);
    };

    const getAllShops = () => {
        router.get(
            route("shops"),
            { location: "Bangladesh", state: "all", q: "", get: undefined },
            { preserveState: true, preserveScroll: true }
        );
        setShowModal(false);
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
                <div className="items-center justify-between space-y-2 md:flex">
                    <div className="flex items-center justify-start py-3">
                        <NavLink href="/">
                            <i className="fas fa-home pe-2"></i>
                        </NavLink>
                        <NavLink href={route("shops")}>
                            <ApplicationName />
                            <div className="px-2">Shops</div>
                        </NavLink>
                    </div>

                    <div className="flex items-center w-full max-w-xl ms-auto">
                        <input
                            type="search"
                            value={q}
                            onChange={(e) => setQ(e.target.value)}
                            className="w-full px-3 py-1 text-gray-700 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-200"
                            placeholder={selectedShop ? "Search products..." : "Search shops..."}
                            style={{ minWidth: 0, fontSize: '16px' }}
                        />
                        <PrimaryButton
                            type="button"
                            className="ms-1"
                            onClick={() => window.open(printUrl, "_blank")}
                        >
                            <i className="fas fa-print"></i>
                        </PrimaryButton>
                        <div>
                            {auth?.user ? (
                                <button
                                    type="button"
                                    onClick={() => setShowModal(true)}
                                    className="px-3 py-2 text-xs bg-white border rounded ms-1"
                                >
                                    {location || auth.user.city || "ANY"}{" "}
                                    <i className="ps-2 fas fa-chevron-down"></i>
                                </button>
                            ) : (
                                <button
                                    type="button"
                                    onClick={() => setShowModal(true)}
                                    className="px-2"
                                >
                                    <i className="fas fa-location"></i>
                                </button>
                            )}
                        </div>
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
                                        className="absolute top-0 left-0 m-2 bg-white rounded-full"
                                        style={{ height: "80px", width: "80px" }}
                                        src={selectedShop.logo_url}
                                        alt=""
                                    />
                                ) : null}
                            </div>
                            <Container>
                                <div>
                                    <div className="flex flex-wrap gaps-10">
                                        <div className="w-48 p-2 m-1 border rounded-lg">
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
                                                    reseller
                                                </div>
                                            </div>
                                        </div>

                                        <div className="w-48 p-2 m-1 border rounded-lg">
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
                                    <div className="flex justify-center space-x-3">
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
                                            style={{
                                                display: "grid",
                                                justifyContent: "start",
                                                gridTemplateColumns:
                                                    "repeat(auto-fill, 160px)",
                                                gridGap: "10px",
                                            }}
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
                                                <div className="flex items-center justify-between w-full gap-3">
                                                    <div className="text-sm text-slate-700">
                                                        {products?.total > 0
                                                            ? `Showing ${products?.from ?? 0}-${products?.to ?? 0} of ${products?.total ?? 0} products`
                                                            : "No products found"}
                                                    </div>
                                                    <div className="flex items-center md:justify-end">
                                                        <div className="overflow-hidden bg-white border shadow-sm rounded-xl border-slate-200">
                                                            <button
                                                                type="button"
                                                                disabled={!productPagination.prev?.url}
                                                                className="px-4 py-2 text-sm transition border-r border-slate-200 text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                                onClick={() => goToPage(productPagination.prev?.url)}
                                                            >
                                                                Previous
                                                            </button>
                                                            {productPagination.pages.map((link, idx) => (
                                                                <button
                                                                    key={`${link.label}-${idx}`}
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
                                                                disabled={!productPagination.next?.url}
                                                                className="px-4 py-2 text-sm transition text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
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
                        <div
                            style={{
                                display: "grid",
                                gridTemplateColumns: "repeat(auto-fit, 300px)",
                                justifyContent: "start",
                                alignItems: "start",
                                gridGap: "10px",
                            }}
                        >
                            {(shops.data ?? []).length > 0 ? (
                                shops.data.map((shop) => (
                                    <div key={shop.id}>
                                        <div className="overflow-hidden bg-white rounded-lg shadow">
                                            <div className="relative">
                                                {shop.banner_url ? (
                                                    <img
                                                        className="w-full bg-indigo-900"
                                                        style={{ height: "100px" }}
                                                        src={shop.banner_url}
                                                        alt=""
                                                    />
                                                ) : null}
                                                {shop.logo_url ? (
                                                    <img
                                                        className="absolute top-0 left-0 m-2 bg-white rounded-full"
                                                        style={{ height: "50px", width: "50px" }}
                                                        src={shop.logo_url}
                                                        alt=""
                                                    />
                                                ) : null}
                                            </div>
                                            <div className="p-3">
                                                <div>{shop.shop_name_en}</div>
                                                <p className="text-xs">
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
                                <div className="flex items-center justify-between w-full gap-3">
                                    <div className="text-sm text-slate-700">
                                        {shops?.total > 0
                                            ? `Showing ${shops?.from ?? 0}-${shops?.to ?? 0} of ${shops?.total ?? 0} shops`
                                            : "No shops found"}
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
                                            {pagination.pages.map((link, idx) => (
                                                <button
                                                    key={`${link.label}-${idx}`}
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

                <Modal show={showModal} onClose={() => setShowModal(false)} maxWidth="sm">
                    <div className="p-3">
                        <p className="text-xs">
                            Shop will be displayed based on you expectation.
                            From where you want to get the shop.
                        </p>
                        <br />
                        <div className="space-y-3 text-center">
                            {auth?.user ? (
                                <PrimaryButton
                                    type="button"
                                    onClick={getShopByMyLocation}
                                    className="flex items-center justify-center w-full p-3 text-white bg-indigo-300 rounded"
                                >
                                    My Location ({auth.user.city}){" "}
                                    <i className="px-2 fas fa-location"></i>
                                </PrimaryButton>
                            ) : null}

                            <SecondaryButton
                                type="button"
                                onClick={getAllShops}
                                className="flex justify-center w-full p-3 items-centere"
                            >
                                All Shops
                            </SecondaryButton>

                            <div className="p-2 bg-gray-200 rounded">
                                <input
                                    type="search"
                                    value={location}
                                    onChange={(e) => setLocation(e.target.value)}
                                    id="find_shop"
                                    className="w-full py-1 mb-1 rounded"
                                    placeholder="search shop by state, city or town"
                                />
                            </div>
                        </div>
                    </div>
                </Modal>
            </Container>
        </AppLayout>
    );
}
