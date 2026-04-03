import { usePage } from "@inertiajs/react";
import Hr from "../components/Hr";
import ResponsiveNavLink from "../components/ResponsiveNavLink";

export default function ResellerResponsiveNavigation() {
    const { auth } = usePage().props;
    const user = auth?.user;

    return (
        <>
            <ResponsiveNavLink
                href={route("my-shop", { user: user?.name ?? "" })}
                active={route().current("my-shop")}
            >
                <i className="w-6 pr-2 fas fa-shop"></i> My Shop
            </ResponsiveNavLink>
            <Hr />

            <ResponsiveNavLink
                href={route("reseller.products.list")}
                active={route().current("reseller.products.*")}
            >
                <i className="w-6 pr-2 fas fa-layer-group"></i> Your Products
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("vendor.products.create")}
                active={route().current("vendor.products.*")}
            >
                <i className="w-6 pr-2 fas fa-plus"></i> Add Products
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("reseller.resel-product.index")}
                active={route().current("reseller.resel-product.*")}
            >
                <i className="w-6 pr-2 fas fa-sync"></i> Resel Product
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("shops")}
                active={route().current("shops")}
            >
                <i className="w-6 pr-2 fas fa-shop"></i> Vendor Shop
            </ResponsiveNavLink>
            <Hr />

            <ResponsiveNavLink
                href={route("vendor.orders.index")}
                active={route().current("vendor.orders.*")}
            >
                <i className="w-6 pr-2 fas fa-sort"></i> Orders
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("reseller.sel.index")}
                active={route().current("reseller.sel.*")}
            >
                <i className="w-6 pr-2 fas fa-shopping-cart"></i> Sel & Earn
            </ResponsiveNavLink>
        </>
    );
}
