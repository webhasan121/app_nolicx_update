import Hr from "../components/Hr";
import ResponsiveNavLink from "../components/ResponsiveNavLink";

export default function RiderResponsiveNavigation() {
    return (
        <>
            <ResponsiveNavLink
                href={route("rider.me")}
                active={route().current("rider.me")}
            >
                <i className="w-6 pr-2 fas fa-person-biking"></i> My Rider
            </ResponsiveNavLink>
            <Hr />

            <ResponsiveNavLink
                href={route("rider.consignment")}
                active={route().current("rider.consignment")}
            >
                <i className="w-6 pr-2 fas fa-truck-fast"></i> Consignments
            </ResponsiveNavLink>
        </>
    );
}
