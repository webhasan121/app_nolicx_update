import { router, usePage } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import ApplicationName from "@/components/ApplicationName";
import NavLink from "@/components/NavLink";
import PrimaryButton from "@/components/PrimaryButton";
import SecondaryButton from "@/components/SecondaryButton";
import Container from "@/components/dashboard/Container";
import ShopsCart from "@/components/client/ShopsCart";
import HeroSlider from "@/components/home/HeroSlider";
import UserLayout from "@/Layouts/User/App";
import useTranslation from "../../hooks/useTranslation";

export default function Index({
    slides = [],
    shops,
    filters = {},
    userLocation = "",
    showFiltered = false,
}) {
    const { t } = useTranslation();
    const { auth } = usePage().props;
    const [q, setQ] = useState(filters.q ?? "");
    const [location, setLocation] = useState(filters.location ?? "");
    const [shopItems, setShopItems] = useState(shops?.data ?? []);
    const [pagination, setPagination] = useState(shops ?? {});
    const [loadingMore, setLoadingMore] = useState(false);
    const loadingMoreRef = useRef(false);
    const fallbackUserLocation =
        auth?.user?.city || auth?.user?.state || auth?.user?.country || "";
    const userLocationName = userLocation || fallbackUserLocation;
    const hasMoreShops =
        Boolean(pagination?.next_page_url) ||
        ((pagination?.current_page ?? 1) < (pagination?.last_page ?? 1));

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
            const locationChanged = location !== (filters.location ?? "");
            const nextState = locationChanged ? "" : (filters.state ?? "");

            if (q === (filters.q ?? "") && !locationChanged) {
                return;
            }

            router.get(
                route("shops.reseller"),
                { q, location, state: nextState },
                {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                },
            );
        }, 900);

        return () => clearTimeout(timeout);
    }, [q, location, filters.q, filters.location, filters.state]);

    const getShopByMyLocation = () => {
        router.get(
            route("shops.reseller"),
            { location: userLocationName, state: "me", q: "" },
            { preserveState: true, preserveScroll: true },
        );
    };

    const handleSearchChange = (event) => {
        setQ(event.target.value);

        if (location !== "") {
            setLocation("");
        }
    };

    const submitSearch = () => {
        router.get(
            route("shops.reseller"),
            { q, location: "", state: "" },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
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
        if (!hasMoreShops || loadingMore) {
            return;
        }

        const nextPageUrl =
            pagination?.next_page_url ??
            route("shops.reseller", {
                q,
                location,
                state: filters.state ?? "",
                page: (pagination?.current_page ?? 1) + 1,
            });

        loadingMoreRef.current = true;
        setLoadingMore(true);

        router.visit(nextPageUrl, {
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
        <UserLayout title={t("Shops")}>
            <HeroSlider slides={slides} />

            <div className="py-4">
                <div>
                    <div className="w-auto w-full mb-3 text-3xl text-center heading_center">
                        <h2 className="flex justify-center gap-3">
                            <ApplicationName />
                            <span className="font-bold text-green-900">{t("Shops")}</span>
                        </h2>
                    </div>
                </div>
            </div>

            <Container>
                <div className="items-center gap-3 space-y-2 md:flex md:space-y-0">
                    <div className="flex items-center justify-start py-3">
                        <NavLink href="/">
                            <i className="fas fa-home pe-2"></i>
                        </NavLink>

                        <NavLink href={route("shops.reseller")}>
                            <ApplicationName />
                            <div className="px-2">{t("Shops")}</div>
                        </NavLink>
                    </div>

                    <div className="flex w-full flex-col gap-2 md:ms-auto md:w-auto md:flex-row md:items-center md:justify-end">
                        <SecondaryButton
                            type="button"
                            onClick={getAllShops}
                            className="flex min-h-9 items-center justify-center whitespace-nowrap px-4 py-2 text-xs md:w-auto"
                        >
                            {t("All Shops")}
                        </SecondaryButton>

                        {auth?.user && (
                            <PrimaryButton
                                type="button"
                                onClick={getShopByMyLocation}
                                className="flex min-h-9 items-center justify-center whitespace-nowrap bg-orange-500 px-4 py-2 text-xs text-white hover:bg-orange-600 md:w-auto"
                            >
                                {t("My Location")} ({userLocationName || "ANY"})
                                <i className="px-2 fas fa-location"></i>
                            </PrimaryButton>
                        )}

                        <input
                            type="search"
                            id="find_shop"
                            value={q}
                            onChange={handleSearchChange}
                            onKeyDown={(event) => {
                                if (event.key === "Enter") {
                                    event.preventDefault();
                                    submitSearch();
                                }
                            }}
                            className="min-h-9 w-full rounded-md border border-gray-300 px-3 py-1 md:w-80"
                            placeholder={t("search shop by state, city or town")}
                        />
                    </div>
                </div>

                {!auth?.user && (
                    <div className="w-full p-1 text-center bg-gray-200">{t("Login to get access the shops based on your location.")}</div>
                )}

                <div>
                    <div
                        className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                    >
                        {shopItems.length > 0 ? (
                            shopItems.map((shop) => (
                                <ShopsCart key={shop.id} shop={shop} />
                            ))
                        ) : (
                            <p>{t("No Shops Found !")}</p>
                        )}
                    </div>

                    {hasMoreShops && shopItems.length >= 20 && (
                        <div className="flex justify-center py-6">
                            <PrimaryButton
                                type="button"
                                onClick={loadMore}
                                disabled={loadingMore}
                                className="px-8 py-3"
                            >
                                {loadingMore ? t("Loading...") : t("Load More")}
                            </PrimaryButton>
                        </div>
                    )}
                </div>

            </Container>
        </UserLayout>
    );
}
