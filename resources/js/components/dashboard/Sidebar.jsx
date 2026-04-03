import { usePage } from "@inertiajs/react";
import ResponsiveNavigation from "../../layout/ResponsiveNavigation";
import ResponsiveNavLink from "../ResponsiveNavLink";

function Section({ children }) {
    if (!children) {
        return null;
    }

    return <div className="w-full pt-2 pb-3">{children}</div>;
}

export default function Sidebar() {
    const { auth, roles = [] } = usePage().props;
    const user = auth?.user;
    const roleNames = roles.length
        ? roles
        : user?.roles?.map((role) => role.name) ?? [];

    const isSystem = roleNames.includes("system") || roleNames.includes("admin");

    return (
        <aside className="hidden h-auto md:block md:w-[220px]">
            <Section>
                {isSystem && (
                    <>
                        <ResponsiveNavigation />
                    </>
                )}

                {user?.active_nav === "vendor" && (
                    <>
                        <ResponsiveNavLink
                            href={route("vendor.products.view")}
                            active={route().current("vendor.products.*")}
                        >
                            <i className="w-6 pr-2 fas fa-box-open"></i>
                            Products
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            href={route("vendor.products.create")}
                            active={route().current("vendor.products.create")}
                        >
                            <i className="w-6 pr-2 fas fa-plus"></i>
                            Add Product
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            href={route("vendor.orders.index")}
                            active={route().current("vendor.orders.*")}
                        >
                            <i className="w-6 pr-2 fas fa-bag-shopping"></i>
                            Orders
                        </ResponsiveNavLink>
                    </>
                )}

                {user?.active_nav === "reseller" && (
                    <>
                        <ResponsiveNavLink
                            href={route("reseller.products.list")}
                            active={route().current("reseller.products.*")}
                        >
                            <i className="w-6 pr-2 fas fa-boxes-stacked"></i>
                            Products
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            href={route("reseller.categories.list")}
                            active={route().current("reseller.categories.*")}
                        >
                            <i className="w-6 pr-2 fas fa-table-list"></i>
                            Categories
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            href={route("reseller.order.index")}
                            active={route().current("reseller.order.*")}
                        >
                            <i className="w-6 pr-2 fas fa-cart-shopping"></i>
                            Orders
                        </ResponsiveNavLink>
                    </>
                )}

                {user?.active_nav === "rider" && (
                    <>
                        <ResponsiveNavLink
                            href={route("rider.consignment")}
                            active={route().current("rider.consignment*")}
                        >
                            <i className="w-6 pr-2 fas fa-truck-fast"></i>
                            Consignments
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            href={route("rider.me")}
                            active={route().current("rider.me")}
                        >
                            <i className="w-6 pr-2 fas fa-id-card"></i>
                            Me
                        </ResponsiveNavLink>
                    </>
                )}
            </Section>
        </aside>
    );
}
