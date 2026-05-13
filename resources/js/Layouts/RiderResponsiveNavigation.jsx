import Hr from "../components/Hr";
import ResponsiveNavLink from "../components/ResponsiveNavLink";
import useTranslation from "../hooks/useTranslation";

export default function RiderResponsiveNavigation() {
    const { t } = useTranslation();

    return (
        <>
            <ResponsiveNavLink
                href={route("rider.me")}
                active={route().current("rider.me")}
            >
                <i className="w-6 pr-2 fas fa-person-biking"></i> {t("My Rider")}
            </ResponsiveNavLink>
            <hr />

            <ResponsiveNavLink
                href={route("rider.consignment")}
                active={route().current("rider.consignment")}
            >
                <i className="w-6 pr-2 fas fa-truck-fast"></i> {t("Consignments")}
            </ResponsiveNavLink>
        </>
    );
}
