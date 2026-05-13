import { usePage } from "@inertiajs/react";
import Hr from "../components/Hr";
import ResponsiveNavLink from "../components/ResponsiveNavLink";
import useTranslation from "../hooks/useTranslation";

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
    const { t } = useTranslation();

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
                    <i className="w-6 pr-2 fas fa-users"></i> {t("Users")}
                </ResponsiveNavLink>
            )}

            {can("admin_view") && (
                <ResponsiveNavLink
                    href={route("system.admin")}
                    active={route().current("system.admin")}
                >
                    <i className="w-6 pr-2 fas fa-user-lock"></i> {t("Admin")}
                </ResponsiveNavLink>
            )}

            {can("vendors_view") && (
                <ResponsiveNavLink
                    href={route("system.vendor.index")}
                    active={route().current("system.vendor.*")}
                >
                    <i className="w-6 pr-2 fas fa-shop"></i> {t("Vendor")}
                </ResponsiveNavLink>
            )}

            {can("resellers_view") && (
                <ResponsiveNavLink
                    href={route("system.reseller.index")}
                    active={route().current("system.reseller.*")}
                >
                    <i className="w-6 pr-2 fas fa-shop"></i> {t("Reseller")}
                </ResponsiveNavLink>
            )}

            {can("riders_view") && (
                <ResponsiveNavLink
                    href={route("system.rider.index")}
                    active={route().current("system.rider.*")}
                >
                    <i className="w-6 pr-2 fas fa-truck-fast"></i> {t("Rider")}
                </ResponsiveNavLink>
            )}

            {can("role_list") && (
                <ResponsiveNavLink
                    href={route("system.role.list")}
                    active={route().current("system.role.*")}
                >
                    <i className="w-6 pr-2 fas fa-user-shield"></i> {t("Role")}
                </ResponsiveNavLink>
            )}

            <ResponsiveNavLink
                href={route("system.consignment.index")}
                active={route().current("system.consignment.*")}
            >
                <i className="w-6 pr-2 fas fa-truck-fast"></i> {t("Consignment")}
            </ResponsiveNavLink>

            <Hr />

            {can("product_view") && (
                <ResponsiveNavLink
                    href={route("system.products.index")}
                    active={route().current("system.products.*")}
                >
                    <i className="w-6 pr-2 fas fa-layer-group"></i> {t("Products")}
                </ResponsiveNavLink>
            )}

            {can("category_view") && (
                <ResponsiveNavLink
                    href={route("system.categories.index")}
                    active={route().current("system.categories.*")}
                >
                    <i className="w-6 pr-2 fas fa-table"></i> {t("Categories")}
                </ResponsiveNavLink>
            )}

            {can("order_view") && (
                <ResponsiveNavLink
                    href={route("system.orders.index")}
                    active={route().current("system.orders.*")}
                >
                    <i className="w-6 pr-2 fas fa-cart-plus"></i> {t("Orders")}
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
                        <i className="w-6 pr-2 fas fa-box-open"></i> {t("ViP Package")}
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        href={route("system.vip.users")}
                        active={
                            route().current("system.vip.users") ||
                            route().current("system.vip.edit") ||
                            route().current("system.vip.print-summery")
                        }
                    >
                        <i className="w-6 pr-2 fas fa-user-tie"></i> {t("ViP Users")}
                    </ResponsiveNavLink>
                </>
            )}

            {can("slider_view") && (
                <>
                    <ResponsiveNavLink
                        href={route("system.slider.index")}
                        active={route().current("system.slider.*")}
                    >
                        <i className="w-6 pr-2 fas fa-photo-film"></i> {t("Carousel")}
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        href={route("system.static-slider.index")}
                        active={route().current("system.static-slider.*")}
                    >
                        <i className="w-6 pr-2 fas fa-photo-film"></i> {t("Static Slider")}
                    </ResponsiveNavLink>
                </>
            )}

            <Hr />

            <ResponsiveNavLink
                href={route("system.settings.index")}
                active={route().current("system.settings.*")}
            >
                <i className="w-6 pr-2 fas fa-gear"></i> {t("Settings")}
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("system.languages.index")}
                active={route().current("system.languages.*")}
            >
                <i className="w-6 pr-2 fas fa-language"></i> {t("Translations")}
            </ResponsiveNavLink>

            <Hr />

            <ResponsiveNavLink
                href={route("system.levels.index")}
                active={route().current("system.levels.index")}
            >
                <i className="w-6 pr-2 fas fa-star"></i> {t("Star System")}
            </ResponsiveNavLink>
            <ResponsiveNavLink
                href={route("system.levels.history")}
                active={route().current("system.levels.history")}
            >
                <i className="w-6 pr-2 fas fa-star"></i> {t("Level-up History")}
            </ResponsiveNavLink>


            <Hr />

            <ResponsiveNavLink
                href={route("system.partnership.developer")}
                active={route().current("system.partnership.developer*")}
            >
                <i className="w-6 pr-2 fas fa-handshake"></i> {t("Developer")}
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("system.partnership.management")}
                active={
                    route().current("system.partnership.management") ||
                    route().current("system.partnership.management.*")
                }
            >
                <i className="w-6 pr-2 fas fa-handshake"></i> {t("Management")}
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("system.partnership.management-team")}
                active={
                    route().current("system.partnership.management-team") ||
                    route().current("system.partnership.management-team.*")
                }
            >
                <i className="w-6 pr-2 fas fa-users-cog"></i> {t("Management TM")}
            </ResponsiveNavLink>

            <Hr />

            {can("store_view") && (
                <ResponsiveNavLink
                    href={route("system.store.index")}
                    active={route().current("system.store.*")}
                >
                    <i className="w-6 pr-2 fas fa-store"></i> {t("Store")}
                </ResponsiveNavLink>
            )}

            <Hr />

            {can("deposit_view") && (
                <ResponsiveNavLink
                    href={route("system.deposit.index")}
                    active={route().current("system.deposit.*")}
                >
                    <i className="w-6 pr-2 fas fa-sign-in"></i> {t("Deposit")}
                </ResponsiveNavLink>
            )}

            {can("comission_view") && (
                <>
                    <ResponsiveNavLink
                        href={route("system.earn.index")}
                        active={route().current("system.earn.*")}
                    >
                        <i className="w-6 pr-2 fas fa-money-bill"></i> {t("Sell")}
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        href={route("system.comissions.index")}
                        active={route().current("system.comissions.*")}
                    >
                        <i className="w-6 pr-2 fas fa-money-bill-transfer"></i> {t("Comission")}
                    </ResponsiveNavLink>
                </>
            )}

            {can("withdraw_view") && (
                <ResponsiveNavLink
                    href={route("system.withdraw.index")}
                    active={route().current("*.withdraw.*")}
                >
                    <i className="w-6 pr-2 fas fa-arrow-up-from-bracket"></i> {t("Withdraw")}
                </ResponsiveNavLink>
            )}
        </>
    );
}
