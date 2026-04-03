import { Link, router, usePage } from "@inertiajs/react";
import { useState } from "react";
import ApplicationName from "../components/ApplicationName";
import Dropdown from "../components/Dropdown";
import DropdownLink from "../components/DropdownLink";
import Hr from "../components/Hr";
import NavLink from "../components/NavLink";
import ResponsiveNavLink from "../components/ResponsiveNavLink";
import ResponsiveNavigation from "../Layouts/ResponsiveNavigation";
import VendorResponsiveNavigation from "../Layouts/VendorResponsiveNavigation";
import ResellerResponsiveNavigation from "../Layouts/ResellerResponsiveNavigation";
import RiderResponsiveNavigation from "../Layouts/RiderResponsiveNavigation";

export default function Navigation() {
    const { auth, roles = [] } = usePage().props;
    const user = auth?.user;
    const availableCoin = auth?.availableCoin ?? 0;
    const roleNames = roles.length
        ? roles
        : user?.roles?.map((role) => role.name) ?? [];
    const currentNav = user?.active_nav ?? roleNames[0] ?? "";
    const [open, setOpen] = useState(false);

    const goToDashboard = (name) => {
        router.post(route("dashboard.navigation"), { name });
    };

    const logout = () => {
        router.post(route("logout.perform"));
    };

    return (
        <nav className="bg-white border-b border-gray-100">
            <div className="w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between h-16">
                    <div className="flex">
                        <div className="shrink-0 flex items-center">
                            <Link href="/" className="flex items-center">
                                <img
                                    height="50"
                                    width="60"
                                    src="/icon.png"
                                    alt=""
                                />
                                <div className="text-lg font-bold ps-2">
                                    <ApplicationName />
                                </div>
                            </Link>
                        </div>

                        <div className="hidden space-x-8 md:-my-px md:ms-10 md:flex">
                            <NavLink
                                href={route("dashboard")}
                                active={route().current("dashboard")}
                            >
                                Dashboard
                            </NavLink>
                        </div>
                    </div>

                    <div className="hidden md:flex md:items-center md:ms-6">
                        <Dropdown
                            align="right"
                            width="48"
                            trigger={
                                <button className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div className="px-2 py-1 border bg-orange-500 border-transparent text-white rounded-md mx-1">
                                        {currentNav}
                                    </div>
                                    <div>{(user?.name ?? "").slice(0, 8)}</div>
                                    <div className="ms-1">
                                        <svg
                                            className="w-4 h-4 fill-current"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                </button>
                            }
                        >
                            <div className="border-b border-gray-200 px-4 py-2">
                                <div className="flex justify-between px-2 ">
                                    <div>Wallet</div>
                                    <div>{availableCoin}</div>
                                </div>

                                <div className="text-end w-full pt-1 uppercase font-bold">
                                    <DropdownLink
                                        href={route("user.wallet.diposit")}
                                        className="font-bold text-center uppercase text-orange-900"
                                    >
                                        <i className="pr-2 fas fa-plus"></i>
                                        Add Balance
                                    </DropdownLink>
                                </div>
                            </div>

                            {roleNames.includes("vendor") && (
                                <button
                                    onClick={() => goToDashboard("vendor")}
                                    className="w-full text-start"
                                >
                                    <div className="block w-full px-4 py-2 text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100">
                                        {currentNav === "vendor" && (
                                            <i className="mr-3 fas fa-check"></i>
                                        )}
                                        <i className="pr-2 fas fa-shop"></i>
                                        Vendor Dashboard
                                    </div>
                                </button>
                            )}

                            {roleNames.includes("reseller") && (
                                <button
                                    onClick={() => goToDashboard("reseller")}
                                    className="w-full text-start"
                                >
                                    <div className="block w-full px-4 py-2 text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100">
                                        {currentNav === "reseller" && (
                                            <i className="mr-3 fas fa-check"></i>
                                        )}
                                        <i className="pr-2 fas fa-shop"></i>
                                        Reseller Dashboard
                                    </div>
                                </button>
                            )}

                            {roleNames.includes("rider") && (
                                <button
                                    onClick={() => goToDashboard("rider")}
                                    className="w-full text-start"
                                >
                                    <div className="block w-full px-4 py-2 text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100">
                                        {currentNav === "rider" && (
                                            <i className="mr-3 fas fa-check"></i>
                                        )}
                                        <i className="pr-2 fas fa-truck-fast"></i>
                                        Rider Dashboard
                                    </div>
                                </button>
                            )}

                            <DropdownLink href={route("profile")}>
                                <i className="pr-2 fas fa-user"></i>
                                Profile
                            </DropdownLink>

                            <div className="block w-full px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out">
                                <i className="pr-2 fas fa-gear"></i>
                                Settings
                            </div>

                            <div className="block w-full px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out">
                                <i className="pr-2 fas fa-bell"></i>
                                Notice
                            </div>

                            <Hr />

                            <DropdownLink
                                href={route("user.dash")}
                                target="_blank"
                            >
                                <i className="pr-2 fas fa-gauge"></i>
                                Back to User Panel
                            </DropdownLink>

                            <DropdownLink
                                href={route("home")}
                                target="_blank"
                            >
                                <i className="pr-2 fas fa-globe"></i>
                                Visit Website
                            </DropdownLink>

                            <Hr />

                            <button onClick={logout} className="w-full text-start">
                                <div className="block w-full px-4 py-2 text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100">
                                    <i className="pr-2 fas fa-sign-out"></i>
                                    Log Out
                                </div>
                            </button>
                        </Dropdown>
                    </div>

                    <div className="-me-2 flex items-center md:hidden">
                        <button
                            onClick={() => setOpen((value) => !value)}
                            className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                        >
                            <svg
                                className="w-6 h-6"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                {open ? (
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                ) : (
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                )}
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {open && (
                <div className="md:hidden">
                    <div className="pt-2 pb-3 space-y-1">
                        <div className="flex justify-between px-2 ">
                            <div>Wallet</div>
                            <div>{availableCoin}</div>
                        </div>

                        <div className="text-end w-full pt-1 uppercase font-bold">
                            <NavLink
                                className="font-bold text-center uppercase text-orange-900"
                                href={route("user.wallet.diposit")}
                            >
                                <i className="pr-2 fas fa-plus"></i>
                                Add Balance
                            </NavLink>
                        </div>

                        <Hr />

                        <ResponsiveNavLink
                            href={route("dashboard")}
                            active={route().current("dashboard")}
                        >
                            <i className="pr-2 fas fa-home"></i>
                            Dashboard
                        </ResponsiveNavLink>

                        <ResponsiveNavigation />
                        {roleNames.includes("vendor") &&
                            currentNav === "vendor" && (
                                <VendorResponsiveNavigation />
                            )}
                        {roleNames.includes("reseller") &&
                            currentNav === "reseller" && (
                                <ResellerResponsiveNavigation />
                            )}
                        {roleNames.includes("rider") &&
                            currentNav === "rider" && (
                                <RiderResponsiveNavigation />
                            )}

                    </div>

                    <div className="pt-4 pb-1 border-t border-gray-200">
                        <div className="px-4">
                            <div className="font-medium text-base text-gray-800">
                                {user?.name}
                            </div>
                            <div className="font-medium text-sm text-gray-500">
                                {user?.email}
                            </div>
                        </div>

                        {roleNames.includes("vendor") && (
                            <button
                                onClick={() => goToDashboard("vendor")}
                                className="w-full text-start"
                            >
                                <div className="block w-full px-4 py-2 text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100">
                                    {currentNav === "vendor" && (
                                        <i className="mr-3 fas fa-check"></i>
                                    )}
                                    <i className="pr-2 fas fa-shop"></i>
                                    Vendor Dashboard
                                </div>
                            </button>
                        )}

                        {roleNames.includes("reseller") && (
                            <button
                                onClick={() => goToDashboard("reseller")}
                                className="w-full text-start"
                            >
                                <div className="block w-full px-4 py-2 text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100">
                                    {currentNav === "reseller" && (
                                        <i className="mr-3 fas fa-check"></i>
                                    )}
                                    <i className="pr-2 fas fa-shop"></i>
                                    Reseller Dashboard
                                </div>
                            </button>
                        )}

                        {roleNames.includes("rider") && (
                            <button
                                onClick={() => goToDashboard("rider")}
                                className="w-full text-start"
                            >
                                <div className="block w-full px-4 py-2 text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100">
                                    {currentNav === "rider" && (
                                        <i className="mr-3 fas fa-check"></i>
                                    )}
                                    <i className="pr-2 fas fa-truck-fast"></i>
                                    Rider Dashboard
                                </div>
                            </button>
                        )}

                        <div className="mt-3 space-y-1">
                            <ResponsiveNavLink
                                href={route("user.dash")}
                                target="_blank"
                            >
                                <i className="pr-2 fas fa-gauge"></i>
                                Back to User Dash
                            </ResponsiveNavLink>

                            <ResponsiveNavLink
                                href={route("home")}
                                target="_blank"
                            >
                                <i className="pr-2 fas fa-globe"></i>
                                Visit Website
                            </ResponsiveNavLink>

                            <ResponsiveNavLink href={route("profile")}>
                                <i className="pr-2 fas fa-user"></i>
                                Profile
                            </ResponsiveNavLink>

                            <button
                                onClick={logout}
                                className="w-full text-start"
                            >
                                <div className="block w-full px-3 py-2 text-sm font-medium text-start text-gray-600 transition duration-150 ease-in-out border-l-4 border-transparent hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800">
                                    <i className="pr-2 fas fa-sign-out"></i>
                                    Log Out
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </nav>
    );
}
