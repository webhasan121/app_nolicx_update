import { usePage } from "@inertiajs/react";
import Hr from "../components/Hr";
import ResponsiveNavLink from "../components/ResponsiveNavLink";

export default function VendorResponsiveNavigation() {
    const { auth } = usePage().props;
    const shopUser = auth?.shopSlug;

    return (
        <>
            <ResponsiveNavLink
                href={route("my-shop", { user: shopUser })}
                active={route().current("my-shop")}
            >
                <i className="w-6 pr-2 fas fa-shop"></i> My Shop
            </ResponsiveNavLink>
            <Hr />

            <ResponsiveNavLink
                href={route("vendor.products.view")}
                active={route().current("vendor.products.*")}
            >
                <i className="pr-2 fas fa-layer-group"></i>Products
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("vendor.products.create")}
                active={route().current("vendor.products.create")}
            >
                <i className="pr-2 fas fa-plus"></i>Products
            </ResponsiveNavLink>

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
