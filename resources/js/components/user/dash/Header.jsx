import { Link, usePage, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import ApplicationName from "../../ApplicationName";
import Dropdown from "../../Dropdown";
import DropdownLink from "../../DropdownLink";
import ResponsiveNavLink from "../../ResponsiveNavLink";
import Hr from "../../Hr";
import VendorResponsiveNavigation from "../../../Layouts/VendorResponsiveNavigation";
import ResellerResponsiveNavigation from "../../../Layouts/ResellerResponsiveNavigation";
import RiderResponsiveNavigation from "../../../Layouts/RiderResponsiveNavigation";
import NoticeBell from "../../NoticeBell";
import LanguageSwitcher from "../../LanguageSwitcher";
import useTranslation from "../../../hooks/useTranslation";

export default function Header() {
    const { auth, roles, active_nav, permissions = [] } = usePage().props;
    const { t } = useTranslation();
    const user = auth.user;
    const [cartCount, setCartCount] = useState(auth?.cartCount ?? 0);

    const roleNames = user?.roles?.map((r) => r.name) ?? [];
    const displayName = user?.name
        ? user.name.length > 8
            ? `${user.name.slice(0, 8)}...`
            : user.name
        : "User";

    const permissionNames = Array.isArray(permissions)
        ? permissions
        : permissions?.map?.((p) => p.name) ?? [];

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

    const logout = () => {
        router.get(route("logout"));
    };

    return (
        <header className="bg-white border-b border-gray-100">
            <style
                dangerouslySetInnerHTML={{
                    __html: `
      .cart-count {
        position: absolute;
        top: 2px;
        right: 2px;
        min-width: 16px;
        height: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #16a34a;
        color: white;
        font-size: 9px;
        font-weight: bold;
        border-radius: 50%;
        padding: 0 3px;
        line-height: 1;
        transform: none;
        z-index: 2;
      }

      .navbar-expand-lg .navbar-nav {
        flex-direction: row;
      }

      @media (max-width: 991px) {
        .cart-count {
          top: 2px;
          right: 2px;
        }
      }
    `,
                }}
            />

            <div className="w-full px-3 mx-auto sm:px-4 lg:px-8">
                <nav className="flex h-16 items-center justify-between gap-2">
                    {/* LOGO */}
                    <Link href="/" className="flex items-center flex-1 min-w-0 sm:flex-none">
                        <img
                            height="50"
                            width="60"
                            src="/icon.png"
                            alt=""
                            className="w-10 h-auto shrink-0 sm:w-12 md:w-[60px]"
                        />
                        <div className="text-base font-bold leading-none truncate ps-1 sm:ps-2 sm:text-lg">
                            <ApplicationName />
                        </div>
                    </Link>

                    <div className="shrink-0">
                        <ul className="flex items-center gap-2 sm:gap-2.5">
                            <li className="hidden md:block">
                                <LanguageSwitcher compact />
                            </li>

                            {/* CART */}
                            <li>
                                <div className="relative">
                                    <Link
                                        className="relative inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50"
                                        href={route("carts.view")}
                                        aria-label={t("Cart")}
                                    >
                                        <i className="fas fa-shopping-cart"></i>
                                        <span className="cart-count">
                                            {cartCount}
                                        </span>
                                    </Link>
                                </div>
                            </li>

                            <li>
                                <NoticeBell role="user" />
                            </li>

                            {/* DROPDOWN */}
                            <Dropdown
                                align="right"
                                width="48"
                                trigger={
                                    <button className="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 bg-white px-0 text-sm font-medium leading-4 text-gray-500 shadow-sm transition duration-150 ease-in-out hover:bg-slate-50 hover:text-gray-700 focus:outline-none sm:w-auto sm:max-w-[124px] sm:justify-start sm:px-2">
                                        <span className="text-base sm:hidden">
                                            <i className="fas fa-user"></i>
                                        </span>
                                        <span className="hidden max-w-[72px] truncate sm:block">
                                            {displayName}
                                        </span>
                                        <svg
                                            className="hidden w-4 h-4 fill-current ms-1 sm:block"
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
                                        <i className="pr-2 fas fa-home"></i>
                                        {t("Go To Dashboard")}
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
                                            {t("Open Vendor Shop")}
                                        </DropdownLink>

                                        <DropdownLink
                                            href={route(
                                                "upgrade.vendor.create",
                                                { upgrade: "reseller" },
                                            )}
                                        >
                                            <i className="pr-2 fas fa-shop"></i>
                                            {t("Open Reseller Shop")}
                                        </DropdownLink>

                                        <DropdownLink
                                            href={route("upgrade.rider.create")}
                                        >
                                            <i className="pr-2 fas fa-truck-fast"></i>
                                            {t("Request Rider")}
                                        </DropdownLink>
                                    </>
                                )}

                                {/* Admin / System */}
                                {(roles?.includes("system") ||
                                    roles?.includes("admin")) && (
                                    <>
                                        <hr />
                                        <div className="py-2">
                                            {permissionNames.includes("users_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.users.view")}
                                                    active={route().current("system.users.*")}
                                                >
                                                    {t("Users Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("admin_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.admin")}
                                                    active={route().current("system.admin")}
                                                >
                                                    {t("Admin Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("vendors_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.vendor.index")}
                                                    active={route().current("system.vendor.*")}
                                                >
                                                    {t("Vendor Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("resellers_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.reseller.index")}
                                                    active={route().current("system.reseller.*")}
                                                >
                                                    {t("Reseller Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("riders_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.rider.index")}
                                                    active={route().current("system.rider.*")}
                                                >
                                                    {t("Rider Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("role_list") && (
                                                <ResponsiveNavLink
                                                    href={route("system.role.list")}
                                                    active={route().current("system.role.*")}
                                                >
                                                    {t("Role Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            <Hr />

                                            {permissionNames.includes("product_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.products.index")}
                                                    active={route().current("system.products.*")}
                                                >
                                                    {t("Products Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("category_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.categories.index")}
                                                    active={route().current("system.categories.*")}
                                                >
                                                    {t("Categories Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("vip_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.vip.users")}
                                                    active={route().current("system.vip.*")}
                                                >
                                                    {t("ViP Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("slider_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.slider.index")}
                                                    active={route().current("system.slider.*")}
                                                >
                                                    {t("Slider Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("store_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.store.index")}
                                                    active={route().current("system.store.*")}
                                                >
                                                    {t("StoreManage")}
                                                </ResponsiveNavLink>
                                            )}

                                            <Hr />

                                            {permissionNames.includes("deposit_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.deposit.index")}
                                                    active={route().current("system.deposit.*")}
                                                >
                                                    {t("Deposit Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("comission_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.comissions.index")}
                                                    active={route().current("system.comissions.*")}
                                                >
                                                    {t("Comission Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("order_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.orders.index")}
                                                    active={route().current("system.orders.*")}
                                                >
                                                    {t("Orders Manage")}
                                                </ResponsiveNavLink>
                                            )}

                                            {permissionNames.includes("withdraw_view") && (
                                                <ResponsiveNavLink
                                                    href={route("system.withdraw.index")}
                                                    active={route().current("*.withdraw.*")}
                                                >
                                                    {t("Withdraw Manage")}
                                                </ResponsiveNavLink>
                                            )}
                                        </div>
                                    </>
                                )}

                                {roleNames.includes("vendor") &&
                                    active_nav === "vendor" && (
                                        <>
                                           <hr />
                                            <VendorResponsiveNavigation />
                                          <hr />
                                        </>
                                    )}

                                {roleNames.includes("reseller") &&
                                    active_nav === "reseller" && (
                                        <>
                                            <hr />
                                            <ResellerResponsiveNavigation />
                                            <hr />
                                        </>
                                    )}

                                {roleNames.includes("rider") &&
                                    active_nav === "rider" && (
                                        <>
                                            <hr />
                                            <RiderResponsiveNavigation />
                                            <hr />
                                        </>
                                    )}

                                {/* Profile */}
                                <DropdownLink href={route("edit.profile")}>
                                    <i className="pr-2 fas fa-user"></i> {t("Profile")}
                                </DropdownLink>

                                {/* Logout */}
                                <button
                                    onClick={logout}
                                    className="block w-full px-4 py-2 text-sm text-red-600 text-start hover:bg-gray-100"
                                >
                                    <i className="pr-2 fas fa-sign-out"></i> {t("Log Out")}
                                </button>
                            </Dropdown>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
    );
}
