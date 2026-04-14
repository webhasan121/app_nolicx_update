import { usePage, Link, router } from "@inertiajs/react";
import { useState, useEffect } from "react";
import ApplicationName from "../ApplicationName";
import CatLoop from "../client/CatLoop";
import StickyNav from "../StickyNav";
import Dropdown from "../Dropdown";
import DropdownLink from "../DropdownLink";
import NavLink from "../NavLink";

export default function Header() {
    const { auth, global, roles, activeNav, categories } = usePage().props; // this global() load in AppServiceProvider
    // const categories = global?.categories || [];
    const user = auth?.user;



    const [active, setActive] = useState(null);

    const [open, setOpen] = useState(false);

    // Logout
    const logout = () => {
        router.get(route("logout"));
    };

    // Search
    const handleSearch = (e) => {
        e.preventDefault();
        const q = e.target.q.value;
        router.get(route("search"), { q });
    };

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
                            className="items-center justify-between flex-1 hidden w-full px-4 md:flex"
                            id="search_content"
                        >
                            <Link
                                href={route("shops.reseller")}
                                className="block px-2"
                            >
                                Shops
                            </Link>

                            <div className="relative flex-1 max-w-xl">
                                <form onSubmit={handleSearch}>
                                    <input
                                        type="search"
                                        name="q"
                                        placeholder="Search Product By Title or Tags"
                                        className="w-full border border-gray-200 rounded-md shadow-0 focus:border-0 focus:shadow-0"
                                        style={{ marginBottom: 0 }}
                                        id="search"
                                    />
                                </form>
                            </div>
                        </div>

                        {/* RIGHT */}
                        <div>
                            {auth?.user ? (
                                <div className="flex items-center">
                                    {/* CART */}
                                    <NavLink
                                        href={route("carts.view")}
                                        className="mr-3"
                                        unstyled
                                    >
                                        <button
                                            type="button"
                                            className="flex items-center btn"
                                        >
                                            <i className="fas fa-cart-plus"></i>
                                            <span
                                                id="displayCartItem"
                                                className="pb-3 text-green"
                                            >
                                                {auth.cartCount ?? 0}
                                            </span>
                                        </button>
                                    </NavLink>

                                    {/* DROPDOWN */}
                                    <div className="flex">
                                        <div className="relative flex sm:items-center sm:ms-6">
                                            <Dropdown
                                                align="right"
                                                width="48"
                                                trigger={
                                                    <button className="flex items-center px-3 py-2 text-sm font-medium text-gray-500 transition bg-white border rounded-md hover:text-gray-700">
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
                                                            Dashboard
                                                        </DropdownLink>
                                                        <hr />
                                                    </>
                                                )}

                                                <DropdownLink
                                                    href={route("user.index")}
                                                >
                                                    <i className="pr-2 fas fa-gauge"></i>
                                                    User Panel
                                                </DropdownLink>

                                                <DropdownLink
                                                    href={route(
                                                        "user.orders.view",
                                                    )}
                                                >
                                                    <i className="pr-2 fas fa-shopping-cart"></i>
                                                    Order
                                                </DropdownLink>

                                                <DropdownLink
                                                    href={route("edit.profile")}
                                                >
                                                    <i className="pr-2 fas fa-user"></i>
                                                    Profile
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
                                                            Request Vendor
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
                                                            Request Reseller
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
                                                    Log Out
                                                </button>
                                            </Dropdown>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <NavLink
                                    href={route("login")}
                                    className="px-3 uppercase text-md"
                                >
                                    <i className="pr-2 fas fa-sign-in"></i>
                                    Login
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
                        <span>Shops</span>
                        <i className="fas fa-caret-right"></i>
                    </Link>

                    {/* Categories */}
                    <div>
                        {categories?.map((item) => (
                            <div
                                key={item.id}
                                className="p-3 mb-1 bg-gray-100 border-b"
                            >
                                {/* Button */}
                                <button
                                    className="flex items-center justify-between w-full"
                                    onClick={() =>
                                        setActive(
                                            active === item.id ? null : item.id,
                                        )
                                    }
                                >
                                    <span>{item.name ?? "N/A"}</span>

                                    <i
                                        className={`fas ${
                                            active === item.id
                                                ? "fa-sort-up"
                                                : "fa-sort-down"
                                        }`}
                                    ></i>
                                </button>

                                {/* Content */}
                                {active === item.id && (
                                    <CatLoop key={item.id} item={item} />
                                )}
                            </div>
                        ))}
                    </div>
                </aside>
            </header>
            <StickyNav open={open} setOpen={setOpen} />
        </>
    );
}
