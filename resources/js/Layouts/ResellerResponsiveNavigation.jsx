import { usePage } from "@inertiajs/react";
import Hr from "../components/Hr";
import ResponsiveNavLink from "../components/ResponsiveNavLink";
import useTranslation from "../hooks/useTranslation";

export default function ResellerResponsiveNavigation() {
    const { auth } = usePage().props;
    const user = auth?.user;
    const { t } = useTranslation();

    return (
        <>
            <ResponsiveNavLink
                href={route("my-shop", { user: user?.name ?? "" })}
                active={route().current("my-shop")}
            >
                <i className="w-6 pr-2 fas fa-shop"></i> {t("My Shop")}
            </ResponsiveNavLink>
            <hr />

            <ResponsiveNavLink
                href={route("reseller.products.list")}
                active={route().current("reseller.products.*")}
            >
                <i className="w-6 pr-2 fas fa-layer-group"></i> {t("Your Products")}
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("vendor.products.create")}
                active={route().current("vendor.products.*")}
            >
                <i className="w-6 pr-2 fas fa-plus"></i> {t("Add Products")}
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("reseller.resel-product.index")}
                active={route().current("reseller.resel-product.*")}
            >
                <i className="w-6 pr-2 fas fa-sync"></i> {t("Resel Product")}
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("shops")}
                active={route().current("shops")}
            >
                <i className="w-6 pr-2 fas fa-shop"></i> {t("Vendor Shop")}
            </ResponsiveNavLink>
            <hr />

            <ResponsiveNavLink
                href={route("vendor.orders.index")}
                active={route().current("vendor.orders.*")}
            >
                <i className="w-6 pr-2 fas fa-sort"></i> {t("Orders")}
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("reseller.sel.index")}
                active={route().current("reseller.sel.*")}
            >
                <i className="w-6 pr-2 fas fa-shopping-cart"></i> {t("Sel & Earn")}
            </ResponsiveNavLink>
        </>
    );
}
