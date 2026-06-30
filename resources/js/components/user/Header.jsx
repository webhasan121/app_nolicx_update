import { usePage, Link, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import ApplicationName from "../ApplicationName";
import CatLoop from "../client/CatLoop";
import StickyNav from "../StickyNav";
import Dropdown from "../Dropdown";
import DropdownLink from "../DropdownLink";
import NavLink from "../NavLink";
import useTranslation from "../../hooks/useTranslation";
import CountrySearchSelect from "../CountrySearchSelect";
import LanguageSwitcher from "../LanguageSwitcher";
import Modal from "../Modal";

const PRIORITY_CATEGORY_SLUGS = [
    "womens-item",
    "mega-deals",
    "medicine",
    "grocery-item",
    "food-items",
];

export default function Header() {
    const {
        auth,
        global,
        activeNav,
        selectedCountry: pageCountry,
    } = usePage().props; // this global() load in AppServiceProvider
    const categories = global?.categories || [];
    const countries = global?.countries || [];
    const user = auth?.user;
    const { t } = useTranslation();
    const resolveCountry = (value) => {
        const normalized = String(value ?? "")
            .trim()
            .toLowerCase();

        if (!normalized) {
            return "";
        }

        return (
            countries.find(
                (country) =>
                    String(country.name ?? "")
                        .trim()
                        .toLowerCase() === normalized ||
                    String(country.id) === String(value),
            )?.name ?? value
        );
    };

    const [open, setOpen] = useState(false);
    const [show, setShow] = useState(false);
    const [search, setSearch] = useState("");
    const [categoryQuery, setCategoryQuery] = useState("");
    const [selectedCountry, setSelectedCountry] = useState(() => {
        if (typeof window !== "undefined") {
            const country = new URLSearchParams(window.location.search).get(
                "country",
            );

            if (country) {
                return resolveCountry(country);
            }
        }

        return resolveCountry(pageCountry || user?.country || "Bangladesh");
    });
    const [cartCount, setCartCount] = useState(auth?.cartCount ?? 0);
    const currentCategorySlug = (() => {
        if (typeof window === "undefined") {
            return "";
        }

        const match = window.location.pathname.match(
            /^\/category\/([^/]+)\/products\/?$/,
        );
        return match ? decodeURIComponent(match[1]) : "";
    })();

    useEffect(() => {
        setSelectedCountry(
            resolveCountry(pageCountry || user?.country || "Bangladesh"),
        );
    }, [pageCountry, user?.country, countries.length]);

    useEffect(() => {
        setCartCount(auth?.cartCount ?? 0);
    }, [auth?.cartCount]);

    useEffect(() => {
        const handleCartUpdated = (event) => {
            const nextCount = Number(event.detail?.cartCount);

            if (Number.isFinite(nextCount)) {
                setCartCount(nextCount);
            }
        };

        window.addEventListener("cart:updated", handleCartUpdated);

        return () => {
            window.removeEventListener("cart:updated", handleCartUpdated);
        };
    }, []);

    // Logout
    const logout = () => {
        router.get(route("logout"));
    };

    // Search
    const submitSearch = (query) => {
        const q = String(query ?? "").trim();

        if (!q) {
            return;
        }

        router.get(route("search"), {
            q,
            country: selectedCountry || undefined,
        });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        submitSearch(e.target.q.value);
    };

    const handleMobileSearch = (e) => {
        e.preventDefault();
        submitSearch(search);
        setShow(false);
    };

    const handleCountryChange = (country) => {
        setSelectedCountry(country);

        if (typeof window === "undefined") {
            return;
        }

        const nextUrl = new URL(window.location.href);
        const params = Object.fromEntries(nextUrl.searchParams.entries());

        delete params.page;

        router.get(
            nextUrl.pathname,
            {
                ...params,
                country: country || undefined,
            },
            {
                preserveScroll: true,
                preserveState: false,
                replace: true,
            },
        );
    };

    const matchesCategory = (item, keyword) => {
        const name = String(item?.name ?? "").toLowerCase();
        const slug = String(item?.slug ?? "").toLowerCase();
        const childMatches = (item?.children ?? []).some((child) =>
            matchesCategory(child, keyword),
        );

        return name.includes(keyword) || slug.includes(keyword) || childMatches;
    };

    const orderedCategories = [...categories].sort((left, right) => {
        const leftPriority = PRIORITY_CATEGORY_SLUGS.indexOf(left.slug);
        const rightPriority = PRIORITY_CATEGORY_SLUGS.indexOf(right.slug);

        if (leftPriority === -1 && rightPriority === -1) return 0;
        if (leftPriority === -1) return 1;
        if (rightPriority === -1) return -1;

        return leftPriority - rightPriority;
    });

    const filteredCategories = !categoryQuery.trim()
        ? orderedCategories
        : orderedCategories.filter((item) =>
              matchesCategory(item, categoryQuery.trim().toLowerCase()),
          );
    const displayName = user?.name
        ? user.name.length > 8
            ? `${user.name.slice(0, 8)}...`
            : user.name
        : "Unauthorize";

    return (
        <>
            <header className="relative z-40 w-full ">
                {/* {{-- normal nav on desktop --}} */}
                <div className="text-center bg-white">
                    <div
                        className="flex items-center justify-between w-full gap-2 px-2 mx-auto sm:px-3 max-w-8xl"
                        id="desktop-nav"
                    >
                        {/* LEFT */}
                        <div className="flex items-center flex-1 min-w-0 gap-2 sm:gap-3 md:flex-none md:gap-4">
                            <button
                                className="flex items-center justify-center w-12 h-12 px-2 border-r shrink-0 sm:w-16 md:w-20"
                                onClick={() => setOpen(!open)}
                            >
                                <i className="text-lg fas fa-align-justify"></i>
                            </button>

                            {/* logo */}
                            <Link
                                href="/"
                                className="flex items-center min-w-0"
                            >
                                <img
                                    height="50"
                                    width="60"
                                    src="/icon.png"
                                    className="w-10 h-auto shrink-0 sm:w-12 md:w-[60px]"
                                />
                                <div className="text-base font-bold leading-none truncate ps-1 sm:ps-2 sm:text-lg">
                                    <ApplicationName />
                                </div>
                            </Link>
                        </div>

                        {/* SEARCH */}
                        <div
                            className="items-center justify-between flex-1 hidden w-full gap-4 lg:flex"
                            id="search_content"
                        >
                            <Link
                                href={route("shops.reseller")}
                                className="block px-2 shrink-0"
                            >
                                {t("Shops")}
                            </Link>

                            <div className="flex items-center justify-end flex-1 min-w-0 gap-2">
                                <div className="relative flex-1 max-w-xs xl:max-w-sm">
                                    <form onSubmit={handleSearch}>
                                        <input
                                            type="search"
                                            name="q"
                                            placeholder={t(
                                                "Search Product By Title or Tags",
                                            )}
                                            className="w-full px-3 text-sm border border-gray-200 rounded-md h-9 pr-11 shadow-0 focus:border-gray-300 focus:shadow-0"
                                            style={{ marginBottom: 0 }}
                                            id="search"
                                        />
                                        <button
                                            type="submit"
                                            className="absolute inset-y-0 right-0 flex items-center justify-center text-gray-100 rounded-r-md w-11 hover:text-gray-800 bg_primary hover:bg_primary"
                                            aria-label={t("Search")}
                                        >
                                            <i className="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>

                                {countries.length ? (
                                    <CountrySearchSelect
                                        value={selectedCountry}
                                        options={countries}
                                        onChange={handleCountryChange}
                                        placeholder={t("Country")}
                                    />
                                ) : null}
                            </div>
                        </div>

                        {/* RIGHT */}
                        <div className="flex items-center shrink-0 gap-0.5 sm:gap-1">
                            <button
                                className="rounded lg:hidden"
                                onClick={() => setShow(true)}
                                aria-label={t("Search")}
                            >
                                <i className="p-2 fas fa-search text-md"></i>
                            </button>
                            {countries.length ? (
                                <CountrySearchSelect
                                    value={selectedCountry}
                                    options={countries}
                                    onChange={handleCountryChange}
                                    placeholder={t("Country")}
                                    className="hidden md:block lg:hidden w-28"
                                />
                            ) : null}
                            <LanguageSwitcher compact className="hidden md:block" />
                            {auth?.user ? (
                                <>
                                    {/* CART */}
                                    <NavLink
                                        href={route("carts.view")}
                                        className="mr-1.5"
                                        unstyled
                                    >
                                        <button
                                            type="button"
                                            className="relative flex items-center justify-center w-10 h-10 gap-0.5 text-sm rounded-md sm:w-auto sm:px-1.5"
                                            aria-label={t("Cart")}
                                        >
                                            <i className="fas fa-cart-plus"></i>
                                            <span
                                                id="displayCartItem"
                                                className="absolute top-1 -right-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-green-600 px-1 text-[10px] font-semibold leading-none text-white"
                                            >
                                                {cartCount}
                                            </span>
                                        </button>
                                    </NavLink>

                                    {/* DROPDOWN */}
                                    <div className="flex">
                                        <div className="relative flex sm:items-center ">
                                            <Dropdown
                                                align="right"
                                                width="48"
                                                trigger={
                                                    <button className="flex items-center justify-center w-10 h-10 px-0 text-sm font-medium text-gray-500 transition bg-white border rounded-md sm:w-auto sm:justify-start sm:px-2 hover:text-gray-700">
                                                        <span className="text-base sm:hidden">
                                                            <i className="fas fa-user"></i>
                                                        </span>
                                                        <div className="hidden truncate max-w-20 sm:block">
                                                            {displayName}
                                                        </div>

                                                        <div className="hidden ms-1 sm:block">
                                                            <svg
                                                                className="w-4 h-4 fill-current"
                                                                viewBox="0 0 20 20"
                                                            >
                                                                <path
                                                                    fillRule="evenodd"
                                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                                />
                                                            </svg>
                                                        </div>
                                                    </button>
                                                }
                                            >
                                                {/* Multiple Roles */}
                                                {user.roles.length > 1 && (
                                                    <>
                                                        <DropdownLink
                                                            href={route(
                                                                "dashboard",
                                                            )}
                                                            target="_blank"
                                                        >
                                                            <i className="pr-2 fas fa-home"></i>
                                                            {t("Dashboard")}
                                                        </DropdownLink>
                                                        <hr />
                                                    </>
                                                )}

                                                <DropdownLink
                                                    href={route("user.index")}
                                                >
                                                    <i className="pr-2 fas fa-gauge"></i>
                                                    {t("User Panel")}
                                                </DropdownLink>

                                                <DropdownLink
                                                    href={route(
                                                        "user.orders.view",
                                                    )}
                                                >
                                                    <i className="pr-2 fas fa-shopping-cart"></i>
                                                    {t("Order")}
                                                </DropdownLink>

                                                <DropdownLink
                                                    href={route("edit.profile")}
                                                >
                                                    <i className="pr-2 fas fa-user"></i>
                                                    {t("Profile")}
                                                </DropdownLink>

                                                {/* Request Vendor / Reseller */}
                                                {!activeNav && (
                                                    <>
                                                        <hr />

                                                        <DropdownLink
                                                            href={route(
                                                                "upgrade.vendor.create",
                                                                {
                                                                    upgrade:
                                                                        "vendor",
                                                                },
                                                            )}
                                                        >
                                                            <i className="pr-2 fas fa-shop"></i>
                                                            {t(
                                                                "Request Vendor",
                                                            )}
                                                        </DropdownLink>

                                                        <DropdownLink
                                                            href={route(
                                                                "upgrade.vendor.create",
                                                                {
                                                                    upgrade:
                                                                        "reseller",
                                                                },
                                                            )}
                                                        >
                                                            <i className="pr-2 fas fa-shop"></i>
                                                            {t(
                                                                "Request Reseller",
                                                            )}
                                                        </DropdownLink>

                                                        <DropdownLink
                                                            href={route(
                                                                "upgrade.rider.create",
                                                            )}
                                                        >
                                                            <i className="pr-2 fas fa-motorcycle"></i>
                                                            {t("Request Rider")}
                                                        </DropdownLink>

                                                        <hr />
                                                    </>
                                                )}

                                                {/* Role-based Sections */}
                                                {(user.roles.includes(
                                                    "admin",
                                                ) ||
                                                    user.roles.includes(
                                                        "system",
                                                    )) && (
                                                    <>
                                                        <hr />
                                                        {/* Admin navigation here */}
                                                        <hr />
                                                    </>
                                                )}

                                                {user.roles.includes(
                                                    "vendor",
                                                ) &&
                                                    activeNav === "vendor" && (
                                                        <>
                                                            <hr />
                                                            {/* Vendor navigation */}
                                                            <hr />
                                                        </>
                                                    )}

                                                {user.roles.includes(
                                                    "reseller",
                                                ) &&
                                                    activeNav ===
                                                        "reseller" && (
                                                        <>
                                                            <hr />
                                                            {/* Reseller navigation */}
                                                            <hr />
                                                        </>
                                                    )}

                                                <hr />

                                                {/* Logout */}
                                                <button
                                                    onClick={logout}
                                                    className="block w-full px-4 py-2 text-sm text-red-600 transition text-start hover:bg-gray-100"
                                                >
                                                    <i className="pr-2 fas fa-sign-out"></i>
                                                    {t("Log Out")}
                                                </button>
                                            </Dropdown>
                                        </div>
                                    </div>
                                </>
                            ) : (
                                <NavLink
                                    href={route("login")}
                                    className="flex items-center justify-center h-10 gap-1 px-2 text-xs uppercase whitespace-nowrap sm:px-3 sm:text-sm md:text-md"
                                >
                                    <i className="pr-1 sm:pr-2 fas fa-sign-in"></i>
                                    <span className="max-[420px]:hidden">
                                        {t("Login")}
                                    </span>
                                </NavLink>
                            )}
                        </div>
                    </div>
                </div>

                {/* sticky */}

                {/* Sidebar */}
                <aside
                    className={`fixed top-0 left-0 z-50 h-screen overflow-y-scroll bg-white shadow-lg transition-all duration-300 ${
                        open ? "block" : "hidden"
                    }`}
                    style={{ width: "min(275px, 85vw)" }}
                >
                    {/* Header */}
                    <div className="flex items-center gap-2 py-2 sm:gap-4">
                        <button
                            className="flex items-center justify-center w-12 h-12 px-2 border-r shrink-0 sm:w-16 md:w-20"
                            onClick={() => setOpen(false)}
                        >
                            <i className="text-lg fas fa-times"></i>
                        </button>

                        <div className="flex items-center min-w-0">
                            <Link href="/" className="flex items-center min-w-0">
                                <img
                                    src="/icon.png"
                                    style={{ width: 40 }}
                                    className="w-10 h-auto shrink-0"
                                />
                                <div className="text-base font-bold leading-none truncate ps-2 sm:text-lg">
                                    <ApplicationName />
                                </div>
                            </Link>
                        </div>
                    </div>

                    {/* Shops */}
                    <Link
                        href={route("shops.reseller")}
                        className="flex items-center justify-between w-full p-3 py-4 mb-4 bg-indigo-200 border rounded"
                    >
                        <span>{t("Shops")}</span>
                        <i className="fas fa-caret-right"></i>
                    </Link>

                    {/* Categories */}
                    <div className="px-1 pb-4">
                        <div className="px-3 pb-3">
                            <input
                                type="search"
                                value={categoryQuery}
                                onChange={(e) =>
                                    setCategoryQuery(e.target.value)
                                }
                                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-200"
                                placeholder={t("Search categories...")}
                            />
                        </div>
                        {filteredCategories?.map((item) => (
                            <CatLoop
                                key={item.id}
                                item={item}
                                cat={currentCategorySlug}
                                variant="sidebar"
                            />
                        ))}
                        {!filteredCategories?.length ? (
                            <div className="px-4 py-2 text-sm text-slate-500">
                                {t("No category found")}
                            </div>
                        ) : null}
                    </div>
                </aside>
            </header>
            <StickyNav open={open} setOpen={setOpen} />
            <Modal show={show} onClose={() => setShow(false)}>
                <div className="p-3">
                    <form onSubmit={handleMobileSearch}>
                        <input
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder={t(
                                "Search Product By Title or Tags",
                            )}
                            className="w-full border rounded-md"
                        />
                        <hr className="my-2" />
                        <button
                            type="submit"
                            className="px-4 py-2 text-white bg-indigo-600 rounded-md"
                        >
                            {t("Search")}
                        </button>
                    </form>
                </div>
            </Modal>
        </>
    );
}
