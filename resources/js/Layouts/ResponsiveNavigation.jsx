import { usePage } from "@inertiajs/react";
import Hr from "../components/Hr";
import ResponsiveNavLink from "../components/ResponsiveNavLink";

export default function ResponsiveNavigation() {
    const { auth, permissions = [] } = usePage().props;
    const user = auth?.user;
    const roleNames = auth?.roles?.length
        ? auth.roles
        : user?.roles?.map((role) => role.name) ?? [];
    const permissionNames = Array.isArray(permissions)
        ? permissions
              .map((permission) =>
                  typeof permission === "string"
                      ? permission
                      : permission?.name
                )
              .filter(Boolean)
        : [];

    const isSystem = roleNames.includes("admin") || roleNames.includes("system");
    const can = (p) => permissionNames.includes(p);

    if (!isSystem) {
        return null;
    }

    return (
        <>
            {can("users_view") && (
                <ResponsiveNavLink
                    href={route("system.users.view")}
                    active={route().current("system.users.*")}
                >
                    <i className="w-6 pr-2 fas fa-users"></i> Users
                </ResponsiveNavLink>
            )}

            {can("admin_view") && (
                <ResponsiveNavLink
                    href={route("system.admin")}
                    active={route().current("system.admin")}
                >
                    <i className="w-6 pr-2 fas fa-user-lock"></i> Admin
                </ResponsiveNavLink>
            )}

            {can("vendors_view") && (
                <ResponsiveNavLink
                    href={route("system.vendor.index")}
                    active={route().current("system.vendor.*")}
                >
                    <i className="w-6 pr-2 fas fa-shop"></i> Vendor
                </ResponsiveNavLink>
            )}

            {can("resellers_view") && (
                <ResponsiveNavLink
                    href={route("system.reseller.index")}
                    active={route().current("system.reseller.*")}
                >
                    <i className="w-6 pr-2 fas fa-shop"></i> Reseller
                </ResponsiveNavLink>
            )}

            {can("riders_view") && (
                <ResponsiveNavLink
                    href={route("system.rider.index")}
                    active={route().current("system.rider.*")}
                >
                    <i className="w-6 pr-2 fas fa-truck-fast"></i> Rider
                </ResponsiveNavLink>
            )}

            {can("role_list") && (
                <ResponsiveNavLink
                    href={route("system.role.list")}
                    active={route().current("system.role.*")}
                >
                    <i className="w-6 pr-2 fas fa-user-shield"></i> Role
                </ResponsiveNavLink>
            )}

            <ResponsiveNavLink
                href={route("system.consignment.index")}
                active={route().current("system.consignment.*")}
            >
                <i className="w-6 pr-2 fas fa-truck-fast"></i> Consignment
            </ResponsiveNavLink>

            <Hr />

            {can("product_view") && (
                <ResponsiveNavLink
                    href={route("system.products.index")}
                    active={route().current("system.products.*")}
                >
                    <i className="w-6 pr-2 fas fa-layer-group"></i> Products
                </ResponsiveNavLink>
            )}

            {can("category_view") && (
                <ResponsiveNavLink
                    href={route("system.categories.index")}
                    active={route().current("system.categories.*")}
                >
                    <i className="w-6 pr-2 fas fa-table"></i> Categories
                </ResponsiveNavLink>
            )}

            {can("order_view") && (
                <ResponsiveNavLink
                    href={route("system.orders.index")}
                    active={route().current("system.orders.*")}
                >
                    <i className="w-6 pr-2 fas fa-cart-plus"></i> Orders
                </ResponsiveNavLink>
            )}

            {can("vip_view") && (
                <>
                    <ResponsiveNavLink
                        href={route("system.vip.index")}
                        active={
                            route().current("system.vip.index") ||
                            route().current("system.vip.crate") ||
                            route().current("system.package.edit") ||
                            route().current("system.package.update")
                        }
                    >
                        <i className="w-6 pr-2 fas fa-box-open"></i> ViP Package
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        href={route("system.vip.users")}
                        active={
                            route().current("system.vip.users") ||
                            route().current("system.vip.edit") ||
                            route().current("system.vip.print-summery")
                        }
                    >
                        <i className="w-6 pr-2 fas fa-user-tie"></i> ViP Users
                    </ResponsiveNavLink>
                </>
            )}

            {can("slider_view") && (
                <>
                    <ResponsiveNavLink
                        href={route("system.slider.index")}
                        active={route().current("system.slider.*")}
                    >
                        <i className="w-6 pr-2 fas fa-photo-film"></i> Carousel
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        href={route("system.static-slider.index")}
                        active={route().current("system.static-slider.*")}
                    >
                        <i className="w-6 pr-2 fas fa-photo-film"></i> Static Slider
                    </ResponsiveNavLink>
                </>
            )}

            <Hr />

            <ResponsiveNavLink
                href={route("system.settings.index")}
                active={route().current("system.settings.*")}
            >
                <i className="w-6 pr-2 fas fa-gear"></i> Settings
            </ResponsiveNavLink>

            <Hr />

            <ResponsiveNavLink
                href={route("system.levels.index")}
                active={route().current("system.levels.index")}
            >
                <i className="w-6 pr-2 fas fa-star"></i> Star System
            </ResponsiveNavLink>
            <ResponsiveNavLink
                href={route("system.levels.history")}
                active={route().current("system.levels.history")}
            >
                <i className="w-6 pr-2 fas fa-star"></i> Level-up History
            </ResponsiveNavLink>


            <Hr />

            <ResponsiveNavLink
                href={route("system.partnership.developer")}
                active={route().current("system.partnership.developer*")}
            >
                <i className="w-6 pr-2 fas fa-handshake"></i> Developer
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("system.partnership.management")}
                active={route().current("system.partnership.management*")}
            >
                <i className="w-6 pr-2 fas fa-handshake"></i> Management
            </ResponsiveNavLink>

            <Hr />

            {can("store_view") && (
                <ResponsiveNavLink
                    href={route("system.store.index")}
                    active={route().current("system.store.*")}
                >
                    <i className="w-6 pr-2 fas fa-store"></i> Store
                </ResponsiveNavLink>
            )}

            <Hr />

            {can("deposit_view") && (
                <ResponsiveNavLink
                    href={route("system.deposit.index")}
                    active={route().current("system.deposit.*")}
                >
                    <i className="w-6 pr-2 fas fa-sign-in"></i> Deposit
                </ResponsiveNavLink>
            )}

            {can("comission_view") && (
                <>
                    <ResponsiveNavLink
                        href={route("system.earn.index")}
                        active={route().current("system.earn.*")}
                    >
                        <i className="w-6 pr-2 fas fa-money-bill"></i> Sell
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        href={route("system.comissions.index")}
                        active={route().current("system.comissions.*")}
                    >
                        <i className="w-6 pr-2 fas fa-money-bill-transfer"></i> Comission
                    </ResponsiveNavLink>
                </>
            )}

            {can("withdraw_view") && (
                <ResponsiveNavLink
                    href={route("system.withdraw.index")}
                    active={route().current("*.withdraw.*")}
                >
                    <i className="w-6 pr-2 fas fa-arrow-up-from-bracket"></i> Withdraw
                </ResponsiveNavLink>
            )}
        </>
    );
}
