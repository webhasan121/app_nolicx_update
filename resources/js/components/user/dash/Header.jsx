import { Link, usePage, router } from "@inertiajs/react";
import { useState } from "react";
import Container from "../../dashboard/Container";
import ApplicationName from "../../ApplicationName";
import Dropdown from "../../Dropdown";
import DropdownLink from "../../DropdownLink";
import ResponsiveNavLink from "../../ResponsiveNavLink";
import Hr from "../../Hr";
import NavLink from "../../NavLink";

export default function Header() {
    const { auth, roles, active_nav } = usePage().props;
    const user = auth.user;

    const roleNames = user?.roles?.map((r) => r.name) ?? [];




    const logout = () => {
        router.get(route("logout"));
    };

    return (
        <header className="bg-white">
            <style
                dangerouslySetInnerHTML={{
                    __html: `
      .cart-count {
        position: absolute;
        top: 5px;
        right: 0;
        background-color: green;
        color: white;
        font-size: 9px;
        font-weight: bold;
        border-radius: 50%;
        padding: 0 4px;
        transform: translate(50%, -50%);
      }

      .navbar-expand-lg .navbar-nav {
        flex-direction: row;
      }

      @media (max-width: 991px) {
        .cart-count {
          top: 5px;
          right: 0;
        }
      }
    `,
                }}
            />

            <Container>
                <nav className="flex items-center justify-between">
                    {/* LOGO */}
                    <Link href="/" className="flex items-center">
                        <img height="50" width="60" src="/icon.png" alt="" />
                        <div className="text-lg font-bold ps-2">
                            <ApplicationName />
                        </div>
                    </Link>

                    <div>
                        <ul className="flex items-center">
                            {/* HOME */}
                            <li>
                                <Link href={route("home")}>Home</Link>
                            </li>

                            {/* CART */}
                            <li className="px-2">
                                <div className="relative">
                                    <Link
                                        className="nav-link"
                                        href={route("carts.view")}
                                    >
                                        <i className="fas fa-shopping-cart"></i>
                                        <span className="cart-count">
                                            {auth.cartCount ?? "0"}
                                        </span>
                                    </Link>
                                </div>
                            </li>

                            {/* DROPDOWN */}
                            <Dropdown
                                align="right"
                                width="48"
                                trigger={
                                    <button className="inline-flex items-center px-3 py-2 mx-2 text-sm font-medium text-gray-500 transition border rounded hover:text-gray-700 hover:border-gray-300">
                                        {user?.name?.slice(0, 8) + "..."}
                                        <svg
                                            className="w-4 h-4 fill-current ms-1"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            />
                                        </svg>
                                    </button>
                                }
                            >
                                {/* Multiple Roles */}
                                {roleNames.length > 1 && (
                                    <DropdownLink href={route("dashboard")}>
                                        <i className="pr-2 fas fa-home"></i> Go
                                        To Dashboard
                                    </DropdownLink>
                                )}

                                {!active_nav && (
                                    <>
                                        <Hr />

                                        <DropdownLink
                                            href={route(
                                                "upgrade.vendor.create",
                                                { upgrade: "vendor" },
                                            )}
                                        >
                                            <i className="pr-2 fas fa-shop"></i>
                                            Open Vendor Shop
                                        </DropdownLink>

                                        <DropdownLink
                                            href={route(
                                                "upgrade.vendor.create",
                                                { upgrade: "reseller" },
                                            )}
                                        >
                                            <i className="pr-2 fas fa-shop"></i>
                                            Open Reseller Shop
                                        </DropdownLink>

                                        <DropdownLink
                                            href={route("upgrade.rider.create")}
                                        >
                                            <i className="pr-2 fas fa-truck-fast"></i>
                                            Request Rider
                                        </DropdownLink>
                                    </>
                                )}

                                {/* Admin / System */}
                                {(roles?.includes("system") ||
                                    roles?.includes("admin")) && (
                                    <>
                                        <Hr />
                                        <ResponsiveNavLink
                                            href={route("system.users.view")}
                                            active={route().current(
                                                "system.users.*",
                                            )}
                                        >
                                            Users Manage
                                        </ResponsiveNavLink>
                                    </>
                                )}

                                {/* Profile */}
                                <Hr />

                                <DropdownLink href={route("edit.profile")}>
                                    <i className="pr-2 fas fa-user"></i> Profile
                                </DropdownLink>

                                {/* Logout */}
                                <button
                                    onClick={logout}
                                    className="block w-full px-4 py-2 text-sm text-red-600 text-start hover:bg-gray-100"
                                >
                                    <i className="pr-2 fas fa-sign-out"></i> Log
                                    Out
                                </button>
                            </Dropdown>
                        </ul>
                    </div>
                </nav>
            </Container>
        </header>
    );
}
