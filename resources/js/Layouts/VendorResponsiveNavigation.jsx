import { usePage } from "@inertiajs/react";
import Hr from "../components/Hr";
import ResponsiveNavLink from "../components/ResponsiveNavLink";
import useTranslation from "../hooks/useTranslation";

export default function VendorResponsiveNavigation() {
    const { auth } = usePage().props;
    const shopUser = auth?.shopSlug;
    const { t } = useTranslation();

    return (
        <>
            <ResponsiveNavLink
                href={route("my-shop", { user: shopUser })}
                active={route().current("my-shop")}
            >
                <i className="w-6 pr-2 fas fa-shop"></i> {t("My Shop")}
            </ResponsiveNavLink>
            <Hr />

            <ResponsiveNavLink
                href={route("vendor.products.view")}
                active={route().current("vendor.products.*")}
            >
                <i className="pr-2 fas fa-layer-group"></i>{t("Products")}
            </ResponsiveNavLink>

            <ResponsiveNavLink
                href={route("vendor.products.create")}
                active={route().current("vendor.products.create")}
            >
                <i className="pr-2 fas fa-plus"></i>{t("Add Products")}
            </ResponsiveNavLink>

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
