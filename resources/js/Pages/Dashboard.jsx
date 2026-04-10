import { usePage } from "@inertiajs/react";
import HasRole from "../components/HasRole";
import PageHeader from "../components/dashboard/PageHeader";
import VendorDashboard from "../layouts/vendor/Vendor";
import RiderConsignmentIndex from "../livewire/rider/consignment/Index";
import ResellerDashboard from "../livewire/reseller/Dashboard";
import SystemDashboardIndex from "../livewire/system/dashboard/Index";
import AppLayout from "../Layouts/App";

export default function Dashboard() {
    const {
        auth,
        resellerOverview,
        riderConsignmentIndex,
        systemOverview,
        vendorOrdersIndex,
        vendorOverview,
    } = usePage().props;
    const user = auth?.user;
    const roles = user?.roles?.map((role) => role.name) ?? [];
    const activeNav = user?.active_nav ?? "";

    const headingPrefix = [
        roles.includes("vendor") && activeNav === "vendor" ? "Vendor" : null,
        roles.includes("rider") && activeNav === "rider" ? "Rider" : null,
        roles.includes("admin") ? "Admin" : null,
        roles.includes("reseller") && activeNav === "reseller"
            ? "Reseller"
            : null,
    ]
        .filter(Boolean)
        .join(" ");

    return (
        <AppLayout
            title="Dashboard"
            header={
                <PageHeader>
                    {headingPrefix ? `${headingPrefix} ` : ""}
                    Dashboard
                </PageHeader>
            }
        >
                {(roles.includes("admin") || roles.includes("system")) && (
                    <SystemDashboardIndex
                        userName={user?.name}
                        adm={systemOverview?.adm}
                        vd={systemOverview?.vd}
                        avd={systemOverview?.avd}
                        rs={systemOverview?.rs}
                        ars={systemOverview?.ars}
                        ri={systemOverview?.ri}
                        ari={systemOverview?.ari}
                        userCount={systemOverview?.userCount}
                        vp={systemOverview?.vp}
                        cat={systemOverview?.cat}
                    />
                )}

                {activeNav === "vendor" && (
                    <HasRole name="vendor">
                        <VendorDashboard
                            vendorOverview={vendorOverview}
                            vendorOrdersIndex={vendorOrdersIndex}
                            activeNav={activeNav}
                        />
                    </HasRole>
                )}

                {activeNav === "reseller" && (
                    <HasRole name="reseller">
                        <ResellerDashboard
                            tp={resellerOverview?.tp}
                            vendor={resellerOverview?.vendor}
                            category={resellerOverview?.category}
                            products={resellerOverview?.products}
                            categories={resellerOverview?.categories}
                            vendorOrdersIndex={vendorOrdersIndex}
                            activeNav={activeNav}
                        />
                    </HasRole>
                )}

                {activeNav === "rider" && (
                    <HasRole name="rider">
                        <RiderConsignmentIndex
                            riderConsignmentIndex={riderConsignmentIndex}
                        />
                    </HasRole>
                )}
        </AppLayout>
    );
}
