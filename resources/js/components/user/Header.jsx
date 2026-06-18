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

export default function Header() {
    const { auth, global, activeNav, selectedCountry: pageCountry } = usePage().props; // this global() load in AppServiceProvider
    const categories = global?.categories || [];
    const countries = global?.countries || [];
    const user = auth?.user;
    const { t } = useTranslation();
    const resolveCountry = (value) => {
        const normalized = String(value ?? "").trim().toLowerCase();

        if (!normalized) {
            return "";
        }

        return (
            countries.find((country) => (
                String(country.name ?? "").trim().toLowerCase() === normalized ||
                String(country.id) === String(value)
            ))?.name ?? value
        );
    };

    const [open, setOpen] = useState(false);
    const [categoryQuery, setCategoryQuery] = useState("");
    const [selectedCountry, setSelectedCountry] = useState(() => {
        if (typeof window !== "undefined") {
            const country = new URLSearchParams(window.location.search).get("country");

            if (country) {
                return resolveCountry(country);
            }

        }

        return resolveCountry(pageCountry || user?.country || "Bangladesh");
    });
    const currentCategorySlug = (() => {
        if (typeof window === "undefined") {
            return "";
        }

        const match = window.location.pathname.match(/^\/category\/([^/]+)\/products\/?$/);
        return match ? decodeURIComponent(match[1]) : "";
    })();

    useEffect(() => {
        setSelectedCountry(resolveCountry(pageCountry || user?.country || "Bangladesh"));
    }, [pageCountry, user?.country, countries.length]);

    // Logout
    const logout = () => {
        router.get(route("logout"));
    };

    // Search
    const handleSearch = (e) => {
        e.preventDefault();
        const q = (e.target.q.value ?? "").trim();

        if (!q) {
            return;
        }

        router.get(route("search"), {
            q,
            country: selectedCountry || undefined,
        });
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
            }
        );
    };

    const matchesCategory = (item, keyword) => {
        const name = String(item?.name ?? "").toLowerCase();
        const slug = String(item?.slug ?? "").toLowerCase();
        const childMatches = (item?.children ?? []).some((child) =>
            matchesCategory(child, keyword)
        );

        return (
            name.includes(keyword) ||
            slug.includes(keyword) ||
            childMatches
        );
    };

    const filteredCategories = !categoryQuery.trim()
        ? categories
        : categories.filter((item) =>
              matchesCategory(item, categoryQuery.trim().toLowerCase())
          );

    return (
        <>
            <header className="relative z-40 w-full ">
                {/* {{-- normal nav on desktop --}} */}
                <div className="text-center bg-white">
                    <div
                        className="flex items-center justify-between w-full px-3 mx-auto max-w-8xl"
                        id="desktop-nav"
                    >
                        {/* LEFT */}
                        <div className="flex items-center gap-4">
                            <button
                                className="w-20 px-2 border-r"
                                onClick={() => setOpen(!open)}
                            >
                                <i className="text-lg fas fa-align-justify"></i>
                            </button>

                            {/* logo */}
                            <Link href="/" className="flex items-center">
                                <img height="50" width="60" src="/icon.png" />
                                <div className="text-lg font-bold ps-2">
                                    <ApplicationName />
                                </div>
                            </Link>
                        </div>

                        {/* SEARCH */}
                        <div
                            className="items-center justify-between flex-1 hidden w-full gap-4 px-4 md:flex"
                            id="search_content"
                        >
                            <Link
                                href={route("shops.reseller")}
                                className="block px-2"
                            >
                                {t("Shops")}
                            </Link>

                            <div className="flex items-center justify-end gap-2">
                                <div className="relative w-72">
                                    <form onSubmit={handleSearch}>
                                        <input
                                            type="search"
                                            name="q"
                                            placeholder={t("Search Product By Title or Tags")}
                                            className="h-10 w-full rounded-md border border-gray-200 px-3 pr-11 text-sm shadow-0 focus:border-gray-300 focus:shadow-0"
                                            style={{ marginBottom: 0 }}
                                            id="search"
                                        />
                                        <button
                                            type="submit"
                                            className="absolute inset-y-0 right-0 flex items-center justify-center w-11 text-gray-500 hover:text-gray-800"
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
                        <div>
                            <div className="flex items-center">
                                <LanguageSwitcher compact className="mx-1" />
                            {auth?.user ? (
                                <>
                                    {/* CART */}
                                    <NavLink
                                        href={route("carts.view")}
                                        className="mr-1"
                                        unstyled
                                    >
                                        <button
                                            type="button"
                                            className="flex h-10 items-center justify-center rounded-md px-2 text-sm"
                                        >
                                            <i className="fas fa-cart-plus"></i>
                                            <span
                                                id="displayCartItem"
                                                className="ml-1 text-green"
                                            >
                                                {auth.cartCount ?? 0}
                                            </span>
                                        </button>
                                    </NavLink>

                                    {/* DROPDOWN */}
                                    <div className="flex">
                                        <div className="relative flex sm:items-center sm:ms-2">
                                            <Dropdown
                                                align="right"
                                                width="48"
                                                trigger={
                                                    <button className="flex h-10 items-center rounded-md border bg-white px-3 text-sm font-medium text-gray-500 transition hover:text-gray-700">
                                                        <div>
                                                            {user?.name
                                                                ? `${user.name.slice(0, 8)}...`
                                                                : "Unauthorize"}
                                                        </div>

                                                        <div className="ms-1">
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
                                                            {t("Request Vendor")}
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
                                                            {t("Request Reseller")}
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
                                                {(user.roles.includes("admin") ||
                                                    user.roles.includes(
                                                        "system",
                                                    )) && (
                                                    <>
                                                        <hr />
                                                        {/* Admin navigation here */}
                                                        <hr />
                                                    </>
                                                )}

                                                {user.roles.includes("vendor") &&
                                                    activeNav === "vendor" && (
                                                        <>
                                                            <hr />
                                                            {/* Vendor navigation */}
                                                            <hr />
                                                        </>
                                                    )}

                                                {user.roles.includes("reseller") &&
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
                                    className="px-3 uppercase text-md"
                                >
                                    <i className="pr-2 fas fa-sign-in"></i>
                                    {t("Login")}
                                </NavLink>
                            )}
                            </div>
                        </div>
                    </div>
                </div>

                {/* sticky */}

                {/* Sidebar */}
                <aside
                    className={`fixed top-0 left-0 z-50 h-screen overflow-y-scroll bg-white shadow-lg transition-all duration-300 ${
                        open ? "block" : "hidden"
                    }`}
                    style={{ width: "275px" }}
                >
                    {/* Header */}
                    <div className="flex items-center gap-4 py-2">
                        <button
                            className="w-20 px-2 border-r"
                            onClick={() => setOpen(false)}
                        >
                            <i className="text-lg fas fa-times"></i>
                        </button>

                        <div className="flex items-center">
                            <Link href="/" className="flex items-center">
                                <img src="/icon.png" style={{ width: 40 }} />
                                <div className="text-lg font-bold ps-2">
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
                                onChange={(e) => setCategoryQuery(e.target.value)}
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
        </>
    );
}
