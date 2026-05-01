import { router, usePage } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import ApplicationName from "@/components/ApplicationName";
import Modal from "@/components/Modal";
import NavLink from "@/components/NavLink";
import PrimaryButton from "@/components/PrimaryButton";
import SecondaryButton from "@/components/SecondaryButton";
import Container from "@/components/dashboard/Container";
import ShopsCart from "@/components/client/ShopsCart";
import HeroSlider from "@/components/home/HeroSlider";
import UserLayout from "@/Layouts/User/App";

export default function Index({
    slides = [],
    shops,
    filters = {},
    showFiltered = false,
}) {
    const { auth } = usePage().props;
    const [q, setQ] = useState(filters.q ?? "");
    const [location, setLocation] = useState(filters.location ?? "");
    const [showModal, setShowModal] = useState(false);
    const [shopItems, setShopItems] = useState(shops?.data ?? []);
    const [pagination, setPagination] = useState(shops ?? {});
    const [loadingMore, setLoadingMore] = useState(false);
    const loadingMoreRef = useRef(false);

    useEffect(() => {
        setQ(filters.q ?? "");
        setLocation(filters.location ?? "");
    }, [filters.q, filters.location]);

    useEffect(() => {
        if (loadingMoreRef.current) {
            return;
        }

        setShopItems(shops?.data ?? []);
        setPagination(shops ?? {});
    }, [shops?.current_page, filters.q, filters.location, filters.state]);

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (
                q === (filters.q ?? "") &&
                location === (filters.location ?? "")
            ) {
                return;
            }

            router.get(
                route("shops.reseller"),
                { q, location, state: filters.state ?? "" },
                {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                },
            );
        }, 400);

        return () => clearTimeout(timeout);
    }, [q, location, filters.q, filters.location, filters.state]);

    const getShopByMyLocation = () => {
        const city = auth?.user?.city ?? "";

        router.get(
            route("shops.reseller"),
            { location: city, state: "me", q: "" },
            { preserveState: true, preserveScroll: true },
        );
    };

    const getAllShops = () => {
        router.get(
            route("shops.reseller"),
            { location: "", state: "all", q: "" },
            { preserveState: true, preserveScroll: true },
        );
    };

    const loadMore = () => {
        if (!pagination?.next_page_url || loadingMore) {
            return;
        }

        loadingMoreRef.current = true;
        setLoadingMore(true);

        router.visit(pagination.next_page_url, {
            preserveScroll: true,
            preserveState: true,
            only: ["shops", "filters", "showFiltered"],
            onSuccess: (page) => {
                const nextShops = page.props.shops ?? {};

                setShopItems((current) => [
                    ...current,
                    ...(nextShops.data ?? []),
                ]);
                setPagination(nextShops);
            },
            onFinish: () => {
                setLoadingMore(false);
                loadingMoreRef.current = false;
            },
        });
    };

    return (
        <UserLayout title="Shops">
            <HeroSlider slides={slides} />

            <div className="py-4">
                <div>
                    <div className="w-auto w-full mb-3 text-3xl text-center heading_center">
                        <h2 className="flex justify-center gap-3">
                            <ApplicationName />
                            <span className="font-bold text-green-900">Shops</span>
                        </h2>
                    </div>
                </div>
            </div>

            <Container>
                <div className="items-center justify-between space-y-2 md:flex">
                    <div className="flex items-center justify-start py-3">
                        <NavLink href="/">
                            <i className="fas fa-home pe-2"></i>
                        </NavLink>

                        <NavLink href={route("shops.reseller")}>
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

                {!auth?.user && (
                    <div className="w-full p-1 text-center bg-gray-200">
                        Login to get access the shops based on your location.
                    </div>
                )}

                <div>
                    <p>
                        Showing {shopItems.length} of {pagination?.total ?? 0} shops
                    </p>

                    <div
                        style={{
                            display: "grid",
                            gridTemplateColumns: "repeat(auto-fit, 300px)",
                            justifyContent: "start",
                            alignItems: "start",
                            gridGap: "10px",
                        }}
                    >
                        {shopItems.length > 0 ? (
                            shopItems.map((shop) => (
                                <ShopsCart key={shop.id} shop={shop} />
                            ))
                        ) : (
                            <p>No Shops Found !</p>
                        )}
                    </div>

                    {pagination?.next_page_url && (
                        <div className="py-6 text-center">
                            <PrimaryButton
                                type="button"
                                onClick={loadMore}
                                disabled={loadingMore}
                            >
                                {loadingMore ? "Loading..." : "Load More"}
                            </PrimaryButton>
                        </div>
                    )}
                </div>

                <Modal show={showModal} onClose={() => setShowModal(false)}>
                    <div className="p-3">
                        <p className="text-xs">
                            Shop will be displayed based on you expectation. From where you want to get the shop.
                        </p>

                        <br />

                        <div className="space-y-3 text-center">
                            {auth?.user && (
                                <PrimaryButton
                                    onClick={getShopByMyLocation}
                                    className="flex items-center justify-center w-full p-3 text-white bg-indigo-300 rounded"
                                >
                                    My Location ({auth.user.city})
                                    <i className="px-2 fas fa-location"></i>
                                </PrimaryButton>
                            )}

                            <SecondaryButton
                                onClick={getAllShops}
                                className="flex justify-center w-full p-3 items-centere"
                            >
                                All Shops
                            </SecondaryButton>

                            <div className="p-2 bg-gray-200 rounded">
                                <input
                                    type="search"
                                    id="find_shop"
                                    value={location}
                                    onChange={(e) => setLocation(e.target.value)}
                                    className="w-full py-1 mb-1 rounded"
                                    placeholder="search shop by state, city or town"
                                />
                            </div>
                        </div>
                    </div>
                </Modal>
            </Container>
        </UserLayout>
    );
}
