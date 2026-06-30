import { useEffect, useState } from "react";
import { usePage, router, Link } from "@inertiajs/react";
import Modal from "./Modal";
import NavLink from "./NavLink";
import useTranslation from "../hooks/useTranslation";
import CountrySearchSelect from "./CountrySearchSelect";
import LanguageSwitcher from "./LanguageSwitcher";

export default function StickyNav({ open, setOpen }) {
    const { auth, global, selectedCountry: pageCountry } = usePage().props;
    const countries = global?.countries || [];
    const { t } = useTranslation();

    const [visible, setVisible] = useState(false);

    useEffect(() => {
        const onScroll = () => {
            setVisible(window.scrollY > 150);
        };

        window.addEventListener("scroll", onScroll);
        return () => window.removeEventListener("scroll", onScroll);
    }, []);

    const user = auth?.user;
    const [cartCount, setCartCount] = useState(auth?.cartCount ?? 0);

    const [show, setShow] = useState(false);
    const [dropdownOpen, setDropdownOpen] = useState(false);
    const [search, setSearch] = useState("");
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

    const handleSubmit = (e) => {
        e.preventDefault();
        const query = search.trim();

        if (!query) {
            return;
        }

        router.get(route("search"), {
            q: query,
            country: selectedCountry || undefined,
        });
        setShow(false);
    };
    const displayName =
        user?.name?.length > 8 ? `${user.name.substring(0, 8)}..` : user?.name;

    return (
        <>
            <div
                className={`fixed top-0 left-0 z-50 w-full bg-white transition-all duration-300 ${
                    visible ? "block" : "hidden pointer-events-none"
                }`}
            >
                <div className="flex items-center justify-between w-full gap-2 px-2 mx-auto sm:px-3 max-w-8xl">
                    {/* LEFT SIDE */}
                    <div className="flex items-center flex-1 min-w-0 gap-2 sm:gap-3 md:flex-none md:gap-4">
                        <button
                            className="flex items-center justify-center w-12 h-12 px-2 border-r shrink-0 sm:w-16 md:w-20"
                            onClick={() => setOpen(!open)}
                        >
                            {!open ? (
                                <i className="text-lg fas fa-align-justify"></i>
                            ) : (
                                <i className="text-lg fas fa-times"></i>
                            )}
                        </button>

                        <Link href="/" className="flex items-center min-w-0">
                            <img
                                height="50"
                                width="60"
                                src="/icon.png"
                                className="w-10 h-auto shrink-0 sm:w-12 md:w-[60px]"
                            />
                            <div className="text-base font-bold leading-none truncate ps-1 sm:ps-2 sm:text-lg">
                                {import.meta.env.VITE_APP_NAME?.toUpperCase()}
                            </div>
                        </Link>
                    </div>

                    <div className="items-center justify-between flex-1 hidden gap-4 lg:flex">
                        <Link
                            href={route("shops.reseller")}
                            className="block px-2 shrink-0 text-inherit"
                        >
                            {t("Shops")}
                        </Link>

                        <div className="flex items-center justify-end flex-1 min-w-0 gap-2">
                            <div className="relative flex-1 max-w-xs xl:max-w-sm">
                                <form onSubmit={handleSubmit}>
                                    <input
                                        type="search"
                                        value={search}
                                        onChange={(e) =>
                                            setSearch(e.target.value)
                                        }
                                        placeholder={t(
                                            "Search Product By Title or Tags",
                                        )}
                                        className="w-full px-3 text-sm border border-gray-200 rounded-md h-9 pr-11 shadow-0 focus:border-gray-300 focus:shadow-0"
                                        style={{ marginBottom: 0 }}
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

                    {/* RIGHT SIDE */}
                    <div className="shrink-0">
                        <div className="flex items-center justify-between gap-0.5 sm:gap-1">
                            {/* SEARCH */}
                            <button
                                className="rounded lg:hidden"
                                onClick={() => setShow(true)}
                                aria-label={t("Search")}
                            >
                                <i className="p-2 fas fa-search text-md"></i>
                            </button>
                            <div className="flex items-center shrink-0">
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
                                {user ? (
                                    <>
                                        {/* CART */}
                                        <NavLink
                                            href={route("carts.view")}
                                            className="p-0 mr-1.5 border-b-0 text-inherit hover:text-inherit hover:border-transparent"
                                        >
                                            <button
                                                className="relative flex items-center justify-center w-10 h-10 gap-0.5 text-sm rounded-md sm:w-auto sm:px-1.5"
                                                aria-label={t("Cart")}
                                            >
                                                <i className="fas fa-cart-plus"></i>
                                                <span className="absolute top-1 -right-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-green-600 px-1 text-[10px] font-semibold leading-none text-white">
                                                    {cartCount}
                                                </span>
                                            </button>
                                        </NavLink>

                                        {/* DROPDOWN */}
                                        <div className="relative flex sm:items-center ">
                                            <button
                                                onClick={() =>
                                                    setDropdownOpen(
                                                        !dropdownOpen,
                                                    )
                                                }
                                                className="flex items-center justify-center w-10 h-10 px-0 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border rounded-md sm:w-auto sm:justify-start sm:px-2 hover:text-gray-700"
                                            >
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

                                            {dropdownOpen && (
                                                <div className="absolute right-0 z-50 w-48 mt-2 bg-white border rounded-md shadow-lg top-full">
                                                    <NavLink
                                                        href={route(
                                                            "user.index",
                                                        )}
                                                        className="block px-4 py-2 pt-2 border-b-0 text-inherit hover:bg-gray-100 hover:text-inherit hover:border-transparent"
                                                    >
                                                        {t("User Panel")}
                                                    </NavLink>

                                                    <NavLink
                                                        href={route(
                                                            "upgrade.vendor.create",
                                                            {
                                                                upgrade:
                                                                    "vendor",
                                                            },
                                                        )}
                                                        className="block px-4 py-2 pt-2 border-b-0 text-inherit hover:bg-gray-100 hover:text-inherit hover:border-transparent"
                                                    >
                                                        {t("Request Vendor")}
                                                    </NavLink>

                                                    <NavLink
                                                        href={route(
                                                            "upgrade.vendor.create",
                                                            {
                                                                upgrade:
                                                                    "reseller",
                                                            },
                                                        )}
                                                        className="block px-4 py-2 pt-2 border-b-0 text-inherit hover:bg-gray-100 hover:text-inherit hover:border-transparent"
                                                    >
                                                        {t("Request Reseller")}
                                                    </NavLink>

                                                    <NavLink
                                                        href={route(
                                                            "upgrade.rider.create",
                                                        )}
                                                        className="block px-4 py-2 pt-2 border-b-0 text-inherit hover:bg-gray-100 hover:text-inherit hover:border-transparent"
                                                    >
                                                        {t("Request Rider")}
                                                    </NavLink>

                                                    <NavLink
                                                        href={route(
                                                            "dashboard",
                                                        )}
                                                        className="block px-4 py-2 pt-2 border-b-0 text-inherit hover:bg-gray-100 hover:text-inherit hover:border-transparent"
                                                    >
                                                        {t("Dashboard")}
                                                    </NavLink>

                                                    <NavLink
                                                        href={route("logout")}
                                                        className="block w-full px-4 py-2 pt-2 text-left text-red-500 border-b-0 hover:bg-gray-100 hover:text-red-500 hover:border-transparent"
                                                    >
                                                        {t("Logout")}
                                                    </NavLink>
                                                </div>
                                            )}
                                        </div>
                                    </>
                                ) : (
                                    <NavLink
                                        href={route("login")}
                                        className="flex items-center justify-center h-10 gap-1 px-2 pt-0 text-xs uppercase border-b-0 whitespace-nowrap sm:px-3 sm:text-sm md:text-md text-inherit hover:text-inherit hover:border-transparent"
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
                </div>
            </div>

            {/* SEARCH MODAL */}
            <>
                <Modal show={show} onClose={() => setShow(false)}>
                    <div className="p-3">
                        <form onSubmit={handleSubmit}>
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
        </>
    );
}
