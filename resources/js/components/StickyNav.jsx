import { useEffect, useState } from "react";
import { usePage, router, Link } from "@inertiajs/react";
import Modal from "./Modal";
import NavLink from "./NavLink";
import useTranslation from "../hooks/useTranslation";
import CountrySearchSelect from "./CountrySearchSelect";
import LanguageSwitcher from "./LanguageSwitcher";

export default function StickyNav({ open, setOpen }) {
    const { auth, global } = usePage().props;
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
    const cartCount = auth?.cartCount ?? 0;

    const [show, setShow] = useState(false);
    const [dropdownOpen, setDropdownOpen] = useState(false);
    const [search, setSearch] = useState("");
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
    const [selectedCountry, setSelectedCountry] = useState(() => {
        if (typeof window !== "undefined") {
            const country = new URLSearchParams(window.location.search).get("country");

            if (country) {
                return resolveCountry(country);
            }
        }

        return resolveCountry(user?.country);
    });

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

    return (
        <>
            <div
                className={`fixed top-0 left-0 z-50 w-full bg-white transition-all duration-300 ${
                    visible ? "block" : "hidden pointer-events-none"
                }`}
            >
                <div className="flex items-center justify-between w-full px-3 mx-auto max-w-8xl">
                    {/* LEFT SIDE */}
                    <div className="flex items-center gap-4">
                        <button
                            className="w-20 px-2 border-r"
                            onClick={() => setOpen(!open)}
                        >
                            {!open ? (
                                <i className="text-lg fas fa-align-justify"></i>
                            ) : (
                                <i className="text-lg fas fa-times"></i>
                            )}
                        </button>

                        <Link href="/" className="flex items-center">
                            <img height="50" width="60" src="/icon.png" />
                            <div className="text-lg font-bold ps-2">
                                {import.meta.env.VITE_APP_NAME?.toUpperCase()}
                            </div>
                        </Link>
                    </div>

                    <div className="items-center justify-between flex-1 hidden gap-4 px-4 md:flex">
                        <Link
                            href={route("shops.reseller")}
                            className="block px-2 text-inherit"
                        >
                            {t("Shops")}
                        </Link>

                        <div className="flex items-center justify-end gap-2">
                            <div className="relative w-72">
                                <form onSubmit={handleSubmit}>
                                    <input
                                        type="search"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        placeholder={t("Search Product By Title or Tags")}
                                        className="h-10 w-full rounded-md border border-gray-200 px-3 pr-11 text-sm shadow-0 focus:border-gray-300 focus:shadow-0"
                                        style={{ marginBottom: 0 }}
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
                                    onChange={setSelectedCountry}
                                    placeholder={t("Country")}
                                />
                            ) : null}
                        </div>
                    </div>

                    {/* RIGHT SIDE */}
                    <div>
                        <div className="flex items-center justify-between">
                            {/* SEARCH */}
                            <button
                                className="mx-2 rounded md:hidden"
                                onClick={() => setShow(true)}
                            >
                                <i className="p-2 fas fa-search text-md"></i>
                            </button>

                            {user ? (
                                <>
                                    <LanguageSwitcher compact className="mx-1" />

                                    {/* CART */}
                                    <NavLink
                                        href={route("carts.view")}
                                        className="p-0 mr-1 border-b-0 text-inherit hover:text-inherit hover:border-transparent"
                                    >
                                        <button className="flex h-10 items-center justify-center rounded-md px-2 text-sm">
                                            <i className="fas fa-cart-plus"></i>
                                            <span className="ml-1 text-green">
                                                {cartCount}
                                            </span>
                                        </button>
                                    </NavLink>

                                    {/* DROPDOWN */}
                                    <div className="relative flex sm:items-center sm:ms-2">
                                        <button
                                            onClick={() =>
                                                setDropdownOpen(!dropdownOpen)
                                            }
                                            className="flex h-10 items-center rounded-md border bg-white px-3 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700"
                                        >
                                            <div>
                                                {user.name.length > 8
                                                    ? user.name.substring(
                                                          0,
                                                          8,
                                                      ) + ".."
                                                    : user.name}
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

                                        {dropdownOpen && (
                                            <div className="absolute right-0 top-full z-50 w-48 mt-2 bg-white border rounded-md shadow-lg">
                                                <NavLink
                                                    href={route("user.index")}
                                                    className="block px-4 py-2 pt-2 border-b-0 text-inherit hover:bg-gray-100 hover:text-inherit hover:border-transparent"
                                                >
                                                    {t("User Panel")}
                                                </NavLink>

                                                <NavLink
                                                    href={route(
                                                        "upgrade.vendor.create",
                                                        { upgrade: "vendor" },
                                                    )}
                                                    className="block px-4 py-2 pt-2 border-b-0 text-inherit hover:bg-gray-100 hover:text-inherit hover:border-transparent"
                                                >
                                                    {t("Request Vendor")}
                                                </NavLink>

                                                <NavLink
                                                    href={route(
                                                        "upgrade.vendor.create",
                                                        { upgrade: "reseller" },
                                                    )}
                                                    className="block px-4 py-2 pt-2 border-b-0 text-inherit hover:bg-gray-100 hover:text-inherit hover:border-transparent"
                                                >
                                                    {t("Request Reseller")}
                                                </NavLink>

                                                <NavLink
                                                    href={route("upgrade.rider.create")}
                                                    className="block px-4 py-2 pt-2 border-b-0 text-inherit hover:bg-gray-100 hover:text-inherit hover:border-transparent"
                                                >
                                                    {t("Request Rider")}
                                                </NavLink>

                                                <NavLink
                                                    href={route("dashboard")}
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
                                    className="px-3 pt-0 uppercase border-b-0 text-md text-inherit hover:text-inherit hover:border-transparent"
                                >
                                    <i className="pr-2 fas fa-sign-in"></i>
                                    {t("Login")}
                                </NavLink>
                            )}
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
                                placeholder={t("Search Product By Title or Tags")}
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
