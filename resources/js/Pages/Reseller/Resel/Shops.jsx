import { Head, router, usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
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

    return (
        <AppLayout title="Vendor Shops" header={<PageHeader>Vendor Shops</PageHeader>}>
            <Head title="Vendor Shops" />

            <Container>
                <div className="md:flex justify-between items-center space-y-2">
                    <div className="flex justify-start items-center py-3">
                        <NavLink href="/">
                            <i className="fas fa-home pe-2"></i>
                        </NavLink>
                        <NavLink href={route("shops")}>
                            <ApplicationName />
                            <div className="px-2">Shops</div>
                        </NavLink>
                    </div>

                    <div className="flex items-center">
                        <input
                            type="search"
                            value={q}
                            onChange={(e) => setQ(e.target.value)}
                            className="py-1 rounded-md"
                            placeholder="search shops by name"
                        />
                        <div>
                            {auth?.user ? (
                                <button
                                    type="button"
                                    onClick={() => setShowModal(true)}
                                    className="py-2 px-3 text-xs ms-1 border rounded bg-white"
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
                        <div className="bg-white overflow-hidden">
                            <div className="relative">
                                {selectedShop.banner_url ? (
                                    <img
                                        className="w-full bg-indigo-900 h-48"
                                        src={selectedShop.banner_url}
                                        alt=""
                                    />
                                ) : null}
                                {selectedShop.logo_url ? (
                                    <img
                                        className="rounded-full absolute left-0 top-0 bg-white m-2"
                                        style={{ height: "80px", width: "80px" }}
                                        src={selectedShop.logo_url}
                                        alt=""
                                    />
                                ) : null}
                            </div>
                            <Container>
                                <div>
                                    <div className="flex flex-wrap gaps-10">
                                        <div className="w-48 m-1 border p-2 rounded-lg">
                                            <p>Shop</p>
                                            <div>{selectedShop.shop_name_en}</div>
                                            <p className="text-xs">
                                                {selectedShop.village},{" "}
                                                {selectedShop.upozila},{" "}
                                                {selectedShop.district}
                                            </p>
                                            <div className="py-3">
                                                <div className="flex items-center">
                                                    <i className="fas fa-star text-indigo-900"></i>
                                                    <i className="fas fa-star text-indigo-900"></i>
                                                    <i className="fas fa-star text-indigo-900"></i>
                                                    <i className="fas fa-star text-indigo-900"></i>
                                                    <i className="fas fa-star"></i>
                                                </div>
                                            </div>
                                            <div className="mt-2 flex justify-between items-center space-x-2 space-y-2">
                                                <div className="px-2 bg-sky-900 text-white rounded-lg inline-block text-xs">
                                                    reseller
                                                </div>
                                            </div>
                                        </div>

                                        <div className="w-48 m-1 border p-2 rounded-lg">
                                            <p>Owner</p>
                                            <div className="text-md">
                                                {selectedShop.user?.name ?? "N/A"}
                                            </div>
                                            <p className="text-xs">
                                                <i className="fas fa-caret-right pr-3"></i>{" "}
                                                {selectedShop.email}
                                            </p>
                                            <p className="text-xs">
                                                <i className="fas fa-caret-right pr-3"></i>{" "}
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
                                                name: selectedShop.shop_name_en,
                                            })}
                                        >
                                            Visit Shops <i className="fas fa-angle px-2"></i>
                                        </NavLink>
                                    </div>
                                </div>
                            </Container>
                        </div>

                        <div className="my-[100]">
                            <div className="product_section w-full md:w-3/4">
                                <div className="text-sm py-2">Products</div>
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
                                        <div className="mt-2 flex flex-wrap gap-2">
                                            {(products.links ?? []).map(
                                                (link, idx) => (
                                                    <button
                                                        key={idx}
                                                        type="button"
                                                        disabled={!link.url}
                                                        onClick={() =>
                                                            link.url &&
                                                            router.visit(
                                                                link.url,
                                                                {
                                                                    preserveScroll: true,
                                                                    preserveState: true,
                                                                }
                                                            )
                                                        }
                                                        className={`px-3 py-1 border rounded ${
                                                            link.active
                                                                ? "bg-gray-900 text-white"
                                                                : "bg-white"
                                                        }`}
                                                    >
                                                        {cleanLabel(link.label)}
                                                    </button>
                                                )
                                            )}
                                        </div>
                                    </>
                                ) : null}
                            </div>
                        </div>
                    </div>
                ) : (
                    <>
                        <div className="mt-2 flex flex-wrap gap-2">
                            {(shops.links ?? []).map((link, idx) => (
                                <button
                                    key={idx}
                                    type="button"
                                    disabled={!link.url}
                                    onClick={() =>
                                        link.url &&
                                        router.visit(link.url, {
                                            preserveScroll: true,
                                            preserveState: true,
                                        })
                                    }
                                    className={`px-3 py-1 border rounded ${
                                        link.active
                                            ? "bg-gray-900 text-white"
                                            : "bg-white"
                                    }`}
                                >
                                    {cleanLabel(link.label)}
                                </button>
                            ))}
                        </div>

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
                                        <div className="bg-white rounded-lg shadow overflow-hidden">
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
                                                        className="rounded-full absolute left-0 top-0 bg-white m-2"
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
                                                        <i className="fas fa-star text-indigo-900"></i>
                                                        <i className="fas fa-star text-indigo-900"></i>
                                                        <i className="fas fa-star text-indigo-900"></i>
                                                        <i className="fas fa-star text-indigo-900"></i>
                                                        <i className="fas fa-star"></i>
                                                    </div>
                                                </div>
                                                <div className="mt-2 flex justify-between items-center space-x-2 space-y-2">
                                                    <div className="px-2 bg-sky-900 text-white rounded-lg inline-block text-xs">
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
                                                        Visit Shops{" "}
                                                        <i className="fas fa-angle px-2"></i>
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
                    </>
                )}

                <Modal show={showModal} onClose={() => setShowModal(false)} maxWidth="sm">
                    <div className="p-3">
                        <p className="text-xs">
                            Shop will be displayed based on you expectation.
                            From where you want to get the shop.
                        </p>
                        <br />
                        <div className="text-center space-y-3">
                            {auth?.user ? (
                                <PrimaryButton
                                    type="button"
                                    onClick={getShopByMyLocation}
                                    className="p-3 flex justify-center items-center bg-indigo-300 text-white w-full rounded"
                                >
                                    My Location ({auth.user.city}){" "}
                                    <i className="px-2 fas fa-location"></i>
                                </PrimaryButton>
                            ) : null}

                            <SecondaryButton
                                type="button"
                                onClick={getAllShops}
                                className="p-3 w-full flex justify-center items-centere"
                            >
                                All Shops
                            </SecondaryButton>

                            <div className="p-2 rounded bg-gray-200">
                                <input
                                    type="search"
                                    value={location}
                                    onChange={(e) => setLocation(e.target.value)}
                                    id="find_shop"
                                    className="py-1 w-full rounded mb-1"
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
